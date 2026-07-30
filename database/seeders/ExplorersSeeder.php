<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ExplorersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Flush cache to ensure seeded updates are visible immediately
        \Illuminate\Support\Facades\Cache::flush();

        // Location 1: User's location (Nairobi)
        $nairobiLat = -1.2841;
        $nairobiLng = 36.8155;

        // Location 2: Kutus
        $kutusLat = -0.5050;
        $kutusLng = 37.2800;

        // Location 3: Rift Valley (Mt. Longonot)
        $riftLat = -0.9167;
        $riftLng = 36.4500;

        $explorers = [
            // Nairobi explorers (user's vicinity)
            ['name' => 'Alex Kimani', 'avatar' => 'https://ui-avatars.com/api/?name=Alex+Kimani&background=0D8ABC&color=fff', 'lat' => $nairobiLat, 'lng' => $nairobiLng, 'city' => 'Nairobi'],
            ['name' => 'Sarah Wanjiku', 'avatar' => 'https://ui-avatars.com/api/?name=Sarah+Wanjiku&background=6B21A8&color=fff', 'lat' => $nairobiLat, 'lng' => $nairobiLng, 'city' => 'Nairobi'],
            ['name' => 'John Doe', 'avatar' => 'https://ui-avatars.com/api/?name=John+Doe&background=059669&color=fff', 'lat' => $nairobiLat, 'lng' => $nairobiLng, 'city' => 'Nairobi'],
            
            // Kutus explorers
            ['name' => 'Mercy Mwende', 'avatar' => 'https://ui-avatars.com/api/?name=Mercy+Mwende&background=D97706&color=fff', 'lat' => $kutusLat, 'lng' => $kutusLng, 'city' => 'Kutus'],
            ['name' => 'Peter Kamau', 'avatar' => 'https://ui-avatars.com/api/?name=Peter+Kamau&background=DC2626&color=fff', 'lat' => $kutusLat, 'lng' => $kutusLng, 'city' => 'Kutus'],
            ['name' => 'Grace Njeri', 'avatar' => 'https://ui-avatars.com/api/?name=Grace+Njeri&background=2563EB&color=fff', 'lat' => $kutusLat, 'lng' => $kutusLng, 'city' => 'Kutus'],
            
            // Rift Valley / Longonot explorers
            ['name' => 'Michael Ochieng', 'avatar' => 'https://ui-avatars.com/api/?name=Michael+Ochieng&background=4F46E5&color=fff', 'lat' => $riftLat, 'lng' => $riftLng, 'city' => 'Naivasha'],
            ['name' => 'Emma Njoroge', 'avatar' => 'https://ui-avatars.com/api/?name=Emma+Njoroge&background=DB2777&color=fff', 'lat' => $riftLat, 'lng' => $riftLng, 'city' => 'Naivasha'],
            ['name' => 'David Kiprono', 'avatar' => 'https://ui-avatars.com/api/?name=David+Kiprono&background=059669&color=fff', 'lat' => $riftLat, 'lng' => $riftLng, 'city' => 'Naivasha'],
        ];

        foreach ($explorers as $index => $explorer) {
            // Add slight random offset to coordinates (approx few hundred meters)
            $latOffset = (rand(-30, 30) / 10000);
            $lngOffset = (rand(-30, 30) / 10000);

            User::updateOrCreate(
                ['email' => 'explorer' . $index . '@example.com'],
                [
                    'name' => $explorer['name'],
                    'username' => Str::slug($explorer['name']) . rand(10,99),
                    'password' => Hash::make('password'),
                    'photo_url' => $explorer['avatar'],
                    'latitude' => $explorer['lat'] + $latOffset,
                    'longitude' => $explorer['lng'] + $lngOffset,
                    'current_activity' => 'rift-valley-panorama', 
                    'location_enabled' => true,
                    'is_profile_public' => true,
                    'city' => $explorer['city'],
                ]
            );
        }

        // Ensure primary Test User exists
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'username' => 'testuser',
                'password' => Hash::make('password'),
            ]
        );

        $firstExplorer = User::where('email', 'explorer0@example.com')->first();
        if ($firstExplorer) {
            \App\Models\Message::firstOrCreate([
                'sender_id' => $firstExplorer->id,
                'receiver_id' => $testUser->id,
                'content' => 'Hey! I see you are also tracking the Rift Valley Panorama. Are you near the second viewpoint?',
            ]);
            
            \App\Models\Message::firstOrCreate([
                'sender_id' => $testUser->id,
                'receiver_id' => $firstExplorer->id,
                'content' => 'Yes, just passed it! The view is amazing.',
            ]);
        }
    }
}
