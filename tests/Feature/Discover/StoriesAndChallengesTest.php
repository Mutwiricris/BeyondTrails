<?php

use App\Models\Challenge;
use App\Models\Story;
use App\Models\StoryFrame;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it can fetch dynamic traveller stories', function () {
    $user = User::factory()->create(['display_name' => 'John Storyteller']);
    
    $story = Story::create([
        'user_id' => $user->id,
        'location_name' => 'Samburu',
        'emoji' => '🦓',
    ]);

    StoryFrame::create([
        'story_id' => $story->id,
        'image_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=200',
        'caption' => 'Loving the Samburu safari',
        'duration_seconds' => 5,
    ]);

    $response = $this->getJson('/api/v1/discover/stories');

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment([
            'name' => 'John Storyteller',
            'location' => 'Samburu',
            'emoji' => '🦓',
            'image' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=200',
        ]);
});

test('it can fetch dynamic challenges list', function () {
    Challenge::create([
        'name' => 'Lake Baringo Birdwatch',
        'description' => 'Spot 10 bird species near Lake Baringo.',
        'points_reward' => 100,
        'icon' => '🦅',
        'difficulty' => 'easy',
        'target_value' => 10,
        'type' => 'birds',
    ]);

    $response = $this->getJson('/api/v1/discover/challenges');

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment([
            'name' => 'Lake Baringo Birdwatch',
            'points_reward' => 100,
            'icon' => '🦅',
            'status' => 'not_started',
        ]);
});
