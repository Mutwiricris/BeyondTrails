<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('Login', function () {

    it('logs in with correct credentials and returns a token', function () {
        User::factory()->create([
            'email'    => 'user@beyondtrails.ke',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'user@beyondtrails.ke',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email'],
                    'token' => ['access_token', 'token_type', 'expires_at'],
                ],
                'meta',
            ]);
    });

    it('rejects wrong password with 422', function () {
        User::factory()->create([
            'email'    => 'user2@beyondtrails.ke',
            'password' => bcrypt('correct'),
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email'    => 'user2@beyondtrails.ke',
            'password' => 'wrong',
        ])->assertStatus(422)->assertJsonValidationErrors(['email']);
    });

    it('rejects unknown email with 422', function () {
        $this->postJson('/api/v1/auth/login', [
            'email'    => 'nobody@beyondtrails.ke',
            'password' => 'whatever',
        ])->assertStatus(422)->assertJsonValidationErrors(['email']);
    });

    it('rejects missing email', function () {
        $this->postJson('/api/v1/auth/login', [
            'password' => 'password123',
        ])->assertStatus(422)->assertJsonValidationErrors(['email']);
    });

    it('rejects missing password', function () {
        $this->postJson('/api/v1/auth/login', [
            'email' => 'user@beyondtrails.ke',
        ])->assertStatus(422)->assertJsonValidationErrors(['password']);
    });
});

describe('Logout', function () {

    it('logs out and invalidates the token', function () {
        $user = User::factory()->create();
        $tokenResult = $user->createToken('auth_token');
        $token = $tokenResult->plainTextToken;

        // Confirm token exists in DB before logout
        expect($user->tokens()->count())->toBe(1);

        // Logout using withToken helper
        $this->withToken($token)
            ->postJson('/api/v1/auth/logout')
            ->assertStatus(200)
            ->assertJsonFragment(['success' => true]);

        // Token should be deleted from DB
        expect($user->fresh()->tokens()->count())->toBe(0);
    });

    it('cannot logout without a token', function () {
        $this->postJson('/api/v1/auth/logout')
            ->assertStatus(401);
    });
});

describe('Protected routes', function () {

    it('can access /api/v1/auth/me with a valid token', function () {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/auth/me')
            ->assertStatus(200)
            ->assertJsonPath('data.user.email', $user->email);
    });

    it('cannot access /api/v1/auth/me without a token', function () {
        $this->getJson('/api/v1/auth/me')
            ->assertStatus(401);
    });
});
