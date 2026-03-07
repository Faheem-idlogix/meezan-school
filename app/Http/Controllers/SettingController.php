<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Student;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('admin.pages.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'school_name'       => 'nullable|string|max:255',
            'school_phone'      => 'nullable|string|max:30',
            'school_address'    => 'nullable|string|max:500',
            'school_email'      => 'nullable|email|max:255',
            'school_tagline'    => 'nullable|string|max:255',
            'school_logo'       => 'nullable|image|mimes:jpg,jpeg,png,ico,svg,webp|max:2048',
            'whatsapp_provider' => 'nullable|in:twilio,wati,ultramsg',
            'whatsapp_api_key'  => 'nullable|string',
            'whatsapp_from'     => 'nullable|string',
            'whatsapp_instance' => 'nullable|string',
        ]);

        // Handle logo upload
        if ($request->hasFile('school_logo')) {
            $path = $request->file('school_logo')->store('school', 'public');
            Setting::set('school_logo', $path);
        }

        foreach ($request->except(['_token', '_method', 'school_logo']) as $key => $value) {
            Setting::set($key, $value);
        }

        flush_settings_cache();

        return redirect()->route('settings.index')->with('success', 'Settings saved successfully');
    }

    /**
     * Send a WhatsApp message to a student's parent/contact.
     */
    public function sendWhatsApp(Request $request)
    {
        $request->validate([
            'to'      => 'required|string',
            'message' => 'required|string',
        ]);

        $provider = Setting::get('whatsapp_provider', 'ultramsg');
        $result   = $this->dispatchWhatsApp($provider, $request->to, $request->message);

        return response()->json($result);
    }

    /**
     * Send diary/notice via WhatsApp to all students matching audience.
     */
    public function broadcastNotice(Request $request)
    {
        $request->validate([
            'notice_id' => 'required|exists:notices,id',
        ]);

        $notice   = Notice::findOrFail($request->notice_id);
        $provider = Setting::get('whatsapp_provider', 'ultramsg');

        $students = Student::whereNotNull('whatsapp_number')->get();
        $sent     = 0;

        foreach ($students as $student) {
            $message = "📢 *{$notice->title}*\n\n{$notice->content}\n\n— " . setting('school_name', 'School');
            $res     = $this->dispatchWhatsApp($provider, $student->whatsapp_number, $message);
            if ($res['success']) $sent++;
        }

        return redirect()->back()->with('success', "Notice sent to {$sent} contacts via WhatsApp");
    }

    private function dispatchWhatsApp(string $provider, string $to, string $message): array
    {
        $apiKey   = Setting::get('whatsapp_api_key');
        $from     = Setting::get('whatsapp_from');
        $instance = Setting::get('whatsapp_instance');

        if (!$apiKey) {
            return ['success' => false, 'error' => 'WhatsApp API key not configured'];
        }

        try {
            if ($provider === 'ultramsg') {
                $response = Http::asForm()->post("https://api.ultramsg.com/{$instance}/messages/chat", [
                    'token'  => $apiKey,
                    'to'     => $to,
                    'body'   => $message,
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
                // WATI
                $response = Http::withHeaders(['Authorization' => "Bearer {$apiKey}"])
                    ->post("https://live-server-{$instance}.wati.io/api/v1/sendSessionMessage/{$to}", [
                        'messageText' => $message,
                    ]);
            }

            return ['success' => $response->successful(), 'response' => $response->json()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
