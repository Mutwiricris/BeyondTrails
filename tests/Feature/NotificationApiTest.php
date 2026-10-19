<?php

use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\HiddenGem;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it can fetch notifications index', function () {
    $response = $this->getJson('/api/v1/notifications');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data',
            'meta' => ['current_page', 'total_items', 'unread_count'],
        ]);
});

test('it can fetch and update notification settings', function () {
    $getRes = $this->getJson('/api/v1/notifications/settings');
    $getRes->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'nearby_gems_enabled' => true,
            ],
        ]);

    $putRes = $this->putJson('/api/v1/notifications/settings', [
        'nearby_gems_enabled' => false,
        'cluster_cooldown_hours' => 6.0,
    ]);

    $putRes->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);
});

test('it responds to location ping correctly', function () {
    $response = $this->postJson('/api/v1/notifications/location-ping', [
        'latitude' => -1.2921,
        'longitude' => 36.8219,
        'speed_kmh' => 5.0,
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'triggered',
        ]);
});

test('it marks a notification as read and can delete it', function () {
    $notification = Notification::create([
        'category' => 'gem',
        'title' => 'Karura Forest Cave',
        'body' => 'You are near Karura Forest Cave',
        'is_read' => false,
    ]);

    $readRes = $this->patchJson("/api/v1/notifications/{$notification->id}/read");
    $readRes->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'is_read' => true,
            ],
        ]);

    $delRes = $this->deleteJson("/api/v1/notifications/{$notification->id}");
    $delRes->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    expect(Notification::find($notification->id))->toBeNull();
});

test('it triggers proximity notification when near an active hidden gem', function () {
    // Create a published hidden gem near Nairobi center
    $gem = HiddenGem::create([
        'slug' => 'nairobi-arboretum-grove',
        'name' => 'Nairobi Arboretum Grove',
        'description' => 'A tranquil sanctuary of indigenous trees and birdlife.',
        'latitude' => -1.2780,
        'longitude' => 36.8080,
        'category' => 'Nature',
        'is_published' => true,
        'rating' => 4.8,
    ]);

    // Send location ping 300 meters away at walking speed
    $response = $this->postJson('/api/v1/notifications/location-ping', [
        'latitude' => -1.2790,
        'longitude' => 36.8090,
        'speed_kmh' => 4.0,
    ]);

    $response->assertStatus(200);
    $data = $response->json();

    expect($data['triggered'])->toBeTrue();
    expect($data['data']['title'])->toBe('Nearby Hidden Gem');
    expect($data['data']['payload']['id'])->toBe($gem->id);
    expect($data['data']['action_buttons'])->toHaveCount(2);
});

