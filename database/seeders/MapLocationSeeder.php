<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MapLocation;

class MapLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $places = [
            // Hidden Gems
            [
                'title' => 'Hell\'s Gate Gorge',
                'location_name' => 'Naivasha',
                'latitude' => -0.8997,
                'longitude' => 36.3197,
                'category' => 'Gorge',
                'rating' => 4.8,
                'description' => 'Spectacular natural gorge with hot springs',
                'type' => 'Hidden Gems',
                'images' => [
                    'https://images.unsplash.com/photo-1547471080-7bc2caa7eee3?w=800',
                    'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=800',
                    'https://images.unsplash.com/photo-1433086966358-54859d0ed716?w=800',
                ],
            ],
            [
                'title' => 'Menengai Crater',
                'location_name' => 'Nakuru',
                'latitude' => -0.2035,
                'longitude' => 36.0883,
                'category' => 'Crater',
                'rating' => 4.6,
                'description' => 'Massive shield volcano crater with scenic views',
                'type' => 'Hidden Gems',
                'images' => [
                    'https://images.unsplash.com/photo-1506744626753-1fa44df31c7f?w=800',
                    'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=800',
                    'https://images.unsplash.com/photo-1454496522488-7a8e488e8606?w=800',
                ],
            ],
            [
                'title' => 'Chale Island',
                'location_name' => 'Kwale',
                'latitude' => -4.4333,
                'longitude' => 39.5333,
                'category' => 'Island',
                'rating' => 4.9,
                'description' => 'Secluded private island resort',
                'type' => 'Hidden Gems',
                'images' => [
                    'https://images.unsplash.com/photo-1499793983690-e29da59ef1c2?w=800',
                    'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800',
                    'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?w=800',
                ],
            ],
            
            // Tours
            [
                'title' => 'Maasai Mara Safari',
                'location_name' => 'Narok',
                'latitude' => -1.4061,
                'longitude' => 35.0061,
                'category' => 'Safari',
                'rating' => 4.9,
                'description' => '3-day migration experience',
                'type' => 'Tours',
                'images' => [
                    'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=800',
                    'https://images.unsplash.com/photo-1547970810-dc1e684757a3?w=800',
                    'https://images.unsplash.com/photo-1535338454770-7a26e296c78a?w=800',
                ],
            ],
            [
                'title' => 'Nairobi City Walk',
                'location_name' => 'CBD',
                'latitude' => -1.2864,
                'longitude' => 36.8172,
                'category' => 'City Tour',
                'rating' => 4.5,
                'description' => 'Historical architecture tour',
                'type' => 'Tours',
                'images' => [
                    'https://images.unsplash.com/photo-1589309736404-2e142a2acdf0?w=800',
                    'https://images.unsplash.com/photo-1574516346387-a3a830b88d8b?w=800',
                    'https://images.unsplash.com/photo-1516468494951-419b6da88da4?w=800',
                ],
            ],
            [
                'title' => 'Dhow Cruise',
                'location_name' => 'Mombasa',
                'latitude' => -4.0435,
                'longitude' => 39.6682,
                'category' => 'Boat Tour',
                'rating' => 4.8,
                'description' => 'Sunset dinner cruise on a traditional dhow',
                'type' => 'Tours',
                'images' => [
                    'https://images.unsplash.com/photo-1566418776856-788ff3f3a9e3?w=800',
                    'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=800',
                    'https://images.unsplash.com/photo-1505142468610-359e7d316be0?w=800',
                ],
            ],

            // Historic
            [
                'title' => 'Fort Jesus',
                'location_name' => 'Mombasa',
                'latitude' => -4.0628,
                'longitude' => 39.6796,
                'category' => 'Monument',
                'rating' => 4.7,
                'description' => '16th-century Portuguese fort',
                'type' => 'Historic',
                'images' => [
                    'https://images.unsplash.com/photo-1600754823158-b68df7e6d0a7?w=800',
                    'https://images.unsplash.com/photo-1596706935293-2782b6c3104e?w=800',
                    'https://images.unsplash.com/photo-1549488344-1f9b8d2bd1f3?w=800',
                ],
            ],
            [
                'title' => 'Gedi Ruins',
                'location_name' => 'Watamu',
                'latitude' => -3.3083,
                'longitude' => 40.0167,
                'category' => 'Ruins',
                'rating' => 4.6,
                'description' => 'Remains of a Swahili town',
                'type' => 'Historic',
                'images' => [
                    'https://images.unsplash.com/photo-1585214165382-0f5b7e8fa73f?w=800',
                    'https://images.unsplash.com/photo-1578331815944-28678e50f306?w=800',
                    'https://images.unsplash.com/photo-1571201086995-ef2d15c0f76c?w=800',
                ],
            ],
            [
                'title' => 'Koobi Fora',
                'location_name' => 'Lake Turkana',
                'latitude' => 4.0500,
                'longitude' => 36.2167,
                'category' => 'Museum',
                'rating' => 4.8,
                'description' => 'Cradle of mankind archaeological site',
                'type' => 'Historic',
                'images' => [
                    'https://images.unsplash.com/photo-1534067783941-51c9c23ecefd?w=800',
                    'https://images.unsplash.com/photo-1534447677768-be436bb09401?w=800',
                    'https://images.unsplash.com/photo-1506477331477-33d5d8b3dc85?w=800',
                ],
            ],

            // Destinations
            [
                'title' => 'Diani Beach',
                'location_name' => 'South Coast',
                'latitude' => -4.2967,
                'longitude' => 39.5775,
                'category' => 'Beach',
                'rating' => 4.8,
                'description' => 'Pristine white sand beaches',
                'type' => 'Destinations',
                'images' => [
                    'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=800',
                    'https://images.unsplash.com/photo-1505142468610-359e7d316be0?w=800',
                    'https://images.unsplash.com/photo-1473496169904-658ba7c44d8a?w=800',
                ],
            ],
            [
                'title' => 'Mount Kenya',
                'location_name' => 'Central Kenya',
                'latitude' => -0.1521,
                'longitude' => 37.3084,
                'category' => 'Mountain',
                'rating' => 4.9,
                'description' => 'Africa\'s second highest peak',
                'type' => 'Destinations',
                'images' => [
                    'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800',
                    'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=800',
                    'https://images.unsplash.com/photo-1454496522488-7a8e488e8606?w=800',
                ],
            ],
            [
                'title' => 'Lamu Old Town',
                'location_name' => 'Lamu',
                'latitude' => -2.2717,
                'longitude' => 40.9020,
                'category' => 'Town',
                'rating' => 4.7,
                'description' => 'UNESCO Swahili settlement',
                'type' => 'Destinations',
                'images' => [
                    'https://images.unsplash.com/photo-1585214165382-0f5b7e8fa73f?w=800',
                    'https://images.unsplash.com/photo-1578331815944-28678e50f306?w=800',
                    'https://images.unsplash.com/photo-1571201086995-ef2d15c0f76c?w=800',
                ],
            ],

            // Nature
            [
                'title' => 'Kakamega Forest',
                'location_name' => 'Western Kenya',
                'latitude' => 0.3000,
                'longitude' => 34.8500,
                'category' => 'Forest',
                'rating' => 4.6,
                'description' => 'Last remaining rainforest',
                'type' => 'Nature',
                'images' => [
                    'https://images.unsplash.com/photo-1511497584788-876760111969?w=800',
                    'https://images.unsplash.com/photo-1542273917363-3b1817f69a2d?w=800',
                    'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=800',
                ],
            ],
            [
                'title' => 'Tsavo National Park',
                'location_name' => 'Coast Province',
                'latitude' => -3.0000,
                'longitude' => 38.5000,
                'category' => 'Park',
                'rating' => 4.7,
                'description' => 'Largest national park in Kenya',
                'type' => 'Nature',
                'images' => [
                    'https://images.unsplash.com/photo-1547970810-dc1e684757a3?w=800',
                    'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=800',
                    'https://images.unsplash.com/photo-1535338454770-7a26e296c78a?w=800',
                ],
            ],
        ];

        foreach ($places as $place) {
            MapLocation::create($place);
        }
    }
}
