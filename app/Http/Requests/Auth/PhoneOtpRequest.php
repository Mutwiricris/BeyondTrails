<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Phone OTP Request — from phone_auth_screen.dart
 * Fields: phone_number (E.164), country_code, is_signup
 */
class PhoneOtpRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'phone_number' => ['required', 'string', 'regex:/^\+[1-9]\d{1,14}$/'],
            'country_code' => 'required|string|size:2',
            'is_signup'    => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.regex' => 'Please enter a valid phone number (e.g. +254712345678).',
        ];
    }
}
