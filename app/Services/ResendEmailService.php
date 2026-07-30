<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use App\Mail\Auth\OtpEmail;
use App\Mail\Auth\WelcomeEmail;
use App\Mail\Auth\PasswordResetEmail;
use App\Mail\Auth\EmailVerificationMail;
use App\Models\User;

/**
 * Resend Email Service
 *
 * Sends transactional emails via the Resend API (https://resend.com).
 * Resend is the modern alternative to Mailgun/SendGrid with:
 *   - Superior deliverability
 *   - Batch sending
 *   - Email templates (React Email)
 *   - Webhook events
 *
 * Used for:
 *   1. OTP codes via email (as alternative to SMS)
 *   2. Welcome emails on registration
 *   3. Password reset links
 *   4. Email verification links
 *   5. Booking confirmations (future)
 *
 * API Docs: https://resend.com/docs/api-reference/emails/send-email
 */
class ResendEmailService
{
    private string $apiKey;
    private string $baseUrl   = 'https://api.resend.com';
    private string $fromEmail;
    private string $fromName;

    public function __construct()
    {
        $this->apiKey    = config('services.resend.api_key', '');
        $this->fromEmail = config('services.resend.from_address', 'no-reply@zuritrails.com');
        $this->fromName  = config('services.resend.from_name', 'ZuriTrails');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1. OTP via Email
    //    Used when user requests verification via email instead of phone
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Send a 6-digit OTP code to the user's email address.
     * Used for email-based verification or as SMS fallback.
     */
    public function sendOtpEmail(
        string $toEmail,
        string $toName,
        string $otpCode,
        string $verificationId,
        bool $isSignup = true
    ): bool {
        $expiryMinutes = 10;
        $subject       = $isSignup
            ? "Your ZuriTrails Verification Code: {$otpCode}"
            : "Your ZuriTrails Login Code: {$otpCode}";

        $html = $this->renderOtpTemplate([
            'name'            => $toName,
            'otp_code'        => $otpCode,
            'verification_id' => $verificationId,
            'expiry_minutes'  => $expiryMinutes,
            'is_signup'       => $isSignup,
            'year'            => date('Y'),
        ]);

        return $this->send(
            to:      [['email' => $toEmail, 'name' => $toName]],
            subject: $subject,
            html:    $html,
            tags:    [['name' => 'category', 'value' => 'otp']],
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. Welcome Email (registration)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Send a branded welcome email after successful registration.
     * Includes: profile completion tips, app download links, getting started guide.
     */
    public function sendWelcomeEmail(User $user): bool
    {
        if (!$user->email) return false;

        $verificationCode = sha1($user->email . $user->id);
        $frontendUrl      = config('app.frontend_url', 'https://app.zuritrails.com');
        $verifyLink       = "{$frontendUrl}/verify-email?user_id={$user->id}&code={$verificationCode}";
        $deepLink         = config('app.deep_link_scheme', 'zuritrails') . "://verify-email?user_id={$user->id}&code={$verificationCode}";

        $html = $this->renderWelcomeTemplate([
            'first_name'       => $user->first_name,
            'display_name'     => $user->display_name ?? $user->first_name,
            'explorer_level'   => ucfirst($user->explorer_level ?? 'explorer'),
            'verify_link'      => $verifyLink,
            'deep_link'        => $deepLink,
            'profile_url'      => "{$frontendUrl}/profile",
            'year'             => date('Y'),
        ]);

        return $this->send(
            to:      [['email' => $user->email, 'name' => $user->first_name]],
            subject: "Welcome to ZuriTrails, {$user->first_name}! 🦁 Your adventure awaits",
            html:    $html,
            tags:    [['name' => 'category', 'value' => 'welcome']],
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. Password Reset Email
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Send a password reset link via Resend.
     */
    public function sendPasswordResetEmail(string $email, string $token, string $firstName): bool
    {
        $frontendUrl = config('app.frontend_url', 'https://app.zuritrails.com');
        $resetLink   = "{$frontendUrl}/reset-password?token={$token}&email={$email}";
        $deepLink    = config('app.deep_link_scheme', 'zuritrails') . "://reset-password?token={$token}&email={$email}";
        $expiresIn   = '60 minutes';

        $html = $this->renderPasswordResetTemplate([
            'first_name' => $firstName,
            'reset_link' => $resetLink,
            'deep_link'  => $deepLink,
            'expires_in' => $expiresIn,
            'year'       => date('Y'),
        ]);

        return $this->send(
            to:      [['email' => $email, 'name' => $firstName]],
            subject: 'Reset Your ZuriTrails Password',
            html:    $html,
            tags:    [['name' => 'category', 'value' => 'password_reset']],
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. Email Verification
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Send email verification link (resend flow).
     */
    public function sendEmailVerification(User $user): bool
    {
        if (!$user->email) return false;

        $verificationCode = sha1($user->email . $user->id);
        $frontendUrl      = config('app.frontend_url', 'https://app.zuritrails.com');
        $verifyLink       = "{$frontendUrl}/verify-email?user_id={$user->id}&code={$verificationCode}";

        $html = $this->renderVerificationTemplate([
            'first_name'  => $user->first_name,
            'verify_link' => $verifyLink,
            'year'        => date('Y'),
        ]);

        return $this->send(
            to:      [['email' => $user->email, 'name' => $user->first_name]],
            subject: 'Verify Your ZuriTrails Email Address',
            html:    $html,
            tags:    [['name' => 'category', 'value' => 'email_verification']],
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 5. Core Send Method
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Send an email via the Resend API.
     *
     * @param  array  $to       [['email' => '...', 'name' => '...']]
     * @param  string $subject  Email subject
     * @param  string $html     HTML body
     * @param  string|null $text Plain text fallback
     * @param  array  $tags     Resend tags for analytics
     */
    public function send(
        array   $to,
        string  $subject,
        string  $html,
        ?string $text = null,
        array   $tags = [],
    ): bool {
        if (!$this->apiKey) {
            Log::warning('📧 Resend API key not configured. Email not sent.', [
                'to'      => $to,
                'subject' => $subject,
            ]);
            return app()->environment('local', 'testing'); // return true in dev so flow continues
        }

        try {
            $payload = [
                'from'    => "{$this->fromName} <{$this->fromEmail}>",
                'to'      => $to,
                'subject' => $subject,
                'html'    => $html,
            ];

            if ($text) {
                $payload['text'] = $text;
            }

            if (!empty($tags)) {
                $payload['tags'] = $tags;
            }

            $response = Http::withToken($this->apiKey)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(10)
                ->post("{$this->baseUrl}/emails", $payload);

            if ($response->successful()) {
                $body = $response->json();
                Log::info('📧 Email sent via Resend', [
                    'resend_id' => $body['id'] ?? null,
                    'to'        => collect($to)->pluck('email')->first(),
                    'subject'   => $subject,
                ]);
                return true;
            }

            Log::error('📧 Resend API error', [
                'status'  => $response->status(),
                'body'    => $response->json(),
                'subject' => $subject,
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('📧 Resend exception: ' . $e->getMessage(), [
                'subject' => $subject,
                'trace'   => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send a batch of emails (Resend supports up to 100 per batch).
     * Used for bulk notifications (e.g., challenge announcements).
     */
    public function sendBatch(array $emails): bool
    {
        if (!$this->apiKey || empty($emails)) return false;

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post("{$this->baseUrl}/emails/batch", $emails);

            if ($response->successful()) {
                Log::info('📧 Batch email sent via Resend', ['count' => count($emails)]);
                return true;
            }

            Log::error('📧 Resend batch error', ['status' => $response->status()]);
            return false;

        } catch (\Exception $e) {
            Log::error('📧 Resend batch exception: ' . $e->getMessage());
            return false;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Email HTML Templates (inline — production should use Blade views)
    // ─────────────────────────────────────────────────────────────────────────

    private function renderOtpTemplate(array $data): string
    {
        $name           = htmlspecialchars($data['name']);
        $otpCode        = $data['otp_code'];
        $expiryMinutes  = $data['expiry_minutes'];
        $isSignup       = $data['is_signup'];
        $year           = $data['year'];
        $action         = $isSignup ? 'complete your registration' : 'log in to your account';
        $digits         = implode('', array_map(
            fn($d) => "<span style=\"display:inline-block;width:44px;height:52px;background:#f4f6fb;border:2px solid #e0e5ef;border-radius:10px;font-size:28px;font-weight:700;line-height:52px;text-align:center;margin:0 4px;color:#1a1a2e;font-family:monospace\">{$d}</span>",
            str_split($otpCode)
        ));

        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Verification Code</title></head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:40px 0">
    <tr><td align="center">
      <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 4px 32px rgba(0,0,0,0.08)">
        <!-- Header -->
        <tr><td style="background:linear-gradient(135deg,#7c3aed 0%,#c026d3 100%);padding:36px 40px;text-align:center">
          <img src="https://zuritrails.com/assets/logo-white.png" alt="ZuriTrails" height="40" onerror="this.style.display='none'">
          <h1 style="color:#ffffff;font-size:26px;font-weight:700;margin:12px 0 0;letter-spacing:-0.5px">ZuriTrails</h1>
          <p style="color:rgba(255,255,255,0.8);margin:6px 0 0;font-size:14px">Your African Adventure Awaits</p>
        </td></tr>
        <!-- Body -->
        <tr><td style="padding:48px 40px">
          <p style="font-size:16px;color:#374151;margin:0 0 8px">Hello, <strong>{$name}</strong>! 👋</p>
          <p style="font-size:15px;color:#6b7280;margin:0 0 32px;line-height:1.6">Use the verification code below to {$action}:</p>
          <!-- OTP digits -->
          <div style="text-align:center;margin:0 0 32px">{$digits}</div>
          <p style="text-align:center;font-size:13px;color:#9ca3af;margin:0 0 32px">⏱ This code expires in <strong>{$expiryMinutes} minutes</strong>.</p>
          <div style="background:#fef3c7;border:1px solid #fbbf24;border-radius:10px;padding:16px;margin:0 0 24px">
            <p style="font-size:13px;color:#92400e;margin:0">🔒 <strong>Never share this code</strong> with anyone. ZuriTrails will never ask for your OTP.</p>
          </div>
          <p style="font-size:13px;color:#9ca3af;margin:0">If you didn't request this code, you can safely ignore this email.</p>
        </td></tr>
        <!-- Footer -->
        <tr><td style="background:#f9fafb;padding:24px 40px;text-align:center;border-top:1px solid #f3f4f6">
          <p style="font-size:12px;color:#9ca3af;margin:0">© {$year} ZuriTrails. All rights reserved.</p>
          <p style="font-size:12px;color:#9ca3af;margin:8px 0 0">Nairobi, Kenya 🇰🇪</p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }

    private function renderWelcomeTemplate(array $data): string
    {
        $name          = htmlspecialchars($data['first_name']);
        $displayName   = htmlspecialchars($data['display_name']);
        $level         = htmlspecialchars($data['explorer_level']);
        $verifyLink    = htmlspecialchars($data['verify_link']);
        $year          = $data['year'];

        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Welcome to ZuriTrails!</title></head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:40px 0">
    <tr><td align="center">
      <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 4px 32px rgba(0,0,0,0.08)">
        <!-- Hero -->
        <tr><td style="background:linear-gradient(135deg,#7c3aed 0%,#c026d3 100%);padding:48px 40px;text-align:center">
          <div style="font-size:56px;margin:0 0 16px">🦁</div>
          <h1 style="color:#ffffff;font-size:28px;font-weight:800;margin:0;letter-spacing:-0.5px">Welcome, {$name}!</h1>
          <p style="color:rgba(255,255,255,0.85);margin:12px 0 0;font-size:16px">Your journey as an <strong>{$level}</strong> starts now</p>
        </td></tr>
        <!-- Body -->
        <tr><td style="padding:48px 40px">
          <p style="font-size:16px;color:#374151;line-height:1.7;margin:0 0 24px">You've just joined thousands of explorers discovering the hidden gems of Africa. 🌍</p>
          <!-- Verify CTA -->
          <div style="text-align:center;margin:0 0 40px">
            <a href="{$verifyLink}" style="display:inline-block;background:linear-gradient(135deg,#7c3aed,#c026d3);color:#ffffff;text-decoration:none;font-size:16px;font-weight:700;padding:16px 40px;border-radius:12px;letter-spacing:0.2px">Verify My Email ✓</a>
          </div>
          <!-- Features -->
          <h3 style="font-size:16px;font-weight:700;color:#1f2937;margin:0 0 16px">What you can do with ZuriTrails:</h3>
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td style="padding:12px 0;border-bottom:1px solid #f3f4f6">
                <span style="font-size:20px">🗺️</span>
                <strong style="font-size:14px;color:#374151;margin-left:12px">Discover Hidden Gems</strong>
                <p style="font-size:13px;color:#6b7280;margin:4px 0 0 32px">Find secret spots across Kenya and East Africa</p>
              </td>
            </tr>
            <tr>
              <td style="padding:12px 0;border-bottom:1px solid #f3f4f6">
                <span style="font-size:20px">🏆</span>
                <strong style="font-size:14px;color:#374151;margin-left:12px">Earn XP & Badges</strong>
                <p style="font-size:13px;color:#6b7280;margin:4px 0 0 32px">Level up from Explorer to Legend</p>
              </td>
            </tr>
            <tr>
              <td style="padding:12px 0">
                <span style="font-size:20px">📍</span>
                <strong style="font-size:14px;color:#374151;margin-left:12px">Track Your Journeys</strong>
                <p style="font-size:13px;color:#6b7280;margin:4px 0 0 32px">GPS-track safaris and adventures like Strava</p>
              </td>
            </tr>
          </table>
        </td></tr>
        <!-- Footer -->
        <tr><td style="background:#f9fafb;padding:24px 40px;text-align:center;border-top:1px solid #f3f4f6">
          <p style="font-size:12px;color:#9ca3af;margin:0">© {$year} ZuriTrails · Nairobi, Kenya 🇰🇪</p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }

    private function renderPasswordResetTemplate(array $data): string
    {
        $name      = htmlspecialchars($data['first_name']);
        $resetLink = htmlspecialchars($data['reset_link']);
        $expiresIn = $data['expires_in'];
        $year      = $data['year'];

        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Reset Your Password</title></head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:40px 0">
    <tr><td align="center">
      <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 4px 32px rgba(0,0,0,0.08)">
        <tr><td style="background:linear-gradient(135deg,#1e3a5f 0%,#7c3aed 100%);padding:36px 40px;text-align:center">
          <div style="font-size:48px">🔐</div>
          <h1 style="color:#ffffff;font-size:24px;font-weight:700;margin:12px 0 0">Password Reset</h1>
        </td></tr>
        <tr><td style="padding:48px 40px">
          <p style="font-size:16px;color:#374151;margin:0 0 16px">Hi <strong>{$name}</strong>,</p>
          <p style="font-size:15px;color:#6b7280;margin:0 0 32px;line-height:1.6">We received a request to reset your ZuriTrails password. Click the button below to create a new one:</p>
          <div style="text-align:center;margin:0 0 32px">
            <a href="{$resetLink}" style="display:inline-block;background:linear-gradient(135deg,#7c3aed,#c026d3);color:#ffffff;text-decoration:none;font-size:16px;font-weight:700;padding:16px 40px;border-radius:12px">Reset My Password</a>
          </div>
          <p style="font-size:13px;color:#9ca3af;margin:0 0 16px">⏱ This link expires in <strong>{$expiresIn}</strong>.</p>
          <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:16px">
            <p style="font-size:13px;color:#991b1b;margin:0">If you didn't request a password reset, please ignore this email. Your account is secure.</p>
          </div>
          <p style="margin:24px 0 0;font-size:13px;color:#9ca3af">If the button doesn't work, copy this link:<br><span style="word-break:break-all;color:#7c3aed">{$resetLink}</span></p>
        </td></tr>
        <tr><td style="background:#f9fafb;padding:24px 40px;text-align:center;border-top:1px solid #f3f4f6">
          <p style="font-size:12px;color:#9ca3af;margin:0">© {$year} ZuriTrails · Nairobi, Kenya 🇰🇪</p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }

    private function renderVerificationTemplate(array $data): string
    {
        $name       = htmlspecialchars($data['first_name']);
        $verifyLink = htmlspecialchars($data['verify_link']);
        $year       = $data['year'];

        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Verify Your Email</title></head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:40px 0">
    <tr><td align="center">
      <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 4px 32px rgba(0,0,0,0.08)">
        <tr><td style="background:linear-gradient(135deg,#7c3aed 0%,#c026d3 100%);padding:36px 40px;text-align:center">
          <div style="font-size:48px">✉️</div>
          <h1 style="color:#ffffff;font-size:24px;font-weight:700;margin:12px 0 0">Verify Your Email</h1>
        </td></tr>
        <tr><td style="padding:48px 40px">
          <p style="font-size:16px;color:#374151;margin:0 0 16px">Hi <strong>{$name}</strong>,</p>
          <p style="font-size:15px;color:#6b7280;margin:0 0 32px;line-height:1.6">Please verify your email address to unlock all ZuriTrails features and start earning XP!</p>
          <div style="text-align:center;margin:0 0 32px">
            <a href="{$verifyLink}" style="display:inline-block;background:linear-gradient(135deg,#7c3aed,#c026d3);color:#ffffff;text-decoration:none;font-size:16px;font-weight:700;padding:16px 40px;border-radius:12px">Verify Email Address ✓</a>
          </div>
          <p style="font-size:13px;color:#9ca3af">If you didn't create a ZuriTrails account, you can safely ignore this email.</p>
        </td></tr>
        <tr><td style="background:#f9fafb;padding:24px 40px;text-align:center;border-top:1px solid #f3f4f6">
          <p style="font-size:12px;color:#9ca3af;margin:0">© {$year} ZuriTrails · Nairobi, Kenya 🇰🇪</p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }
}
