<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    public function __construct(private readonly ResendEmailService $resendEmail) {}

    /**
     * Send email verification link to a newly registered user.
     */
    public function sendEmailVerification(User $user): void
    {
        if (!$user->email) return;

        // Use Resend to send the welcome email (which includes the verification link)
        // or just the verification email if it's a resend request.
        
        // For a new user, we can send the Welcome email which has the verification link.
        // For simplicity, let's just call the welcome email here if they are new,
        // or the verification email if we just want to verify.
        
        $this->resendEmail->sendEmailVerification($user);

        \Illuminate\Support\Facades\Log::info("Email verification sent to {$user->email} via Resend");
    }


    /**
     * Get aggregated user statistics for login/me responses.
     * Mirrors the stats object expected by login_screen.dart.
     */
    public function getUserStats(User $user): array
    {
        // These will query the DB once other tables exist.
        // For now return zeros until journeys/gems/trips tables are added.
        return [
            'total_journeys'       => 0, // TODO: Journey::where('user_id', $user->id)->count()
            'total_distance_km'    => 0, // TODO: Journey::where('user_id', $user->id)->sum('distance_km')
            'total_gems_discovered' => 0, // TODO: VisitRecord::where('user_id', $user->id)->where('place_type','gem')->count()
            'total_trips'          => 0, // TODO: Trip::where('user_id', $user->id)->count()
            'active_streak'        => $user->streak_days > 0,
        ];
    }

    /**
     * Process a profile photo from temp path to permanent storage.
     * Returns [photo_url, thumbnail_url].
     */
    public function processProfilePhoto(string $tempPath): array
    {
        // TODO: Implement with Intervention Image + S3/Spaces
        // 1. Load from temp/uploads/
        // 2. Resize to 800x800 (profile) + 200x200 (thumbnail)
        // 3. Upload to S3
        // 4. Return CDN URLs

        \Illuminate\Support\Facades\Log::info("Processing profile photo from: {$tempPath}");

        return [null, null]; // [photo_url, thumbnail_url]
    }

    /**
     * Download a social provider's profile photo and store it.
     */
    public function downloadAndStorePhoto(string $url): ?string
    {
        // TODO: Download with Guzzle, store with S3
        return $url; // For now return the original URL
    }

    /**
     * Derive nationality from country name.
     */
    public function getNationalityFromCountry(?string $country): ?string
    {
        if (!$country) return null;

        $nationalities = [
            'Kenya'          => 'Kenyan',
            'Tanzania'       => 'Tanzanian',
            'Uganda'         => 'Ugandan',
            'Rwanda'         => 'Rwandan',
            'Ethiopia'       => 'Ethiopian',
            'South Africa'   => 'South African',
            'United States'  => 'American',
            'United Kingdom' => 'British',
            'Germany'        => 'German',
            'France'         => 'French',
            'India'          => 'Indian',
            'China'          => 'Chinese',
            'Japan'          => 'Japanese',
            'Australia'      => 'Australian',
            'Canada'         => 'Canadian',
        ];

        return $nationalities[$country] ?? $country;
    }

    /**
     * Verify a social OAuth token with the respective provider.
     * TODO: implement per-provider verification.
     */
    public function verifyProviderToken(string $provider, string $token, string $providerId): bool
    {
        // Google: GET https://oauth2.googleapis.com/tokeninfo?access_token={token}
        // Apple:  Decode and verify JWT
        // Facebook: GET https://graph.facebook.com/me?access_token={token}
        return true; // stub
    }
}
