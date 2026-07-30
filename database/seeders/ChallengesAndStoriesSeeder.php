<?php

namespace Database\Seeders;

use App\Models\Challenge;
use App\Models\Story;
use App\Models\StoryFrame;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ChallengesAndStoriesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Challenges
        $challenges = [
            [
                'name' => 'Mara Explorer',
                'description' => 'Complete a safari route of at least 50km in Maasai Mara.',
                'points_reward' => 150,
                'icon' => '🦁',
                'difficulty' => 'easy',
                'target_value' => 50,
                'type' => 'distance',
            ],
            [
                'name' => 'Diani Wanderer',
                'description' => 'Visit at least 3 hidden gems along Diani Beach.',
                'points_reward' => 200,
                'icon' => '🏖️',
                'difficulty' => 'moderate',
                'target_value' => 3,
                'type' => 'gems_visited',
            ],
            [
                'name' => 'Summit Master',
                'description' => 'Hike a scenic route with over 500m of elevation gain.',
                'points_reward' => 300,
                'icon' => '🥾',
                'difficulty' => 'challenging',
                'target_value' => 500,
                'type' => 'elevation',
            ],
        ];

        foreach ($challenges as $c) {
            Challenge::create($c);
        }

        // 2. Seed stories
        $users = User::all();
        if ($users->isEmpty()) {
            $users = collect([
                User::factory()->create([
                    'name' => 'Amara',
                    'email' => 'amara@beyondtrails.ke',
                ]),
                User::factory()->create([
                    'name' => 'James',
                    'email' => 'james@beyondtrails.ke',
                ]),
                User::factory()->create([
                    'name' => 'Sara',
                    'email' => 'sara@beyondtrails.ke',
                ]),
            ]);
        }

        $names = ['Amara', 'James', 'Sara', 'David', 'Zara'];
        $locations = ['Mara', 'Diani', 'Meru', 'Nakuru', 'Lamu'];
        $emojis = ['🦁', '🏖️', '🌲', '🦒', '⛵'];
        $images = [
            'https://images.unsplash.com/photo-1494790108755-2616b612b5a8?w=200',
            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200',
            'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200',
            'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200',
            'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=200',
        ];

        $userList = $users->values();

        for ($i = 0; $i < 5; $i++) {
            $user = $userList->get($i % $userList->count());
            
            if ($i < count($names)) {
                $user->update(['display_name' => $names[$i]]);
            }

            $story = Story::create([
                'user_id' => $user->id,
                'location_name' => $locations[$i],
                'emoji' => $emojis[$i],
            ]);

            StoryFrame::create([
                'story_id' => $story->id,
                'image_url' => $images[$i],
                'caption' => 'Loving the vibes at ' . $locations[$i],
                'duration_seconds' => 5,
            ]);
        }
    }
}
