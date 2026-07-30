<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SMS Service
 *
 * Sends OTP codes via Africa's Talking (primary) or Twilio (fallback).
 * Used by PhoneAuthController for the phone_auth_screen.dart flow.
 */
class SmsService
{
    /**
     * Send a 6-digit OTP code to a phone number.
     * Format: "Your ZuriTrails code is: 123456. Valid for 5 minutes."
     */
    public function sendOtp(string $phoneNumber, string $otpCode): bool
    {
        $message = "Your ZuriTrails verification code is: {$otpCode}. Valid for 5 minutes. Do not share this code.";

        try {
            return $this->sendViaAfricasTalking($phoneNumber, $message);
        } catch (\Exception $e) {
            Log::warning("Africa's Talking failed, trying Twilio: " . $e->getMessage());

            try {
                return $this->sendViaTwilio($phoneNumber, $message);
            } catch (\Exception $e2) {
                Log::error("Both SMS providers failed: " . $e2->getMessage());
                return false;
            }
        }
    }

    /**
     * Send via Africa's Talking API
     * Docs: https://developers.africastalking.com/docs/sms/sending
     */
    private function sendViaAfricasTalking(string $phoneNumber, string $message): bool
    {
        $apiKey   = config('services.africastalking.api_key');
        $username = config('services.africastalking.username', 'sandbox');
        $senderId = config('services.africastalking.sender_id', 'ZuriTrails');

        if (!$apiKey) {
            Log::warning('Africa\'s Talking API key not configured. OTP: ' . $message);
            return true; // Dev mode: log and return success
        }

        $baseUrl = $username === 'sandbox'
            ? 'https://api.sandbox.africastalking.com/version1/messaging'
            : 'https://api.africastalking.com/version1/messaging';

        $response = Http::withHeaders([
            'apiKey'       => $apiKey,
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept'       => 'application/json',
        ])->asForm()->post($baseUrl, [
            'username'  => $username,
            'to'        => $phoneNumber,
            'message'   => $message,
            'from'      => $senderId,
        ]);

        if ($response->successful()) {
            $body = $response->json();
            Log::info("SMS sent via Africa's Talking to {$phoneNumber}", $body);
            return true;
        }

        Log::error("Africa's Talking SMS failed", ['response' => $response->body()]);
        return false;
    }

    /**
     * Send via Twilio (fallback)
     * Docs: https://www.twilio.com/docs/sms/api
     */
    private function sendViaTwilio(string $phoneNumber, string $message): bool
    {
        $sid      = config('services.twilio.sid');
        $token    = config('services.twilio.token');
        $fromNum  = config('services.twilio.from');

        if (!$sid || !$token) {
            Log::warning('Twilio not configured. OTP would be sent to: ' . $phoneNumber);
            return true; // Dev mode
        }

        $response = Http::withBasicAuth($sid, $token)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'From' => $fromNum,
                'To'   => $phoneNumber,
                'Body' => $message,
            ]);

        if ($response->successful()) {
            Log::info("SMS sent via Twilio to {$phoneNumber}");
            return true;
        }

        throw new \Exception('Twilio SMS failed: ' . $response->body());
    }
}
