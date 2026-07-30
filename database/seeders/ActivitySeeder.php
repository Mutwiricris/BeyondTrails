<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $organizer = User::first();
        if (!$organizer) {
            $organizer = User::factory()->create(['email' => 'explorer@example.com']);
        }

        $activities = [
            [
                'user_id' => $organizer->id,
                'category' => 'nature',
                'type' => 'hiking',
                'title' => 'Weekend Hike at Mt. Longonot',
                'description' => 'Join me for a challenging but rewarding hike up Mt. Longonot. We will meet at the park gate early morning. Carry enough water and snacks.',
                'location_type' => 'specific',
                'general_area' => 'Rift Valley',
                'location_name' => 'Mt. Longonot National Park',
                'latitude' => -0.9167,
                'longitude' => 36.4500,
                'date' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'time_type' => 'specific',
                'specific_time' => '07:00 AM',
                'duration_hours' => 6,
                'min_age' => 16,
                'max_age' => 60,
                'privacy' => 'open',
                'max_capacity' => 15,
                'join_approval' => 'instant',
                'tags' => json_encode(['hiking', 'nature', 'fitness']),
                'is_host_verified' => true,
                'status' => 'upcoming',
            ],
            [
                'user_id' => $organizer->id,
                'category' => 'culture',
                'type' => 'photowalk',
                'title' => 'Nairobi CBD Architecture Photowalk',
                'description' => 'A casual photowalk around Nairobi CBD exploring old and new architecture. Perfect for beginners and pros alike.',
                'location_type' => 'specific',
                'general_area' => 'Nairobi',
                'location_name' => 'Nairobi CBD (Meet at KICC)',
                'latitude' => -1.2885,
                'longitude' => 36.8231,
                'date' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'time_type' => 'specific',
                'specific_time' => '04:00 PM',
                'duration_hours' => 3,
                'min_age' => 12,
                'max_age' => 80,
                'privacy' => 'open',
                'max_capacity' => 20,
                'join_approval' => 'instant',
                'tags' => json_encode(['photography', 'city', 'culture']),
                'is_host_verified' => true,
                'status' => 'upcoming',
            ]
        ];

        foreach ($activities as $actData) {
            $activity = Activity::create($actData);
            // Add organizer as participant
            $activity->participants()->attach($organizer->id, ['status' => 'joined']);
        }
    }
}
