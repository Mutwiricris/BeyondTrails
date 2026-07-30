<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * UserResource
 *
 * Transforms the User model into the exact JSON structure
 * expected by the Flutter app's UserProfile.fromJson() factory.
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // ── Identity ───────────────────────────────────────────────────
            'id'             => $this->id,
            'name'           => $this->name ?? ($this->first_name . ' ' . $this->last_name),
            'email'          => $this->email,
            'phone_number'   => $this->phone_number,
            'first_name'     => $this->first_name,
            'last_name'      => $this->last_name,
            'display_name'   => $this->display_name ?? ($this->first_name . ' ' . $this->last_name),
            'full_name'      => $this->first_name . ' ' . $this->last_name,
            'selectables'    => array_merge($this->selectables ?? [], [
                'firstName'             => $this->first_name,
                'lastName'              => $this->last_name,
                'photoUrl'              => $this->photo_url,
                'bio'                   => $this->bio,
                'gender'                => $this->gender,
                'nationality'           => $this->nationality,
                'dateOfBirth'           => $this->date_of_birth?->toDateString(),
                'interests'             => $this->interests ?? [],
                'travelStyles'          => $this->travel_styles ?? [],
                'activityPreferences'   => $this->activity_preferences ?? [],
                'homeCountry'           => $this->home_country,
                'referralSource'        => $this->referral_source,
                'emailNotifications'    => $this->email_notifications,
                'smsNotifications'      => $this->sms_notifications,
                'pushNotifications'     => $this->push_notifications,
                'locationEnabled'       => $this->location_enabled,
                'showDistanceAway'      => $this->show_distance_away,
                'isProfilePublic'       => $this->is_profile_public,
                'shareLocationWithFriends' => $this->share_location_with_friends,
                'preferredCurrency'     => $this->preferred_currency,
                'emergencyContactName'  => $this->emergency_contact_name,
                'emergencyContactPhone' => $this->emergency_contact_phone,
                'emergencyContactRelation' => $this->emergency_contact_relation,
                'travelStyle'           => $this->travel_style,
                'travelInsurance'       => $this->travel_insurance,
                'phone'                 => $this->phone_number,
                'phoneNumber'           => $this->phone_number,
                'city'                  => $this->city,
                'country'               => $this->country,
                'postalCode'            => $this->postal_code,
            ]),

            // ── Profile ────────────────────────────────────────────────────
            'bio'            => $this->bio,
            'photo_url'      => $this->photo_url,
            'photo_thumbnail_url' => $this->photo_thumbnail_url,
            'date_of_birth'  => $this->date_of_birth?->toDateString(),
            'age'            => $this->age,
            'gender'         => $this->gender,
            'nationality'    => $this->nationality,
            'home_country'   => $this->home_country,
            'referral_source' => $this->referral_source,

            // ── Address ────────────────────────────────────────────────────
            'address'        => $this->address,
            'city'           => $this->city,
            'country'        => $this->country,
            'postal_code'    => $this->postal_code,

            // ── Preferences (JSONB arrays) ─────────────────────────────────
            'interests'              => $this->interests ?? [],
            'languages'              => $this->languages ?? [],
            'travel_styles'          => $this->travel_styles ?? [],
            'activity_preferences'   => $this->activity_preferences ?? [],
            'dietary_restrictions'   => $this->dietary_restrictions ?? [],
            'accessibility_needs'    => $this->accessibility_needs ?? [],

            // ── Settings ───────────────────────────────────────────────────
            'preferred_currency'         => $this->preferred_currency,
            'email_notifications'        => $this->email_notifications,
            'sms_notifications'          => $this->sms_notifications,
            'push_notifications'         => $this->push_notifications,
            'location_enabled'           => $this->location_enabled,
            'show_distance_away'         => $this->show_distance_away,

            // ── Privacy ────────────────────────────────────────────────────
            'is_profile_public'           => $this->is_profile_public,
            'share_location_with_friends' => $this->share_location_with_friends,

            // ── Emergency Contact ──────────────────────────────────────────
            'emergency_contact' => $this->emergency_contact_name ? [
                'name'     => $this->emergency_contact_name,
                'phone'    => $this->emergency_contact_phone,
                'relation' => $this->emergency_contact_relation,
            ] : null,

            // ── Travel Preferences ─────────────────────────────────────────
            'travel_style'    => $this->travel_style,
            'travel_insurance' => $this->travel_insurance,

            // ── Gamification ───────────────────────────────────────────────
            'explorer_level'   => $this->explorer_level,
            'current_xp'       => $this->current_xp,
            'xp_to_next_level' => $this->xp_to_next_level,
            'level_progress'   => round($this->level_progress, 4),
            'streak_days'      => $this->streak_days,
            'unlocked_badges'  => $this->unlocked_badges ?? [],

            // ── Account ────────────────────────────────────────────────────
            'role'               => $this->role,
            'profile_completion' => $this->profile_completion,
            'email_verified'     => !is_null($this->email_verified_at),
            'phone_verified'     => !is_null($this->phone_verified_at),

            // ── Verification timestamps ────────────────────────────────────
            'email_verified_at'  => $this->email_verified_at?->toIso8601String(),
            'phone_verified_at'  => $this->phone_verified_at?->toIso8601String(),
            'last_active_at'     => $this->last_active_at?->toIso8601String(),

            // ── Timestamps ─────────────────────────────────────────────────
            'created_at'  => $this->created_at->toIso8601String(),
            'updated_at'  => $this->updated_at->toIso8601String(),
        ];
    }
}
