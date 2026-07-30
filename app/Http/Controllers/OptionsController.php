<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OptionsController extends Controller
{
    public function index()
    {
        return response()->json([
            'activities' => [
                ['label' => 'Safari & Wildlife', 'emoji' => '🦁'],
                ['label' => 'Food & Drinks', 'emoji' => '🍽️'],
                ['label' => 'Nightlife', 'emoji' => '🎉'],
                ['label' => 'Outdoor & Active', 'emoji' => '🥾'],
                ['label' => 'Sightseeing', 'emoji' => '🗺️'],
                ['label' => 'Culture & Arts', 'emoji' => '🎨'],
                ['label' => 'Beach & Water', 'emoji' => '🏖️'],
                ['label' => 'Wellness', 'emoji' => '🧘'],
            ],
            'travel_styles' => [
                ['label' => 'Safari Enthusiast', 'description' => 'Exploring wildlife and national parks', 'emoji' => '🦁'],
                ['label' => 'Beach Lover', 'description' => 'Relaxing by the coast and ocean activities', 'emoji' => '🏖️'],
                ['label' => 'Cultural Explorer', 'description' => 'Discovering local culture and heritage', 'emoji' => '🎨'],
                ['label' => 'Adventure Seeker', 'description' => 'Hiking, climbing, and outdoor activities', 'emoji' => '🏔️'],
                ['label' => 'Business Traveler', 'description' => 'Working while traveling in Kenya', 'emoji' => '💼'],
                ['label' => 'Local Host', 'description' => 'Local who wants to meet travelers', 'emoji' => '🏠'],
            ],
            'interests' => [
                'Wildlife & Safari' => [
                    ['label' => 'Game Drives', 'emoji' => '🚙'],
                    ['label' => 'Wildlife Photography', 'emoji' => '📷'],
                    ['label' => 'Bird Watching', 'emoji' => '🦅'],
                    ['label' => 'Conservation', 'emoji' => '🌿'],
                ],
                'Culture & Arts' => [
                    ['label' => 'Art & Museums', 'emoji' => '🎨'],
                    ['label' => 'Local Culture', 'emoji' => '🌍'],
                    ['label' => 'History', 'emoji' => '🏛️'],
                    ['label' => 'Music & Dance', 'emoji' => '🎵'],
                    ['label' => 'Food Tours', 'emoji' => '🍽️'],
                    ['label' => 'Local Markets', 'emoji' => '🛍️'],
                ],
                'Outdoor & Adventure' => [
                    ['label' => 'Hiking', 'emoji' => '🥾'],
                    ['label' => 'Cycling', 'emoji' => '🚴'],
                    ['label' => 'Running', 'emoji' => '🏃'],
                    ['label' => 'Climbing', 'emoji' => '🧗'],
                    ['label' => 'Camping', 'emoji' => '⛺'],
                    ['label' => 'Water Sports', 'emoji' => '🏄'],
                    ['label' => 'Diving', 'emoji' => '🤿'],
                ],
                'Beach & Coastal' => [
                    ['label' => 'Beach', 'emoji' => '🏖️'],
                    ['label' => 'Snorkeling', 'emoji' => '🤿'],
                    ['label' => 'Sailing', 'emoji' => '⛵'],
                    ['label' => 'Surfing', 'emoji' => '🏄'],
                ],
                'Wellness & Fitness' => [
                    ['label' => 'Yoga', 'emoji' => '🧘'],
                    ['label' => 'Meditation', 'emoji' => '🧘‍♀️'],
                    ['label' => 'Fitness', 'emoji' => '💪'],
                    ['label' => 'Spa', 'emoji' => '💆'],
                ],
            ]
        ]);
    }
}
