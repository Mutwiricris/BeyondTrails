<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

/**
 * Simple Registration Request
 *
 * Validates the basic signup form from signup_screen.dart
 * Fields: first_name, last_name, date_of_birth, email, password
 */
class SimpleRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username'              => 'nullable|string|max:100|unique:users,username',
            'first_name'            => 'required|string|max:100',
            'last_name'             => 'required|string|max:100',
            // date_of_birth: must be 18+ years ago
            'date_of_birth'         => [
                'nullable',
                'date',
                'before:' . now()->subYears(14)->toDateString(),
                'after:1900-01-01',
            ],
            'email'                 => 'required|email|unique:users,email|max:255',
            'password'              => ['required', 'confirmed', Password::min(8)],
            'password_confirmation' => 'required|string',
            'device_name'           => 'nullable|string|max:255',
            'selectables'           => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'date_of_birth.before'  => 'You must be at least 18 years old to sign up.',
            'date_of_birth.after'   => 'Please enter a valid date of birth.',
            'email.unique'          => 'This email address is already registered.',
            'email.email'           => 'Please enter a valid email address.',
            'password.min'          => 'Password must be at least 8 characters.',
        ];
    }
}
