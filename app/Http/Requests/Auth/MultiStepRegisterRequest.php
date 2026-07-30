<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Multi-Step Registration Request
 *
 * Validates all 13 steps from SignupFlowCoordinator:
 * Step 1:  welcome (no data)
 * Step 2:  birthday
 * Step 3:  referral_source
 * Step 4:  first_name, last_name
 * Step 5:  gender
 * Step 6:  home_country
 * Step 7:  travel_styles[]
 * Step 8:  interests[]
 * Step 9:  profile_photo_path (optional)
 * Step 10: notifications_enabled
 * Step 11: activity_preferences[]
 * Step 12: location_enabled, show_distance_away
 * Step 13: email, password, password_confirmation (privacy/terms acceptance)
 */
class MultiStepRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Step 4: Name ──────────────────────────────────────────────
            'username'   => 'required|string|max:100|unique:users,username',
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',

            // ── Step 2: Birthday ──────────────────────────────────────────
            'birthday' => [
                'required',
                'date',
                'before:' . now()->subYears(14)->toDateString(),
                'after:1900-01-01',
            ],

            // ── Step 3: Referral Source ───────────────────────────────────
            'referral_source' => [
                'nullable',
                'string',
                'max:100',
            ],

            // ── Step 5: Gender ────────────────────────────────────────────
            'gender' => 'required|string|max:100',

            // ── Step 6: Country ───────────────────────────────────────────
            'home_country' => 'required|string|max:100',

            // ── Step 7: Travel Styles (multi-select, min 1) ───────────────
            'travel_styles'   => 'required|array|min:1',
            'travel_styles.*' => 'string|max:100',

            // ── Step 8: Interests (multi-select, min 1) ───────────────────
            'interests'   => 'required|array|min:1',
            'interests.*' => 'string|max:100',

            // ── Step 9: Profile Photo (optional) ──────────────────────────
            'profile_photo_path' => 'nullable|string',

            // ── Step 10: Notifications ────────────────────────────────────
            'notifications_enabled' => 'required|boolean',

            // ── Step 11: Activity Preferences (multi-select, min 1) ───────
            'activity_preferences'   => 'required|array|min:1',
            'activity_preferences.*' => 'string|max:100',

            // ── Step 12: Location ─────────────────────────────────────────
            'location_enabled'  => 'required|boolean',
            'show_distance_away' => 'required|boolean',

            // ── Step 13: Email & Password (Privacy step) ──────────────────
            'email'                 => 'required|email:rfc|unique:users,email|max:255',
            'password'              => ['required', 'confirmed', Password::min(8)],
            'password_confirmation' => 'required|string',

            // Optional
            'device_name'  => 'nullable|string|max:255',
            'remember_me'  => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'birthday.before'           => 'You must be at least 14 years old to sign up.',
            'birthday.after'            => 'Please enter a valid date of birth.',
            'email.unique'              => 'This email address is already registered.',
            'travel_styles.min'         => 'Please select at least one travel style.',
            'interests.min'             => 'Please select at least one interest.',
            'activity_preferences.min'  => 'Please select at least one activity preference.',
            'gender.in'                 => 'Please select a valid gender option.',
        ];
    }
}
