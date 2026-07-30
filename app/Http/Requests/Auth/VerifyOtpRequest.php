<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Verify OTP Request — from verification_code_screen.dart
 * User enters 6-digit code from SMS
 */
class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'verification_id'        => 'required|string',
            'otp_code'               => 'required|string|size:6|regex:/^\d{6}$/',
            'is_signup'              => 'required|boolean',

            // signup_data is required only when is_signup=true
            'signup_data'                  => 'required_if:is_signup,true|array',
            'signup_data.first_name'       => 'required_if:is_signup,true|string|max:100',
            'signup_data.last_name'        => 'required_if:is_signup,true|string|max:100',
            'signup_data.travel_styles'    => 'nullable|array',
            'signup_data.travel_styles.*'  => 'string',
            'signup_data.interests'        => 'nullable|array',
            'signup_data.interests.*'      => 'string',
        ];
    }

    public function messages(): array
    {
        return [
            'otp_code.size'  => 'Verification code must be 6 digits.',
            'otp_code.regex' => 'Verification code must contain only numbers.',
        ];
    }
}
