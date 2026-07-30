<?php

namespace App\Services;

use App\Models\User;

/**
 * Profile Completion Service
 *
 * Calculates a user's profile completion percentage.
 * Mirrors the completionPercentage getter in Flutter's UserProfile model.
 */
class ProfileCompletionService
{
    /**
     * Calculate profile completion score (0-100).
     *
     * Fields are weighted to incentivize completion of all steps.
     */
    public function calculate(User $user): int
    {
        $score = 0;

        // ── Tier 1: Auth (required for basic use) ────────── 30 pts
        if ($user->email)              $score += 10;
        if ($user->email_verified_at)  $score += 5;
        if ($user->phone_number)       $score += 10;
        if ($user->phone_verified_at)  $score += 5;

        // ── Tier 2: Basic Identity ───────────────────────── 20 pts
        if ($user->first_name && $user->last_name) $score += 5;
        if ($user->date_of_birth)      $score += 5;
        if ($user->photo_url)          $score += 10;

        // ── Tier 3: Multi-step profile ───────────────────── 25 pts
        if ($user->gender)             $score += 5;
        if ($user->home_country)       $score += 5;
        if ($user->bio)                $score += 5;
        if ($user->referral_source)    $score += 2;
        if ($user->nationality)        $score += 3;
        if ($user->city)               $score += 5;

        // ── Tier 4: Preferences ──────────────────────────── 15 pts
        if (!empty($user->interests))           $score += 5;
        if (!empty($user->travel_styles))       $score += 5;
        if (!empty($user->activity_preferences)) $score += 5;

        // ── Tier 5: Safety & Settings ────────────────────── 10 pts
        if ($user->emergency_contact_name && $user->emergency_contact_phone) $score += 5;
        if ($user->travel_style)       $score += 2;
        if ($user->preferred_currency) $score += 1;
        if (!empty($user->languages))  $score += 2;

        return min(100, $score);
    }

    /**
     * Get a list of incomplete profile sections (for "Complete Your Profile" prompts).
     */
    public function getMissingFields(User $user): array
    {
        $missing = [];

        if (!$user->photo_url) {
            $missing[] = ['field' => 'photo', 'label' => 'Add a profile photo', 'xp_reward' => 20];
        }
        if (!$user->bio) {
            $missing[] = ['field' => 'bio', 'label' => 'Write a short bio', 'xp_reward' => 10];
        }
        if (!$user->phone_number) {
            $missing[] = ['field' => 'phone', 'label' => 'Add your phone number', 'xp_reward' => 15];
        }
        if (empty($user->interests)) {
            $missing[] = ['field' => 'interests', 'label' => 'Set your travel interests', 'xp_reward' => 10];
        }
        if (!$user->emergency_contact_name) {
            $missing[] = ['field' => 'emergency_contact', 'label' => 'Add an emergency contact', 'xp_reward' => 10];
        }
        if (empty($user->languages)) {
            $missing[] = ['field' => 'languages', 'label' => 'Add spoken languages', 'xp_reward' => 5];
        }
        if (!$user->city) {
            $missing[] = ['field' => 'location', 'label' => 'Add your city', 'xp_reward' => 5];
        }

        return $missing;
    }
}
