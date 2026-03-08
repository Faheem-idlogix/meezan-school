<?php

namespace App\Http\Controllers;

use App\Jobs\SendWhatsAppMessage;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\WhatsappLog;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    // ── Main WhatsApp Hub page ──────────────────────────────────────
    public function index(Request $request)
    {
        $tab = $request->tab ?? 'contacts';

        // ── WhatsApp config ──
        $settings = Setting::all()->keyBy('key');

        // ── Student contacts with whatsapp_number presence ──
        $students = Student::select('id', 'student_name', 'contact_no', 'whatsapp_number', 'class_room_id')
            ->with('classroom:id,class_name')
            ->orderBy('student_name')
            ->get()
            ->map(function ($s) {
                $s->has_whatsapp = !empty($s->whatsapp_number);
                $s->display_phone = $s->whatsapp_number ?: $s->contact_no ?: '—';
                return $s;
            });

        // ── Teacher contacts with whatsapp_number presence ──
        $teachers = Teacher::select('id', 'teacher_name', 'contact_no', 'whatsapp_number')
            ->orderBy('teacher_name')
            ->get()
            ->map(function ($t) {
                $t->has_whatsapp = !empty($t->whatsapp_number);
                $t->display_phone = $t->whatsapp_number ?: $t->contact_no ?: '—';
                return $t;
            });

        // ── Stats ──
        $studentTotal    = $students->count();
        $studentWhatsapp = $students->where('has_whatsapp', true)->count();
        $teacherTotal    = $teachers->count();
        $teacherWhatsapp = $teachers->where('has_whatsapp', true)->count();

        // ── Message logs ──
        $logs = WhatsappLog::orderByDesc('created_at')->paginate(25)->withQueryString();
        $logStats = [
            'total'   => WhatsappLog::count(),
            'sent'    => WhatsappLog::where('status', 'sent')->count(),
            'failed'  => WhatsappLog::where('status', 'failed')->count(),
            'queued'  => WhatsappLog::where('status', 'queued')->count(),
        ];

        // ── Rate limit usage stats ──
        $usage = $this->getUsageStats();

        // ── Active notices for broadcast dropdown ──
        $notices = Notice::where('is_active', 1)->latest()->get();

        // ── Pending jobs in queue ──
        $pendingJobs = \DB::table('jobs')->where('queue', 'whatsapp')->count();

        return view('admin.pages.whatsapp.index', compact(
            'tab', 'settings', 'students', 'teachers',
            'studentTotal', 'studentWhatsapp', 'teacherTotal', 'teacherWhatsapp',
            'logs', 'logStats', 'notices', 'usage', 'pendingJobs'
        ));
    }

    // ── Sync/update whatsapp numbers ──────────────────────────────────
    public function syncNumbers(Request $request)
    {
        $request->validate([
            'type'    => 'required|in:student,teacher',
            'numbers' => 'required|array',
            'numbers.*.id' => 'required|integer',
            'numbers.*.whatsapp_number' => 'nullable|string|max:20',
        ]);

        $model = $request->type === 'student' ? Student::class : Teacher::class;

        foreach ($request->numbers as $entry) {
            $model::where('id', $entry['id'])->update([
                'whatsapp_number' => $entry['whatsapp_number'] ?: null,
            ]);
        }

        return redirect()->route('whatsapp.index', ['tab' => 'contacts'])
                         ->with('success', ucfirst($request->type) . ' WhatsApp numbers synced successfully.');
    }

    // ── Send single test message (synchronous — single msg, no queue needed) ──
    public function sendTest(Request $request)
    {
        $request->validate([
            'to'      => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        // Check limits even for test
        $usage = $this->getUsageStats();
        if ($usage['daily_remaining'] <= 0) {
            return response()->json(['success' => false, 'error' => "Daily limit reached ({$usage['daily_limit']}/day). Try tomorrow."]);
        }
        if ($usage['monthly_remaining'] <= 0) {
            return response()->json(['success' => false, 'error' => "Monthly limit reached ({$usage['monthly_limit']}/month)."]);
        }

        $result = $this->sendDirectWhatsApp($request->to, $request->message, 'test');

        return response()->json($result);
    }

    // ── Send message to selected recipients (QUEUED) ─────────────────
    public function sendBulk(Request $request)
    {
        $request->validate([
            'recipient_type' => 'required|in:student,teacher',
            'recipient_ids'  => 'required|array|min:1',
            'recipient_ids.*'=> 'integer',
            'message'        => 'required|string|max:2000',
            'message_type'   => 'nullable|string|max:50',
        ]);

        $model = $request->recipient_type === 'student' ? Student::class : Teacher::class;
        $nameField = $request->recipient_type === 'student' ? 'student_name' : 'teacher_name';

        $recipients = $model::whereIn('id', $request->recipient_ids)
            ->whereNotNull('whatsapp_number')
            ->get();

        // Pre-check: do we have enough room in the limits?
        $usage     = $this->getUsageStats();
        $count     = $recipients->count();

        if ($count === 0) {
            return redirect()->route('whatsapp.index', ['tab' => 'compose'])
                             ->with('error', 'No recipients with WhatsApp numbers found.');
        }
        if ($usage['daily_remaining'] <= 0) {
            return redirect()->route('whatsapp.index', ['tab' => 'compose'])
                             ->with('error', "Daily limit reached ({$usage['daily_limit']}/day). Try again tomorrow.");
        }
        if ($usage['monthly_remaining'] <= 0) {
            return redirect()->route('whatsapp.index', ['tab' => 'compose'])
                             ->with('error', "Monthly limit reached ({$usage['monthly_limit']}/month).");
        }

        // Only queue up to the remaining daily allowance
        $canSend   = min($count, $usage['daily_remaining'], $usage['monthly_remaining']);
        $delay     = (int) Setting::get('wa_delay_seconds', 8);

        $queued = 0;
        foreach ($recipients->take($canSend) as $i => $recipient) {
            // Log as queued immediately
            $log = WhatsappLog::create([
                'school_id'      => 1,
                'to'             => $recipient->whatsapp_number,
                'recipient_name' => $recipient->{$nameField},
                'recipient_type' => $request->recipient_type,
                'recipient_id'   => $recipient->id,
                'message_type'   => $request->message_type ?? 'manual',
                'message'        => $request->message,
                'status'         => 'queued',
                'api_response'   => "Queued #" . ($i + 1) . " — delay {$i}×{$delay}s",
                'provider'       => Setting::get('whatsapp_provider', 'ultramsg'),
            ]);

            SendWhatsAppMessage::dispatch(
                $recipient->whatsapp_number,
                $request->message,
                $request->message_type ?? 'manual',
                $request->recipient_type,
                $recipient->id,
                $recipient->{$nameField},
                $log->id
            )->onQueue('whatsapp')->delay(now()->addSeconds($i * $delay));

            $queued++;
        }

        $skipped = $count - $canSend;
        $msg = "{$queued} messages queued (sending 1 every {$delay}s).";
        if ($skipped > 0) {
            $msg .= " {$skipped} skipped due to daily/monthly limit.";
        }

        return redirect()->route('whatsapp.index', ['tab' => 'compose'])
                         ->with('success', $msg);
    }

    // ── Broadcast a notice to all students with WhatsApp numbers (QUEUED) ──
    public function broadcastNotice(Request $request)
    {
        $request->validate([
            'notice_id' => 'required|exists:notices,id',
        ]);

        $notice   = Notice::findOrFail($request->notice_id);
        $students = Student::whereNotNull('whatsapp_number')->get();
        $usage    = $this->getUsageStats();

        if ($usage['daily_remaining'] <= 0) {
            return redirect()->route('whatsapp.index', ['tab' => 'compose'])
                             ->with('error', "Daily limit reached ({$usage['daily_limit']}/day). Try again tomorrow.");
        }

        $canSend = min($students->count(), $usage['daily_remaining'], $usage['monthly_remaining']);
        $delay   = (int) Setting::get('wa_delay_seconds', 8);
        $queued  = 0;

        foreach ($students->take($canSend) as $i => $student) {
            $msg = "📢 *{$notice->title}*\n\n{$notice->content}\n\n— Meezan School";

            $log = WhatsappLog::create([
                'school_id'      => 1,
                'to'             => $student->whatsapp_number,
                'recipient_name' => $student->student_name,
                'recipient_type' => 'student',
                'recipient_id'   => $student->id,
                'message_type'   => 'notice',
                'message'        => $msg,
                'status'         => 'queued',
                'api_response'   => "Queued #" . ($i + 1) . " — delay {$i}×{$delay}s",
                'provider'       => Setting::get('whatsapp_provider', 'ultramsg'),
            ]);

            SendWhatsAppMessage::dispatch(
                $student->whatsapp_number, $msg, 'notice',
                'student', $student->id, $student->student_name, $log->id
            )->onQueue('whatsapp')->delay(now()->addSeconds($i * $delay));

            $queued++;
        }

        $skipped = $students->count() - $canSend;
        $statusMsg = "Notice queued for {$queued} of {$students->count()} contacts (1 every {$delay}s).";
        if ($skipped > 0) {
            $statusMsg .= " {$skipped} skipped due to limits.";
        }

        return redirect()->route('whatsapp.index', ['tab' => 'compose'])
                         ->with('success', $statusMsg);
    }

    // ── Clear all logs ──────────────────────────────────────────────
    public function clearLogs()
    {
        WhatsappLog::truncate();
        return redirect()->route('whatsapp.index', ['tab' => 'logs'])
                         ->with('success', 'All WhatsApp logs cleared.');
    }

    // ═══════════════════════════════════════════════════════════════
    // PRIVATE: Usage stats for rate limiting
    // ═══════════════════════════════════════════════════════════════
    private function getUsageStats(): array
    {
        $dailyLimit   = (int) Setting::get('wa_daily_limit', 200);
        $monthlyLimit = (int) Setting::get('wa_monthly_limit', 3000);

        $todaySent = WhatsappLog::whereIn('status', ['sent', 'queued'])
            ->whereDate('created_at', today())
            ->count();

        $monthSent = WhatsappLog::whereIn('status', ['sent', 'queued'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            'daily_limit'       => $dailyLimit,
            'monthly_limit'     => $monthlyLimit,
            'today_sent'        => $todaySent,
            'month_sent'        => $monthSent,
            'daily_remaining'   => max(0, $dailyLimit - $todaySent),
            'monthly_remaining' => max(0, $monthlyLimit - $monthSent),
        ];
    }

    // ═══════════════════════════════════════════════════════════════
    // PRIVATE: Direct send for test messages only (not queued)
    // ═══════════════════════════════════════════════════════════════
    private function sendDirectWhatsApp(string $to, string $message, string $messageType = 'test'): array
    {
        $provider = Setting::get('whatsapp_provider', 'ultramsg');
        $apiKey   = Setting::get('whatsapp_api_key');
        $from     = Setting::get('whatsapp_from');
        $instance = Setting::get('whatsapp_instance');

        if (!$apiKey) {
            WhatsappLog::create([
                'school_id' => 1, 'to' => $to, 'message_type' => $messageType,
                'message' => $message, 'status' => 'failed',
                'api_response' => 'API key not configured', 'provider' => $provider,
            ]);
            return ['success' => false, 'error' => 'WhatsApp API key not configured'];
        }

        try {
            if ($provider === 'cloud_api') {
                $phoneNumberId = $instance;
                $response = Http::withToken($apiKey)
                    ->post("https://graph.facebook.com/v21.0/{$phoneNumberId}/messages", [
                        'messaging_product' => 'whatsapp',
                        'to'   => $to,
                        'type' => 'text',
                        'text' => ['body' => $message],
                    ]);
            } elseif ($provider === 'ultramsg') {
                $response = Http::asForm()->post("https://api.ultramsg.com/{$instance}/messages/chat", [
                    'token' => $apiKey, 'to' => $to, 'body' => $message,
                ]);
            } elseif ($provider === 'twilio') {
                $response = Http::withBasicAuth($from, $apiKey)->asForm()
                    ->post("https://api.twilio.com/2010-04-01/Accounts/{$from}/Messages.json", [
                        'From' => "whatsapp:{$from}", 'To' => "whatsapp:{$to}", 'Body' => $message,
                    ]);
            } else {
                $response = Http::withHeaders(['Authorization' => "Bearer {$apiKey}"])
                    ->post("https://live-server-{$instance}.wati.io/api/v1/sendSessionMessage/{$to}", [
                        'messageText' => $message,
                    ]);
            }

            $success = $response->successful();
            WhatsappLog::create([
                'school_id' => 1, 'to' => $to, 'message_type' => $messageType,
                'message' => $message, 'status' => $success ? 'sent' : 'failed',
                'api_response' => json_encode($response->json()), 'provider' => $provider,
            ]);
            return ['success' => $success, 'response' => $response->json()];
        } catch (\Exception $e) {
            WhatsappLog::create([
                'school_id' => 1, 'to' => $to, 'message_type' => $messageType,
                'message' => $message, 'status' => 'failed',
                'api_response' => $e->getMessage(), 'provider' => $provider,
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
