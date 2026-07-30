<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it can fetch people nearby within radius', function () {
    // Create requesting user
    $me = User::factory()->create([
        'latitude' => -1.2921,
        'longitude' => 36.8219,
        'location_enabled' => true,
        'is_profile_public' => true,
    ]);

    // Create a user nearby (within 10km)
    $nearUser = User::factory()->create([
        'display_name' => 'John Near',
        'latitude' => -1.2500, // ~6km away
        'longitude' => 36.8000,
        'location_enabled' => true,
        'is_profile_public' => true,
        'current_activity' => 'hiking',
    ]);

    // Create a user far away (more than 50km)
    $farUser = User::factory()->create([
        'display_name' => 'Jane Far',
        'latitude' => -2.0000, // ~80km away
        'longitude' => 37.0000,
        'location_enabled' => true,
        'is_profile_public' => true,
    ]);

    // Create a private user nearby (should be hidden)
    $privateUser = User::factory()->create([
        'display_name' => 'Secret User',
        'latitude' => -1.2500,
        'longitude' => 36.8000,
        'location_enabled' => true,
        'is_profile_public' => false,
    ]);

    Sanctum::actingAs($me);

    $response = $this->getJson('/api/v1/discover/people-nearby?latitude=-1.2921&longitude=36.8219&radius=50');

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonCount(2, 'data') // Includes $me + $nearUser
        ->assertJsonFragment([
            'name' => 'John Near',
            'current_activity' => 'hiking',
        ]);
});
