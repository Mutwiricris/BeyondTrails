<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('Register', function () {

    it('registers a new user and returns a token', function () {
        $response = $this->postJson('/api/v1/auth/register', [
            'first_name'            => 'Trail',
            'last_name'             => 'Explorer',
            'date_of_birth'         => '2000-01-01',
            'email'                 => 'explorer@beyondtrails.ke',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'selectables', 'created_at'],
                    'token' => ['access_token', 'token_type', 'expires_at'],
                ],
                'meta',
            ])
            ->assertJsonFragment(['email' => 'explorer@beyondtrails.ke']);

        $this->assertDatabaseHas('users', ['email' => 'explorer@beyondtrails.ke']);
    });

    it('stores selectables as json during registration', function () {
        $selectables = [
            'interests'    => ['Hiking', 'Bird Watching'],
            'travelStyles' => ['Adventure Seeker'],
            'gender'       => 'Male',
        ];

        $response = $this->postJson('/api/v1/auth/register', [
            'first_name'            => 'JSON',
            'last_name'             => 'User',
            'date_of_birth'         => '2000-01-01',
            'email'                 => 'json@beyondtrails.ke',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'selectables'           => $selectables,
        ]);

        $response->assertStatus(201);

        $user = User::where('email', 'json@beyondtrails.ke')->first();
        expect($user->selectables)->toBe($selectables);
    });

    it('rejects duplicate email', function () {
        User::factory()->create([
            'email' => 'taken@beyondtrails.ke',
            'username' => 'takenuser'
        ]);

        $response = $this->postJson('/api/v1/auth/register', [
            'first_name'            => 'Dup',
            'last_name'             => 'User',
            'date_of_birth'         => '2000-01-01',
            'email'                 => 'taken@beyondtrails.ke',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    it('rejects missing name', function () {
        $this->postJson('/api/v1/auth/register', [
            'last_name'             => 'User',
            'date_of_birth'         => '2000-01-01',
            'email'                 => 'noname@beyondtrails.ke',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertStatus(422)->assertJsonValidationErrors(['first_name']);
    });

    it('rejects password shorter than 8 chars', function () {
        $this->postJson('/api/v1/auth/register', [
            'first_name'            => 'Short',
            'last_name'             => 'Pass',
            'date_of_birth'         => '2000-01-01',
            'email'                 => 'shortpass@beyondtrails.ke',
            'password'              => 'abc',
            'password_confirmation' => 'abc',
        ])->assertStatus(422)->assertJsonValidationErrors(['password']);
    });

    it('rejects mismatched password confirmation', function () {
        $this->postJson('/api/v1/auth/register', [
            'first_name'            => 'Mismatch',
            'last_name'             => 'User',
            'date_of_birth'         => '2000-01-01',
            'email'                 => 'mismatch@beyondtrails.ke',
            'password'              => 'secret123',
            'password_confirmation' => 'different',
        ])->assertStatus(422)->assertJsonValidationErrors(['password']);
    });

    it('rejects invalid email format', function () {
        $this->postJson('/api/v1/auth/register', [
            'first_name'            => 'Bad',
            'last_name'             => 'Email',
            'date_of_birth'         => '2000-01-01',
            'email'                 => 'not-an-email',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertStatus(422)->assertJsonValidationErrors(['email']);
    });
});
