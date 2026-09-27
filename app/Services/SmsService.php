<?php

namespace App\Services;

use App\Models\SmsLog;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS message using the tenant's configured gateway.
     */
    public function send($tenant, $recipient, $message, $bookingId = null)
    {
        // Get tenant settings
        $settings = $tenant->settings ?? [];
        $provider = $settings['sms_provider'] ?? 'log'; // Default to log for now
        
        $status = 'sent';
        $response = 'Mock Success';

        if ($provider === 'log') {
            Log::info("SMS to {$recipient} from Tenant [{$tenant->name}]: {$message}");
        } else {
            // Future: Integrate real providers here (Twilio, BulkSMSBD, etc.)
            // For now, we stay with log mode unless API keys are provided.
        }

        // Log the SMS
        SmsLog::create([
            'booking_id' => $bookingId,
            'recipient' => $recipient,
            'message' => $message,
            'provider' => $provider,
            'status' => $status,
            'response' => $response,
        ]);

        return true;
    }

    /**
     * Helper to parse templates with placeholders
     */
    public function parseTemplate($template, $data)
    {
        foreach ($data as $placeholder => $value) {
            $template = str_replace("[{$placeholder}]", $value, $template);
        }
        return $template;
    }
}
