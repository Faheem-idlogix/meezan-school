<?php

namespace App\Jobs;

use App\Models\Setting;
use App\Models\WhatsappLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 30;

    public function __construct(
        public string  $to,
        public string  $message,
        public string  $messageType = 'manual',
        public ?string $recipientType = null,
        public ?int    $recipientId = null,
        public ?string $recipientName = null,
        public ?int    $logId = null,
    ) {}

    public function handle(): void
    {
        // Find the queued log entry created by controller
        $log = $this->logId
            ? WhatsappLog::find($this->logId)
            : WhatsappLog::where('to', $this->to)
                ->where('status', 'queued')
                ->where('message_type', $this->messageType)
                ->latest()
                ->first();

        // Re-check daily limit at send time
        $dailyLimit = (int) Setting::get('wa_daily_limit', 200);
        $todaySent  = WhatsappLog::where('status', 'sent')
            ->whereDate('created_at', today())
            ->count();

        if ($todaySent >= $dailyLimit) {
            $this->updateLog($log, 'failed', 'Daily limit reached (' . $dailyLimit . '/day)');
            return;
        }

        // Re-check monthly limit at send time
        $monthlyLimit = (int) Setting::get('wa_monthly_limit', 3000);
        $monthSent    = WhatsappLog::where('status', 'sent')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        if ($monthSent >= $monthlyLimit) {
            $this->updateLog($log, 'failed', 'Monthly limit reached (' . $monthlyLimit . '/month)');
            return;
        }

        $provider = Setting::get('whatsapp_provider', 'ultramsg');
        $apiKey   = Setting::get('whatsapp_api_key');
        $from     = Setting::get('whatsapp_from');
        $instance = Setting::get('whatsapp_instance');

        if (!$apiKey) {
            $this->updateLog($log, 'failed', 'API key not configured');
            return;
        }

        try {
            if ($provider === 'cloud_api') {
                // Meta WhatsApp Cloud API — FREE 1000 conversations/month
                $phoneNumberId = $instance; // Phone Number ID from Meta
                $response = Http::withToken($apiKey)
                    ->post("https://graph.facebook.com/v21.0/{$phoneNumberId}/messages", [
                        'messaging_product' => 'whatsapp',
                        'to'   => $this->to,
                        'type' => 'text',
                        'text' => ['body' => $this->message],
                    ]);
            } elseif ($provider === 'ultramsg') {
                $response = Http::asForm()->post("https://api.ultramsg.com/{$instance}/messages/chat", [
                    'token' => $apiKey,
                    'to'    => $this->to,
                    'body'  => $this->message,
                ]);
            } elseif ($provider === 'twilio') {
                $response = Http::withBasicAuth($from, $apiKey)
                    ->asForm()
                    ->post("https://api.twilio.com/2010-04-01/Accounts/{$from}/Messages.json", [
                        'From' => "whatsapp:{$from}",
                        'To'   => "whatsapp:{$this->to}",
                        'Body' => $this->message,
                    ]);
            } else {
                $response = Http::withHeaders(['Authorization' => "Bearer {$apiKey}"])
                    ->post("https://live-server-{$instance}.wati.io/api/v1/sendSessionMessage/{$this->to}", [
                        'messageText' => $this->message,
                    ]);
            }

            $this->updateLog(
                $log,
                $response->successful() ? 'sent' : 'failed',
                json_encode($response->json())
            );
        } catch (\Exception $e) {
            $this->updateLog($log, 'failed', $e->getMessage());
        }
    }

    private function updateLog(?WhatsappLog $log, string $status, string $apiResponse): void
    {
        if ($log) {
            $log->update([
                'status'       => $status,
                'api_response' => $apiResponse,
            ]);
        } else {
            // Fallback: create log if queued entry not found
            WhatsappLog::create([
                'school_id'      => 1,
                'to'             => $this->to,
                'recipient_name' => $this->recipientName,
                'recipient_type' => $this->recipientType,
                'recipient_id'   => $this->recipientId,
                'message_type'   => $this->messageType,
                'message'        => $this->message,
                'status'         => $status,
                'api_response'   => $apiResponse,
                'provider'       => Setting::get('whatsapp_provider', 'ultramsg'),
            ]);
        }
    }
}
