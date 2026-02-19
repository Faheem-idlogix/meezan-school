<?php

namespace App\Http\Controllers;

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
            'total'  => WhatsappLog::count(),
            'sent'   => WhatsappLog::where('status', 'sent')->count(),
            'failed' => WhatsappLog::where('status', 'failed')->count(),
        ];

        // ── Active notices for broadcast dropdown ──
        $notices = Notice::where('is_active', 1)->latest()->get();

        return view('admin.pages.whatsapp.index', compact(
            'tab', 'settings', 'students', 'teachers',
            'studentTotal', 'studentWhatsapp', 'teacherTotal', 'teacherWhatsapp',
            'logs', 'logStats', 'notices'
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

    // ── Send single test message ─────────────────────────────────────
    public function sendTest(Request $request)
    {
        $request->validate([
            'to'      => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        $result = $this->dispatchWhatsApp($request->to, $request->message, 'test');

        return response()->json($result);
    }

    // ── Send message to selected recipients ──────────────────────────
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

        $sent = 0;
        $failed = 0;

        foreach ($recipients as $recipient) {
            $result = $this->dispatchWhatsApp(
                $recipient->whatsapp_number,
                $request->message,
                $request->message_type ?? 'manual',
                $request->recipient_type,
                $recipient->id,
                $recipient->{$nameField}
            );
            if ($result['success']) $sent++;
            else $failed++;
        }

        return redirect()->route('whatsapp.index', ['tab' => 'compose'])
                         ->with('success', "Messages sent: {$sent} success, {$failed} failed.");
    }

    // ── Broadcast a notice to all students with WhatsApp numbers ─────
    public function broadcastNotice(Request $request)
    {
        $request->validate([
            'notice_id' => 'required|exists:notices,id',
        ]);

        $notice  = Notice::findOrFail($request->notice_id);
        $students = Student::whereNotNull('whatsapp_number')->get();

        $sent = 0;
        foreach ($students as $student) {
            $msg = "📢 *{$notice->title}*\n\n{$notice->content}\n\n— Meezan School";
            $result = $this->dispatchWhatsApp(
                $student->whatsapp_number, $msg, 'notice',
                'student', $student->id, $student->student_name
            );
            if ($result['success']) $sent++;
        }

        return redirect()->route('whatsapp.index', ['tab' => 'compose'])
                         ->with('success', "Notice broadcast to {$sent} of {$students->count()} contacts.");
    }

    // ── Clear all logs ──────────────────────────────────────────────
    public function clearLogs()
    {
        WhatsappLog::truncate();
        return redirect()->route('whatsapp.index', ['tab' => 'logs'])
                         ->with('success', 'All WhatsApp logs cleared.');
    }

    // ═══════════════════════════════════════════════════════════════
    // PRIVATE: dispatch and log
    // ═══════════════════════════════════════════════════════════════
    private function dispatchWhatsApp(
        string $to,
        string $message,
        string $messageType = 'manual',
        ?string $recipientType = null,
        ?int $recipientId = null,
        ?string $recipientName = null
    ): array {
        $provider = Setting::get('whatsapp_provider', 'ultramsg');
        $apiKey   = Setting::get('whatsapp_api_key');
        $from     = Setting::get('whatsapp_from');
        $instance = Setting::get('whatsapp_instance');

        if (!$apiKey) {
            WhatsappLog::create([
                'school_id'      => 1,
                'to'             => $to,
                'recipient_name' => $recipientName,
                'recipient_type' => $recipientType,
                'recipient_id'   => $recipientId,
                'message_type'   => $messageType,
                'message'        => $message,
                'status'         => 'failed',
                'api_response'   => 'API key not configured',
                'provider'       => $provider,
            ]);
            return ['success' => false, 'error' => 'WhatsApp API key not configured'];
        }

        try {
            if ($provider === 'ultramsg') {
                $response = Http::asForm()->post("https://api.ultramsg.com/{$instance}/messages/chat", [
                    'token' => $apiKey,
                    'to'    => $to,
                    'body'  => $message,
                ]);
            } elseif ($provider === 'twilio') {
                $response = Http::withBasicAuth($from, $apiKey)
                    ->asForm()
                    ->post("https://api.twilio.com/2010-04-01/Accounts/{$from}/Messages.json", [
                        'From' => "whatsapp:{$from}",
                        'To'   => "whatsapp:{$to}",
                        'Body' => $message,
                    ]);
            } else {
                $response = Http::withHeaders(['Authorization' => "Bearer {$apiKey}"])
                    ->post("https://live-server-{$instance}.wati.io/api/v1/sendSessionMessage/{$to}", [
                        'messageText' => $message,
                    ]);
            }

            $success = $response->successful();

            WhatsappLog::create([
                'school_id'      => 1,
                'to'             => $to,
                'recipient_name' => $recipientName,
                'recipient_type' => $recipientType,
                'recipient_id'   => $recipientId,
                'message_type'   => $messageType,
                'message'        => $message,
                'status'         => $success ? 'sent' : 'failed',
                'api_response'   => json_encode($response->json()),
                'provider'       => $provider,
            ]);

            return ['success' => $success, 'response' => $response->json()];
        } catch (\Exception $e) {
            WhatsappLog::create([
                'school_id'      => 1,
                'to'             => $to,
                'recipient_name' => $recipientName,
                'recipient_type' => $recipientType,
                'recipient_id'   => $recipientId,
                'message_type'   => $messageType,
                'message'        => $message,
                'status'         => 'failed',
                'api_response'   => $e->getMessage(),
                'provider'       => $provider,
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
