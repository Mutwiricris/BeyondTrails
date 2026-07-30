<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Destination;
use App\Models\DestinationItinerary;
use App\Models\DestinationAccommodation;
use App\Models\HiddenGem;
use App\Models\NearbyAttraction;
use App\Models\LocalTip;
use App\Models\Operator;
use App\Models\DiscoverRoute;
use App\Models\RouteSegment;
use Illuminate\Support\Str;

/**
 * DiscoverSeeder
 *
 * Seeds all data from the Flutter app's hardcoded lists in:
 *   - comprehensive_browse_screen.dart (_destinations, _gems, _operators)
 *   - operator_data.dart (getMockOperators)
 *   - hidden_gem.dart (MockGemsData.getMockGems)
 *   - route.dart (MockRouteData.getMockRoutes)
 *
 * Plus full itinerary, accommodation, tips, and nearby data
 * for each destination and gem (mirroring safari_details_screen.dart
 * and gem_details_screen.dart).
 */
class DiscoverSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedOperators();
        $this->seedDestinations();
        $this->seedHiddenGems();
        $this->seedRoutes();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // OPERATORS  (from operator_data.dart + browse screen)
    // ──────────────────────────────────────────────────────────────────────────
    private function seedOperators(): void
    {
        $operators = [
            [
                'slug' => 'safari-adventures-ltd',
                'name' => 'Safari Adventures Ltd',
                'tagline' => 'Your Gateway to Wild Kenya',
                'description' => "Premier safari operator offering authentic wildlife experiences across Kenya's most spectacular national parks and reserves.",
                'logo_url' => 'https://ui-avatars.com/api/?name=Safari+Adventures&background=0D8ABC&color=fff',
                'cover_image_url' => 'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=800',
                'gallery' => [
                    'https://images.unsplash.com/photo-1547471080-7cc2caa01a7e?w=800',
                    'https://images.unsplash.com/photo-1534445538923-14f2cd67e636?w=800',
                    'https://images.unsplash.com/photo-1535338268104-3b4ac09f3b1a?w=800',
                ],
                'email' => 'info@safariadventures.co.ke',
                'phone' => '+254 712 345 678',
                'website' => 'www.safariadventures.co.ke',
                'address' => 'Kenyatta Avenue, Nairobi',
                'city' => 'Nairobi',
                'country' => 'Kenya',
                'business_type' => 'Tour Operator',
                'specializations' => ['Safari Tours', 'Wildlife Photography', 'Cultural Tours'],
                'certifications' => ['KTB Certified', 'Eco-Tourism Award 2023', 'Safety Excellence'],
                'services' => ['Tours', 'Transport', 'Guide Services'],
                'languages' => ['English', 'Swahili', 'German', 'French'],
                'is_verified' => true,
                'verification_badge' => 'Kenya Tourism Board Certified',
                'license_number' => 'KTB-2018-0542',
                'rating' => 4.9,
                'review_count' => 1240,
                'total_bookings' => 1250,
                'tours_offered' => 45,
                'member_since' => '2018-03-15',
                'is_featured' => true,
            ],
            [
                'slug' => 'wilderness-expeditions',
                'name' => 'Wilderness Expeditions',
                'tagline' => 'Premium Luxury Safaris',
                'description' => 'Premium luxury safari operator delivering world-class wildlife experiences with the highest standards of comfort.',
                'logo_url' => 'https://i.pravatar.cc/200?img=7',
                'cover_image_url' => 'https://images.unsplash.com/photo-1549366021-9f761d450615?w=800',
                'email' => 'hello@wildernessexpeditions.co.ke',
                'phone' => '+254 733 987 654',
                'website' => 'www.wildernessexpeditions.co.ke',
                'city' => 'Nairobi', 'country' => 'Kenya',
                'business_type' => 'Tour Operator',
                'specializations' => ['Luxury Safaris', 'Wildlife', 'Photography Safaris'],
                'certifications' => ['KATO Member', 'TripAdvisor Excellence'],
                'services' => ['Tours', 'Accommodation', 'Transport'],
                'languages' => ['English', 'Swahili', 'French'],
                'is_verified' => true,
                'verification_badge' => 'KATO Certified',
                'rating' => 4.9,
                'review_count' => 1456,
                'total_bookings' => 3200,
                'tours_offered' => 52,
                'member_since' => '2016-01-10',
                'is_featured' => true,
            ],
            [
                'slug' => 'serena-hotels-resorts',
                'name' => 'Serena Hotels & Resorts',
                'tagline' => 'Luxury in the Wild',
                'description' => "Luxury accommodations in Kenya's most breathtaking locations. Experience world-class hospitality in the heart of nature.",
                'logo_url' => 'https://i.pravatar.cc/200?img=2',
                'cover_image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800',
                'email' => 'reservations@serena.co.ke',
                'phone' => '+254 720 123 456',
                'website' => 'www.serenahotels.com',
                'address' => 'Westlands, Nairobi',
                'city' => 'Nairobi', 'country' => 'Kenya',
                'business_type' => 'Hotel & Resort',
                'specializations' => ['Luxury Lodges', 'Safari Camps', 'Beach Resorts'],
                'certifications' => ['5-Star Rating', 'Green Key Award', 'TripAdvisor Excellence'],
                'services' => ['Accommodation', 'Tours', 'Spa', 'Fine Dining'],
                'languages' => ['English', 'Swahili', 'Italian', 'Spanish'],
                'is_verified' => true,
                'verification_badge' => '5-Star Certified',
                'rating' => 4.9,
                'review_count' => 856,
                'total_bookings' => 3420,
                'tours_offered' => 5,
                'accommodations_offered' => 12,
                'member_since' => '2015-01-10',
                'is_featured' => true,
            ],
            [
                'slug' => 'mountain-guides-co',
                'name' => 'Mountain Guides Co',
                'tagline' => 'Expert Mountain Experiences',
                'description' => 'Expert mountain guides offering certified hiking and climbing experiences across Kenya\'s highland terrain.',
                'logo_url' => 'https://i.pravatar.cc/200?img=3',
                'cover_image_url' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800',
                'email' => 'climb@mountainguides.co.ke',
                'phone' => '+254 721 567 890',
                'city' => 'Nanyuki', 'country' => 'Kenya',
                'business_type' => 'Tour Operator',
                'specializations' => ['Mountain Hiking', 'Rock Climbing', 'High Altitude'],
                'services' => ['Tours', 'Guide Services', 'Equipment Rental'],
                'languages' => ['English', 'Swahili'],
                'is_verified' => true,
                'verification_badge' => 'KMC Certified',
                'rating' => 4.8,
                'review_count' => 856,
                'tours_offered' => 32,
                'member_since' => '2019-05-20',
            ],
            [
                'slug' => 'beach-adventures',
                'name' => 'Beach Adventures',
                'tagline' => 'Coastal Adventures Specialists',
                'description' => 'Specialists in coastal and beach adventures along Kenya\'s stunning Indian Ocean coastline.',
                'logo_url' => 'https://i.pravatar.cc/200?img=4',
                'cover_image_url' => 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=800',
                'email' => 'hello@beachadventures.co.ke',
                'phone' => '+254 741 234 567',
                'city' => 'Mombasa', 'country' => 'Kenya',
                'business_type' => 'Tour Operator',
                'specializations' => ['Beach Tours', 'Water Sports', 'Marine Excursions'],
                'services' => ['Tours', 'Water Sports', 'Accommodation'],
                'languages' => ['English', 'Swahili', 'German'],
                'is_verified' => true,
                'verification_badge' => 'KTB Certified',
                'rating' => 4.7,
                'review_count' => 634,
                'tours_offered' => 28,
                'member_since' => '2020-02-15',
            ],
            [
                'slug' => 'culture-tours-kenya',
                'name' => 'Culture Tours Kenya',
                'tagline' => 'Authentic Cultural Experiences',
                'description' => 'Delivering authentic Kenyan cultural experiences connecting travellers with local communities.',
                'logo_url' => 'https://i.pravatar.cc/200?img=5',
                'cover_image_url' => 'https://images.unsplash.com/photo-1523805009345-7448845a9e53?w=800',
                'email' => 'info@culturetourskenya.com',
                'phone' => '+254 722 876 543',
                'city' => 'Nairobi', 'country' => 'Kenya',
                'business_type' => 'Tour Operator',
                'specializations' => ['Cultural Tours', 'Heritage Sites', 'Village Experiences'],
                'services' => ['Tours', 'Cultural Visits', 'Guide Services'],
                'languages' => ['English', 'Swahili', 'Maa', 'Kikuyu'],
                'is_verified' => true,
                'verification_badge' => 'KTB Certified',
                'rating' => 4.9,
                'review_count' => 945,
                'tours_offered' => 38,
                'member_since' => '2017-08-01',
                'is_featured' => true,
            ],
            [
                'slug' => 'urban-explorer',
                'name' => 'Urban Explorer',
                'tagline' => 'City Tours & Nightlife',
                'description' => 'Specialised Nairobi city tours including nightlife, food scenes, and urban culture experiences.',
                'logo_url' => 'https://i.pravatar.cc/200?img=6',
                'cover_image_url' => 'https://images.unsplash.com/photo-1611348524140-53c9a25263d6?w=800',
                'email' => 'hello@urbanexplorer.co.ke',
                'phone' => '+254 712 111 222',
                'city' => 'Nairobi', 'country' => 'Kenya',
                'business_type' => 'Tour Operator',
                'specializations' => ['City Tours', 'Food Tours', 'Nightlife'],
                'services' => ['Tours', 'Transport'],
                'languages' => ['English', 'Swahili'],
                'is_verified' => true,
                'rating' => 4.5,
                'review_count' => 567,
                'tours_offered' => 18,
                'member_since' => '2021-04-10',
            ],
        ];

        foreach ($operators as $data) {
            Operator::updateOrCreate(['slug' => $data['slug']], $data);
        }

        $this->command?->info('✅ Operators seeded: ' . count($operators));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // DESTINATIONS  (from browse screen + safari_details_screen defaults)
    // ──────────────────────────────────────────────────────────────────────────
    private function seedDestinations(): void
    {
        $safariOp   = Operator::where('slug', 'safari-adventures-ltd')->first();
        $wildernessOp = Operator::where('slug', 'wilderness-expeditions')->first();
        $mountainOp = Operator::where('slug', 'mountain-guides-co')->first();
        $beachOp    = Operator::where('slug', 'beach-adventures')->first();
        $cultureOp  = Operator::where('slug', 'culture-tours-kenya')->first();
        $urbanOp    = Operator::where('slug', 'urban-explorer')->first();

        $destinations = [
            [
                'slug' => 'maasai-mara-safari',
                'title' => 'Maasai Mara Safari',
                'description' => "Experience the world-famous Maasai Mara — home of the Big Five and the spectacular Great Migration. Our curated 3-day safari takes you deep into the heart of the Mara ecosystem with expert guides and luxury tented camps.",
                'short_description' => 'World-famous wildlife reserve, home of the Big Five and Great Migration.',
                'category' => 'Wildlife',
                'location' => 'Narok County',
                'county' => 'Narok',
                'region' => 'Rift Valley',
                'latitude' => -1.5060,
                'longitude' => 35.1432,
                'cover_image_url' => 'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=800',
                'gallery' => [
                    'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=800',
                    'https://images.unsplash.com/photo-1547471080-7cc2caa01a7e?w=800',
                    'https://images.unsplash.com/photo-1534445538923-14f2cd67e636?w=800',
                    'https://images.unsplash.com/photo-1549366021-9f761d450615?w=800',
                ],
                'price_kes' => 45000,
                'price_usd' => 350.00,
                'rating' => 4.9,
                'review_count' => 487,
                'duration_days' => 3,
                'duration_label' => '3 Days',
                'group_size_max' => 6,
                'difficulty' => 'Moderate',
                'tour_type' => 'Safari',
                'is_popular' => true,
                'is_featured' => true,
                'operator_id' => $safariOp?->id,
                'highlights' => ['Big Five game viewing', 'Great Migration (Jul–Oct)', 'Sundowner at Mara River', 'Visit to Maasai village', 'Professional photography guide', 'Luxury safari accommodation'],
                'included' => ['All park entry fees', 'Professional safari guide', 'Game drives in 4x4 vehicle', 'Accommodation for 2 nights', 'All meals as per itinerary', 'Bottled water during game drives', 'Airport/hotel pickup & drop-off'],
                'excluded' => ['International flights', 'Travel insurance', 'Personal expenses', 'Tips and gratuities', 'Alcoholic beverages', 'Optional activities'],
                'what_to_bring' => ['Neutral-coloured clothing', 'Camera with zoom lens', 'Binoculars', 'Sunscreen & hat', 'Light jacket (mornings are cool)', 'Insect repellent'],
                'languages_spoken' => ['English', 'Swahili', 'German', 'French'],
                'meeting_point' => 'Wilson Airport, Nairobi or your hotel lobby',
                'meeting_lat' => -1.3220,
                'meeting_lng' => 36.8145,
                'transport_info' => ['type' => 'Toyota Land Cruiser 4x4', 'features' => ['Pop-up roof for game viewing', 'Air conditioning', 'Charging ports', 'Cooler box'], 'seats' => 'Maximum 6 passengers per vehicle'],
                'meal_info' => ['dietary' => 'Vegetarian, vegan, and special dietary requirements accommodated', 'style' => 'Buffet breakfast and dinner, packed lunch on safari', 'beverages' => 'Tea, coffee, and water included. Soft drinks and alcohol available for purchase'],
                'health_safety_info' => ['vaccinations' => 'Yellow fever recommended. Consult doctor about malaria prophylaxis', 'what_to_bring_medically' => 'Personal medication, mosquito repellent'],
                'cancellation_policy' => 'Free cancellation up to 7 days before departure. 50% refund 3-7 days before. No refund within 3 days.',
                'faqs' => [
                    ['question' => 'What is the best time to visit?', 'answer' => 'The Great Migration typically occurs July–October. However, the Mara offers excellent wildlife viewing year-round.'],
                    ['question' => 'Is this suitable for children?', 'answer' => 'Yes, children of all ages are welcome. We recommend ages 5+ for long game drives.'],
                    ['question' => 'What should I wear?', 'answer' => 'Neutral-coloured clothing (khaki, brown, green). Layers for cool mornings.'],
                    ['question' => 'Do I need vaccinations?', 'answer' => 'Yellow fever recommended. Consult your doctor about malaria prophylaxis.'],
                ],
                'xp_reward' => 250,
            ],
            [
                'slug' => 'amboseli-elephant-safari',
                'title' => 'Amboseli Elephant Safari',
                'description' => "Marvel at Africa's largest elephant herds against the backdrop of snow-capped Mount Kilimanjaro. Amboseli is one of Kenya's best wildlife destinations for unobstructed savannah views.",
                'short_description' => 'Africa\'s largest elephant herds below Mt Kilimanjaro.',
                'category' => 'Wildlife',
                'location' => 'Amboseli',
                'county' => 'Kajiado',
                'region' => 'Rift Valley',
                'latitude' => -2.6527,
                'longitude' => 37.2606,
                'cover_image_url' => 'https://images.unsplash.com/photo-1564760055775-d63b17a55c44?w=800',
                'gallery' => ['https://images.unsplash.com/photo-1564760055775-d63b17a55c44?w=800', 'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=800'],
                'price_kes' => 42000,
                'price_usd' => 325.00,
                'rating' => 4.9,
                'review_count' => 389,
                'duration_days' => 3,
                'duration_label' => '3 Days',
                'group_size_max' => 6,
                'difficulty' => 'Easy',
                'tour_type' => 'Safari',
                'is_popular' => true,
                'is_featured' => true,
                'operator_id' => $wildernessOp?->id,
                'highlights' => ['Elephant herds with Kilimanjaro backdrop', 'Observation Hill panoramic views', 'Maasai cultural visit', 'Big Five sightings', 'Wetland bird watching'],
                'included' => ['Park fees', 'Expert guide', '4x4 game drive vehicle', '2-night accommodation', 'All meals', 'Transfers'],
                'excluded' => ['Flights', 'Travel insurance', 'Tips', 'Personal expenses'],
                'xp_reward' => 240,
            ],
            [
                'slug' => 'tsavo-wildlife-safari',
                'title' => 'Tsavo Wildlife Safari',
                'description' => "Explore Kenya's largest national park — the vast red-earthed savannah of Tsavo East and West. Famous for its red elephants, Lugard Falls, and incredible biodiversity.",
                'short_description' => 'Kenya\'s largest national park — red elephants and vast savannah.',
                'category' => 'Wildlife',
                'location' => 'Tsavo National Park',
                'county' => 'Taita Taveta',
                'region' => 'Coastal',
                'latitude' => -3.0789,
                'longitude' => 38.4697,
                'cover_image_url' => 'https://images.unsplash.com/photo-1547471080-7cc2caa01a7e?w=800',
                'gallery' => ['https://images.unsplash.com/photo-1547471080-7cc2caa01a7e?w=800', 'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=800'],
                'price_kes' => 35000,
                'price_usd' => 270.00,
                'rating' => 4.8,
                'review_count' => 342,
                'duration_days' => 3,
                'duration_label' => '3 Days',
                'group_size_max' => 7,
                'difficulty' => 'Easy',
                'tour_type' => 'Safari',
                'is_popular' => true,
                'operator_id' => $safariOp?->id,
                'highlights' => ['Red elephants (iron-rich soil)', 'Lugard Falls', 'Mzima Springs', 'Bird watching', 'Night game drives'],
                'included' => ['Park fees', 'Professional guide', 'Game drives', '2-night lodge stay', 'All meals', 'Transfers from Nairobi or Mombasa'],
                'excluded' => ['Flights', 'Travel insurance', 'Tips'],
                'xp_reward' => 220,
            ],
            [
                'slug' => 'diani-beach-resort',
                'title' => 'Diani Beach Resort',
                'description' => "Relax on one of Africa's most beautiful beaches — pristine white sands, crystal-clear turquoise waters, and world-class resorts. Diani is Kenya's premier beach destination.",
                'short_description' => 'Pristine white sand beaches on Kenya\'s stunning South Coast.',
                'category' => 'Beach',
                'location' => 'South Coast',
                'county' => 'Kwale',
                'region' => 'Coast',
                'latitude' => -4.2763,
                'longitude' => 39.5943,
                'cover_image_url' => 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=800',
                'gallery' => ['https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=800'],
                'price_kes' => 28000,
                'price_usd' => 215.00,
                'rating' => 4.8,
                'review_count' => 298,
                'duration_days' => 4,
                'duration_label' => '4 Days',
                'group_size_max' => 10,
                'difficulty' => 'Easy',
                'tour_type' => 'Beach',
                'is_popular' => true,
                'is_featured' => true,
                'operator_id' => $beachOp?->id,
                'highlights' => ['14km of white sand beach', 'Snorkelling & scuba diving', 'Colobus monkey sanctuary', 'Dhow cruise at sunset', 'Deep sea fishing', 'Kitesurfing'],
                'included' => ['Beach resort accommodation', 'Daily breakfast', 'Beach activities', 'Airport transfer'],
                'excluded' => ['Flights', 'Lunch & dinner', 'Optional water sports'],
                'xp_reward' => 180,
            ],
            [
                'slug' => 'mount-kenya-hiking',
                'title' => 'Mount Kenya Hiking',
                'description' => "Summit Africa's second-highest peak and experience the unique mountain ecosystem — bamboo forests, glaciers, and moorlands with stunning views from the top.",
                'short_description' => 'Africa\'s second-highest peak with diverse ecosystems.',
                'category' => 'Mountain',
                'location' => 'Central Kenya',
                'county' => 'Nyeri',
                'region' => 'Central',
                'latitude' => -0.1521,
                'longitude' => 37.3082,
                'cover_image_url' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800',
                'gallery' => ['https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800'],
                'price_kes' => 18000,
                'price_usd' => 140.00,
                'rating' => 4.7,
                'review_count' => 156,
                'duration_days' => 5,
                'duration_label' => '5 Days',
                'group_size_max' => 8,
                'difficulty' => 'Difficult',
                'tour_type' => 'Hiking',
                'is_popular' => false,
                'operator_id' => $mountainOp?->id,
                'highlights' => ['Point Lenana summit (4,985m)', 'Bamboo forest zone', 'Alpine moorland', 'Glacier views', 'Diverse wildlife', 'Star gazing at altitude'],
                'included' => ['Park entry fees', 'Certified mountain guide', 'Porter services', 'Camping/hut accommodation', 'All meals on mountain', 'Safety equipment'],
                'excluded' => ['Personal climbing gear', 'Travel insurance', 'Tips'],
                'xp_reward' => 400,
            ],
            [
                'slug' => 'lamu-island-culture',
                'title' => 'Lamu Island Culture',
                'description' => "Step back in time on Lamu Island — the oldest continuously inhabited town in East Africa. UNESCO World Heritage Site with authentic Swahili architecture, dhow sailing, and no cars.",
                'short_description' => 'UNESCO World Heritage — East Africa\'s oldest inhabited town.',
                'category' => 'Culture',
                'location' => 'Lamu',
                'county' => 'Lamu',
                'region' => 'Coast',
                'latitude' => -2.2717,
                'longitude' => 40.9022,
                'cover_image_url' => 'https://images.unsplash.com/photo-1523805009345-7448845a9e53?w=800',
                'gallery' => ['https://images.unsplash.com/photo-1523805009345-7448845a9e53?w=800'],
                'price_kes' => 20000,
                'price_usd' => 155.00,
                'rating' => 4.6,
                'review_count' => 223,
                'duration_days' => 4,
                'duration_label' => '4 Days',
                'group_size_max' => 8,
                'difficulty' => 'Easy',
                'tour_type' => 'Cultural',
                'is_popular' => false,
                'is_featured' => true,
                'operator_id' => $cultureOp?->id,
                'highlights' => ['Lamu Old Town (UNESCO)', 'Traditional dhow sailing', 'Donkey sanctuary visit', 'Swahili cooking class', 'Manda Island beach', 'Fort Lamu museum'],
                'included' => ['Guesthouse accommodation', 'Daily breakfast', 'Dhow trip', 'Walking tour with guide', 'Ferry transfers'],
                'excluded' => ['Flights', 'Lunch & dinner', 'Personal expenses'],
                'xp_reward' => 200,
            ],
            [
                'slug' => 'nairobi-city-tour',
                'title' => 'Nairobi City Tour',
                'description' => "Discover the surprising depth of Nairobi — Africa's most vibrant capital. From the Giraffe Centre and Nairobi National Park to street food tours and rooftop bars.",
                'short_description' => 'Africa\'s most vibrant capital city — wildlife, culture, and cuisine.',
                'category' => 'City',
                'location' => 'Nairobi',
                'county' => 'Nairobi',
                'region' => 'Central',
                'latitude' => -1.2921,
                'longitude' => 36.8219,
                'cover_image_url' => 'https://images.unsplash.com/photo-1611348524140-53c9a25263d6?w=800',
                'gallery' => ['https://images.unsplash.com/photo-1611348524140-53c9a25263d6?w=800'],
                'price_kes' => 5000,
                'price_usd' => 39.00,
                'rating' => 4.5,
                'review_count' => 445,
                'duration_days' => 1,
                'duration_label' => '1 Day',
                'group_size_max' => 12,
                'difficulty' => 'Easy',
                'tour_type' => 'City Tour',
                'is_popular' => true,
                'is_featured' => false,
                'operator_id' => $urbanOp?->id,
                'highlights' => ['Nairobi National Park', 'David Sheldrick Elephant Orphanage', 'Giraffe Centre', 'Karen Blixen Museum', 'Westgate food court tour', 'Sunset rooftop bar'],
                'included' => ['Hotel pick-up & drop-off', 'Expert city guide', 'All entrance fees', 'Lunch at local restaurant', 'Transport in comfortable minivan'],
                'excluded' => ['Personal shopping', 'Drinks at bars', 'Tips'],
                'xp_reward' => 100,
            ],
            [
                'slug' => 'hells-gate-adventure',
                'title' => "Hell's Gate Adventure",
                'description' => "Cycle, hike, and rock-climb in one of Kenya's most dramatic landscapes. Hell's Gate National Park inspired The Lion King's Pride Rock and offers the rare chance to walk and cycle among wildlife.",
                'short_description' => 'Cycle among giraffes — the park that inspired The Lion King.',
                'category' => 'Adventure',
                'location' => 'Nakuru',
                'county' => 'Nakuru',
                'region' => 'Rift Valley',
                'latitude' => -0.9047,
                'longitude' => 36.3091,
                'cover_image_url' => 'https://images.unsplash.com/photo-1589182373726-e4f658ab50f0?w=800',
                'gallery' => ['https://images.unsplash.com/photo-1589182373726-e4f658ab50f0?w=800'],
                'price_kes' => 8000,
                'price_usd' => 62.00,
                'rating' => 4.7,
                'review_count' => 267,
                'duration_days' => 1,
                'duration_label' => '1 Day',
                'group_size_max' => 10,
                'difficulty' => 'Moderate',
                'tour_type' => 'Adventure',
                'is_popular' => false,
                'operator_id' => $mountainOp?->id,
                'highlights' => ["Fischer's Tower rock column", 'Gorge walk (Ol Njorowa)', 'Cycling among wildlife', 'Hot geothermal springs', 'Masai Mara viewpoints', 'Olkaria Geothermal Spa'],
                'included' => ['Park entry fees', 'Bicycle hire', 'Expert guide', 'Transport from Naivasha', 'Safety equipment'],
                'excluded' => ['Spa fees', 'Personal lunch', 'Tips'],
                'xp_reward' => 150,
            ],
        ];

        foreach ($destinations as $data) {
            $destination = Destination::updateOrCreate(['slug' => $data['slug']], $data);

            // Seed itinerary for each destination
            $this->seedDestinationItinerary($destination);
            // Seed accommodations
            $this->seedDestinationAccommodations($destination);
        }

        $this->command?->info('✅ Destinations seeded: ' . count($destinations));
    }

    private function seedDestinationItinerary($destination): void
    {
        DestinationItinerary::where('destination_id', $destination->id)->delete();

        $itineraries = [
            'maasai-mara-safari' => [
                ['day_number' => 1, 'title' => 'Arrival & Evening Game Drive', 'description' => 'Pick-up from Nairobi or Wilson Airport. Drive to Maasai Mara (approx 5 hours). Check-in to tented camp. Evening game drive along the Mara River.', 'meals' => ['Lunch', 'Dinner']],
                ['day_number' => 2, 'title' => 'Full Day Safari', 'description' => 'Full day game viewing across the Mara Plains. Watch the Big Five in their natural habitat. Visit the Mara River crossing during migration season. Sundowner by the river.', 'meals' => ['Breakfast', 'Lunch', 'Dinner']],
                ['day_number' => 3, 'title' => 'Morning Drive & Maasai Village', 'description' => 'Early morning game drive (golden hour photography). Visit a traditional Maasai village. Brunch at camp then transfer back to Nairobi.', 'meals' => ['Breakfast', 'Lunch']],
            ],
        ];

        if (isset($itineraries[$destination->slug])) {
            foreach ($itineraries[$destination->slug] as $day) {
                DestinationItinerary::create(array_merge($day, ['destination_id' => $destination->id]));
            }
        } else {
            // Generic 1-day or 3-day itinerary
            $days = $destination->duration_days >= 3 ? 3 : $destination->duration_days;
            for ($d = 1; $d <= $days; $d++) {
                DestinationItinerary::create([
                    'destination_id' => $destination->id,
                    'day_number' => $d,
                    'title' => $d === 1 ? 'Arrival & Orientation' : ($d === $days ? 'Farewell & Departure' : 'Full Exploration Day'),
                    'description' => "Day {$d} of your " . $destination->title . " experience.",
                    'meals' => $d === 1 ? ['Dinner'] : ($d === $days ? ['Breakfast'] : ['Breakfast', 'Lunch', 'Dinner']),
                ]);
            }
        }
    }

    private function seedDestinationAccommodations($destination): void
    {
        DestinationAccommodation::where('destination_id', $destination->id)->delete();

        if (in_array($destination->category, ['Wildlife', 'Mountain', 'Adventure'])) {
            DestinationAccommodation::create([
                'destination_id' => $destination->id,
                'name' => 'Luxury Tented Camp',
                'type' => 'Tented Camp',
                'rating' => 4.7,
                'room_type' => 'Deluxe Safari Tent',
                'bed_configuration' => '1 Queen bed',
                'amenities' => ['En-suite bathroom', 'Solar-powered lighting', 'Eco-friendly', 'Private deck', 'Room service'],
                'image_url' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800',
            ]);
        }

        if ($destination->category === 'Beach') {
            DestinationAccommodation::create([
                'destination_id' => $destination->id,
                'name' => 'Beachfront Resort',
                'type' => 'Hotel',
                'rating' => 4.8,
                'room_type' => 'Ocean View Suite',
                'bed_configuration' => '1 King bed or 2 Twin beds',
                'amenities' => ['Swimming pool', 'Beach access', 'Spa', 'Restaurant', 'Bar', 'Wi-Fi'],
                'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800',
            ]);
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HIDDEN GEMS  (from browse screen + gem_details_screen.dart defaults)
    // ──────────────────────────────────────────────────────────────────────────
    private function seedHiddenGems(): void
    {
        $gems = [
            [
                'slug' => 'chyulu-hills',
                'name' => 'Chyulu Hills',
                'description' => 'Ancient volcanic hills with lava caves, sweeping savannah views, and one of Kenya\'s least-visited wilderness areas. Home to diverse flora and wildlife, with the rare black-and-white colobus monkey.',
                'short_description' => 'Ancient volcanic hills and lava caves.',
                'category' => 'nature',
                'type' => 'Mountain',
                'tags' => ['volcanic', 'hiking', 'caves', 'wildlife', 'remote'],
                'location_name' => 'Chyulu Hills',
                'city' => 'Kibwezi',
                'county' => 'Makueni',
                'region' => 'Eastern',
                'latitude' => -2.5833,
                'longitude' => 37.8833,
                'cover_image_url' => 'https://images.unsplash.com/photo-1547471080-7cc2caa01a7e?w=800',
                'gallery' => ['https://images.unsplash.com/photo-1547471080-7cc2caa01a7e?w=800', 'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=800'],
                'difficulty' => 'Moderate',
                'difficulty_level' => 2,
                'best_time_to_visit' => 'June to October',
                'entry_fee_citizens_kes' => 500,
                'entry_fee_residents_kes' => 800,
                'entry_fee_non_residents_usd' => 15,
                'entry_fee_children_kes' => 250,
                'is_free_entry' => false,
                'access_info' => '5 hours from Nairobi via Mombasa Road. Nearest town is Kibwezi. 4WD strongly recommended in wet season.',
                'what_to_bring' => ['Hiking boots', 'Water (2L+)', 'Sunscreen', 'Camera', 'Light jacket', 'Snacks', 'First aid kit'],
                'best_for' => ['Hikers', 'Nature Lovers', 'Photographers', 'Adventure Seekers'],
                'transport_options' => [
                    ['method' => 'By Private Car', 'description' => '5-hour drive from Nairobi on A109 highway. 4WD recommended.', 'icon' => 'directions_car', 'duration' => '5 hours'],
                ],
                'rating' => 4.8,
                'review_count' => 234,
                'visitor_count' => 1560,
                'is_published' => true,
                'is_featured' => true,
                'xp_reward' => 200,
            ],
            [
                'slug' => 'hells-gate-gorge',
                'name' => "Hell's Gate Gorge",
                'description' => "Dramatic volcanic gorges carved by centuries of geothermal activity. One of the few Kenyan parks where you can walk and cycle among wildlife — zebras, giraffes, and buffaloes roam freely.",
                'short_description' => 'Walk among wildlife in gorges that inspired The Lion King.',
                'category' => 'adventure',
                'type' => 'Adventure',
                'tags' => ['gorge', 'cycling', 'wildlife', 'geothermal', 'hiking'],
                'location_name' => "Hell's Gate Gorge",
                'city' => 'Naivasha',
                'county' => 'Nakuru',
                'region' => 'Rift Valley',
                'latitude' => -0.9047,
                'longitude' => 36.3091,
                'cover_image_url' => 'https://images.unsplash.com/photo-1589182373726-e4f658ab50f0?w=800',
                'gallery' => ['https://images.unsplash.com/photo-1589182373726-e4f658ab50f0?w=800'],
                'difficulty' => 'Easy',
                'difficulty_level' => 1,
                'best_time_to_visit' => 'Year-round, avoid heavy rains (April, November)',
                'entry_fee_citizens_kes' => 350,
                'entry_fee_residents_kes' => 500,
                'entry_fee_non_residents_usd' => 26,
                'entry_fee_children_kes' => 200,
                'is_free_entry' => false,
                'access_info' => '1.5 hours from Nairobi. Bus or matatu to Naivasha, then taxi to park gate.',
                'what_to_bring' => ['Comfortable shoes', 'Water bottle', 'Sunscreen', 'Camera', 'Cash for bike rental'],
                'best_for' => ['Families', 'Cyclists', 'Nature Lovers', 'Day Trippers'],
                'parking_info' => 'Secure parking at main gate. KSh 300 per vehicle.',
                'rating' => 4.7,
                'review_count' => 445,
                'visitor_count' => 4200,
                'is_published' => true,
                'is_featured' => true,
                'xp_reward' => 150,
            ],
            [
                'slug' => 'kakamega-forest',
                'name' => 'Kakamega Forest',
                'description' => 'Kenya\'s only tropical rainforest — a remnant of the great Congo Basin forest. Home to 330+ bird species, 400+ plant species, and rare mammals including the red-tailed monkey and giant flying squirrel.',
                'short_description' => 'Kenya\'s only tropical rainforest — a birdwatcher\'s paradise.',
                'category' => 'nature',
                'type' => 'Forest',
                'tags' => ['rainforest', 'birds', 'primates', 'biodiversity', 'trekking'],
                'location_name' => 'Kakamega Forest Reserve',
                'city' => 'Kakamega',
                'county' => 'Kakamega',
                'region' => 'Western',
                'latitude' => 0.2833,
                'longitude' => 34.8500,
                'cover_image_url' => 'https://images.unsplash.com/photo-1542273917363-3b1817f69a2d?w=800',
                'gallery' => ['https://images.unsplash.com/photo-1542273917363-3b1817f69a2d?w=800'],
                'difficulty' => 'Moderate',
                'difficulty_level' => 2,
                'best_time_to_visit' => 'June to August (dry season), December to February',
                'entry_fee_citizens_kes' => 300,
                'entry_fee_residents_kes' => 500,
                'entry_fee_non_residents_usd' => 20,
                'is_free_entry' => false,
                'access_info' => '380km from Nairobi. 30 mins from Kakamega town. Public transport available.',
                'what_to_bring' => ['Binoculars (essential)', 'Bird guide book', 'Rain jacket', 'Insect repellent', 'Sturdy shoes'],
                'best_for' => ['Birdwatchers', 'Nature Lovers', 'Researchers', 'Solo Travelers'],
                'rating' => 4.6,
                'review_count' => 189,
                'visitor_count' => 890,
                'is_published' => true,
                'xp_reward' => 180,
            ],
            [
                'slug' => 'ngare-ndare-forest',
                'name' => 'Ngare Ndare Forest',
                'description' => "A magical forest on the lower slopes of Mt Kenya featuring natural swimming pools, rope bridge canopy walk, and a seasonal waterfall. The forest is an elephant corridor connecting Mt Kenya and Lewa Conservancy.",
                'short_description' => 'Canopy walks, natural pools, and elephant corridors below Mt Kenya.',
                'category' => 'nature',
                'type' => 'Forest',
                'tags' => ['canopy walk', 'swimming', 'elephants', 'waterfall', 'camping'],
                'location_name' => 'Ngare Ndare Forest',
                'city' => 'Timau',
                'county' => 'Meru',
                'region' => 'Central',
                'latitude' => 0.1833,
                'longitude' => 37.2833,
                'cover_image_url' => 'https://images.unsplash.com/photo-1511497584788-876760111969?w=800',
                'gallery' => ['https://images.unsplash.com/photo-1511497584788-876760111969?w=800'],
                'difficulty' => 'Moderate',
                'difficulty_level' => 2,
                'best_time_to_visit' => 'January–March, July–October',
                'entry_fee_citizens_kes' => 600,
                'entry_fee_non_residents_usd' => 20,
                'is_free_entry' => false,
                'access_info' => '220km from Nairobi. Take A2 highway north. Turn off at Timau. Signposted from main road.',
                'what_to_bring' => ['Swimwear', 'Towel', 'Water shoes', 'Sunscreen', 'Camera'],
                'best_for' => ['Families', 'Couples', 'Adventure Seekers', 'Photographers'],
                'rating' => 4.9,
                'review_count' => 312,
                'visitor_count' => 2100,
                'is_published' => true,
                'is_featured' => true,
                'xp_reward' => 200,
            ],
            [
                'slug' => 'shimba-hills',
                'name' => 'Shimba Hills',
                'description' => "Kenya's coastal rainforest preserve with rare sable antelopes, elephants, and breathtaking views over the Indian Ocean and Diani Beach below. The Sheldrick Falls is one of the tallest in Kenya.",
                'short_description' => 'Coastal rainforest with rare sable antelopes and ocean views.',
                'category' => 'nature',
                'type' => 'Nature',
                'tags' => ['coastal forest', 'sable antelope', 'elephants', 'waterfall', 'ocean views'],
                'location_name' => 'Shimba Hills National Reserve',
                'city' => 'Kwale',
                'county' => 'Kwale',
                'region' => 'Coast',
                'latitude' => -4.2167,
                'longitude' => 39.4333,
                'cover_image_url' => 'https://images.unsplash.com/photo-1590614147843-ab3d0bfa0c33?w=800',
                'gallery' => ['https://images.unsplash.com/photo-1590614147843-ab3d0bfa0c33?w=800'],
                'difficulty' => 'Easy',
                'difficulty_level' => 1,
                'best_time_to_visit' => 'July to October, January to February',
                'entry_fee_citizens_kes' => 400,
                'entry_fee_non_residents_usd' => 25,
                'is_free_entry' => false,
                'access_info' => '30km from Diani Beach. Easily combined with a beach holiday.',
                'what_to_bring' => ['Binoculars', 'Camera with zoom', 'Comfortable shoes', 'Sunscreen', 'Water'],
                'best_for' => ['Families', 'Wildlife Lovers', 'Beach Holidaymakers'],
                'rating' => 4.7,
                'review_count' => 267,
                'visitor_count' => 1800,
                'is_published' => true,
                'xp_reward' => 160,
            ],
            [
                'slug' => 'mount-longonot',
                'name' => 'Mount Longonot',
                'description' => "Hike up a dormant volcano and peer into its massive 2km-wide caldera. On a clear day, you can see the Great Rift Valley, Lake Naivasha, and Mt Kenya from the rim trail.",
                'short_description' => 'Dormant volcano hike with stunning caldera and Rift Valley views.',
                'category' => 'adventure',
                'type' => 'Mountain',
                'tags' => ['volcano', 'hiking', 'crater', 'rift valley', 'challenge'],
                'location_name' => 'Mount Longonot National Park',
                'city' => 'Naivasha',
                'county' => 'Nakuru',
                'region' => 'Rift Valley',
                'latitude' => -0.9144,
                'longitude' => 36.4567,
                'cover_image_url' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=800',
                'gallery' => ['https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=800'],
                'difficulty' => 'Difficult',
                'difficulty_level' => 4,
                'best_time_to_visit' => 'January–March, July–October (avoid rainy season)',
                'entry_fee_citizens_kes' => 400,
                'entry_fee_residents_kes' => 600,
                'entry_fee_non_residents_usd' => 26,
                'entry_fee_children_kes' => 200,
                'is_free_entry' => false,
                'access_info' => '80km from Nairobi. Take A104 highway. Signposted from Naivasha town.',
                'what_to_bring' => ['Hiking boots (essential)', 'Plenty of water (3L+)', 'Energy snacks', 'Sunscreen', 'Hat', 'Light rain jacket'],
                'best_for' => ['Hikers', 'Fitness Enthusiasts', 'Photographers', 'Adventure Seekers'],
                'parking_info' => 'Free parking at the park gate. Secure facility with rangers on duty.',
                'rating' => 4.8,
                'review_count' => 523,
                'visitor_count' => 5600,
                'is_published' => true,
                'is_featured' => true,
                'xp_reward' => 300,
            ],
            [
                'slug' => 'oldonyo-sabuk',
                'name' => 'Ol Doinyo Sabuk',
                'description' => "The 'Mountain of the Buffalo' — a serene hill sanctuary just 60km from Nairobi. Wild buffaloes, baboons, and impala roam freely through montane forest. Home to the grave of William Northrup McMillan.",
                'short_description' => 'Buffalo mountain sanctuary just 60km from Nairobi.',
                'category' => 'nature',
                'type' => 'Mountain',
                'tags' => ['buffalo', 'hiking', 'forest', 'day trip', 'nairobi'],
                'location_name' => 'Ol Doinyo Sabuk National Park',
                'city' => 'Thika',
                'county' => 'Machakos',
                'region' => 'Central',
                'latitude' => -1.1500,
                'longitude' => 37.2333,
                'cover_image_url' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800',
                'gallery' => ['https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800'],
                'difficulty' => 'Challenging',
                'difficulty_level' => 3,
                'best_time_to_visit' => 'June–October, December–February',
                'entry_fee_citizens_kes' => 400,
                'entry_fee_non_residents_usd' => 26,
                'is_free_entry' => false,
                'access_info' => 'Drive via Thika Superhighway. 60km from Nairobi city centre.',
                'what_to_bring' => ['Hiking boots', 'Water (2L+)', 'Snacks', 'Camera', 'Buffalo spray (park rangers carry)'],
                'best_for' => ['Day Trippers', 'Hikers', 'Nature Lovers', 'History Buffs'],
                'rating' => 4.5,
                'review_count' => 156,
                'visitor_count' => 780,
                'is_published' => true,
                'xp_reward' => 170,
            ],
            [
                'slug' => 'karura-forest',
                'name' => 'Karura Forest',
                'description' => "Nairobi's urban forest sanctuary — 1,063 hectares of natural cedar forest in the heart of the city. Running trails, walking paths, waterfalls, caves, and a rope bridge make this a favourite weekend escape.",
                'short_description' => 'Urban forest sanctuary in the heart of Nairobi.',
                'category' => 'nature',
                'type' => 'Forest',
                'tags' => ['urban', 'running', 'walking', 'caves', 'waterfall', 'cycling'],
                'location_name' => 'Karura Forest',
                'city' => 'Nairobi',
                'county' => 'Nairobi',
                'region' => 'Central',
                'latitude' => -1.2291,
                'longitude' => 36.8219,
                'cover_image_url' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=800',
                'gallery' => ['https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=800'],
                'difficulty' => 'Easy',
                'difficulty_level' => 1,
                'best_time_to_visit' => 'Year-round. Best early morning 6–9am.',
                'entry_fee_citizens_kes' => 100,
                'entry_fee_non_residents_usd' => 5,
                'is_free_entry' => false,
                'access_info' => 'Multiple gates: Kiambu Road Gate, Limuru Road Gate, UN Gate. Uber or public transport available.',
                'what_to_bring' => ['Running shoes or trainers', 'Water bottle', 'Sun hat', 'Camera'],
                'best_for' => ['Runners', 'Families', 'Cyclists', 'Dog Walkers', 'Picnickers'],
                'parking_info' => 'Parking available at all gates. KSh 100 per vehicle.',
                'amenities' => [
                    ['icon' => 'restaurant', 'name' => 'Café/Restaurant', 'available' => true],
                    ['icon' => 'local_parking', 'name' => 'Parking', 'available' => true],
                    ['icon' => 'wc', 'name' => 'Restrooms', 'available' => true],
                    ['icon' => 'wifi', 'name' => 'WiFi', 'available' => false],
                    ['icon' => 'accessible', 'name' => 'Wheelchair Access', 'available' => false],
                ],
                'transport_options' => [
                    ['method' => 'By Uber/Bolt', 'description' => 'Direct to main gate, Kiambu Road.', 'icon' => 'local_taxi', 'duration' => '20-30 min from CBD'],
                    ['method' => 'By Matatu', 'description' => 'Route 45 from City Centre drops near Kiambu Road gate.', 'icon' => 'directions_bus', 'duration' => '30-45 min'],
                ],
                'rating' => 4.6,
                'review_count' => 678,
                'visitor_count' => 12000,
                'is_published' => true,
                'is_featured' => true,
                'xp_reward' => 80,
            ],
        ];

        foreach ($gems as $data) {
            $gem = HiddenGem::updateOrCreate(['slug' => $data['slug']], $data);
            $this->seedGemNearby($gem);
            $this->seedGemTips($gem);
        }

        $this->command?->info('✅ Hidden Gems seeded: ' . count($gems));
    }

    private function seedGemNearby($gem): void
    {
        NearbyAttraction::where('source_type', 'hidden_gem')->where('source_id', $gem->id)->delete();

        $nearby = [
            'karura-forest' => [
                ['name' => 'Giraffe Centre', 'category' => 'Wildlife', 'category_icon' => 'pets', 'distance_km' => 5.1, 'image_url' => 'https://images.unsplash.com/photo-1564760055775-d63b17a55c44?w=400'],
                ['name' => 'Bomas of Kenya', 'category' => 'Culture', 'category_icon' => 'museum', 'distance_km' => 8.7, 'image_url' => 'https://images.unsplash.com/photo-1523805009345-7448845a9e53?w=400'],
                ['name' => 'Westgate Mall', 'category' => 'City', 'category_icon' => 'shopping_bag', 'distance_km' => 2.1, 'image_url' => 'https://images.unsplash.com/photo-1611348524140-53c9a25263d6?w=400'],
            ],
            'hells-gate-gorge' => [
                ['name' => 'Lake Naivasha', 'category' => 'Nature', 'category_icon' => 'water', 'distance_km' => 12.0, 'image_url' => 'https://images.unsplash.com/photo-1547471080-7cc2caa01a7e?w=400'],
                ['name' => 'Crescent Island', 'category' => 'Wildlife', 'category_icon' => 'pets', 'distance_km' => 15.5, 'image_url' => 'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=400'],
                ['name' => 'Olkaria Geothermal Spa', 'category' => 'Wellness', 'category_icon' => 'spa', 'distance_km' => 4.2, 'image_url' => 'https://images.unsplash.com/photo-1589182373726-e4f658ab50f0?w=400'],
            ],
            'mount-longonot' => [
                ['name' => 'Lake Naivasha', 'category' => 'Nature', 'category_icon' => 'water', 'distance_km' => 18.0, 'image_url' => 'https://images.unsplash.com/photo-1547471080-7cc2caa01a7e?w=400'],
                ["name" => "Hell's Gate", 'category' => 'Adventure', 'category_icon' => 'hiking', 'distance_km' => 22.5, 'image_url' => 'https://images.unsplash.com/photo-1589182373726-e4f658ab50f0?w=400'],
            ],
        ];

        $attractions = $nearby[$gem->slug] ?? [
            ['name' => 'Nairobi National Park', 'category' => 'Wildlife', 'category_icon' => 'pets', 'distance_km' => rand(5, 30) + 0.5, 'image_url' => 'https://images.unsplash.com/photo-1516426122078-c23e76319801?w=400'],
            ['name' => 'Local Town Centre', 'category' => 'City', 'category_icon' => 'location_city', 'distance_km' => rand(2, 10) + 0.3, 'image_url' => 'https://images.unsplash.com/photo-1611348524140-53c9a25263d6?w=400'],
        ];

        foreach ($attractions as $i => $attraction) {
            NearbyAttraction::create(array_merge($attraction, [
                'source_type' => 'hidden_gem',
                'source_id'   => $gem->id,
                'sort_order'  => $i,
            ]));
        }
    }

    private function seedGemTips($gem): void
    {
        LocalTip::where('tippable_type', 'hidden_gem')->where('tippable_id', $gem->id)->delete();

        $tips = [
            ['title' => 'Best Photography Time', 'description' => 'Golden hour (6–7am and 5:30–6:30pm) offers the most stunning lighting.', 'icon' => 'camera_alt', 'is_important' => true, 'sort_order' => 1],
            ['title' => 'Hire a Local Guide', 'description' => 'Local guides enhance your experience and support the community. KSh 500–1,500 is standard.', 'icon' => 'person', 'is_important' => false, 'sort_order' => 2],
            ['title' => 'Respect Wildlife', 'description' => 'Maintain safe distance from animals. Never feed or disturb them.', 'icon' => 'pets', 'is_important' => true, 'sort_order' => 3],
            ['title' => 'Leave No Trace', 'description' => 'Carry out all waste. Help preserve this beautiful location for future visitors.', 'icon' => 'recycling', 'is_important' => false, 'sort_order' => 4],
        ];

        foreach ($tips as $tip) {
            LocalTip::create(array_merge($tip, ['tippable_type' => 'hidden_gem', 'tippable_id' => $gem->id]));
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // ROUTES  (from route.dart MockRouteData)
    // ──────────────────────────────────────────────────────────────────────────
    private function seedRoutes(): void
    {
        $routes = [
            [
                'slug' => 'rift-valley-panorama',
                'name' => 'Rift Valley Panorama',
                'description' => 'Breathtaking drive through the Great Rift Valley with stunning viewpoints and wildlife sightings along Lake Naivasha, Nakuru, and Elementaita.',
                'type' => 'scenic',
                'difficulty' => 'moderate',
                'region' => 'Rift Valley',
                'start_point_name' => 'Naivasha Town',
                'start_latitude' => -0.9231,
                'start_longitude' => 36.0566,
                'end_point_name' => 'Elementaita',
                'end_latitude' => -0.2833,
                'end_longitude' => 36.0667,
                'waypoints' => [
                    ['lat' => -0.9231, 'lng' => 36.0566, 'name' => 'Naivasha', 'order' => 1],
                    ['lat' => -0.6833, 'lng' => 36.2167, 'name' => 'Nakuru', 'order' => 2],
                    ['lat' => -0.2833, 'lng' => 36.0667, 'name' => 'Elementaita', 'order' => 3],
                ],
                'distance_km' => 95.5,
                'duration_minutes' => 210,
                'elevation_gain_meters' => 450.0,
                'highlights' => ['Lake Naivasha views', 'Flamingo colonies', 'Escarpment vistas', 'Nakuru National Park stop'],
                'cover_photo_url' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800',
                'rating' => 4.8,
                'completed_count' => 234,
                'is_featured' => true,
                'xp_reward' => 200,
            ],
            [
                'slug' => 'mount-kenya-forest-trail',
                'name' => 'Mount Kenya Forest Trail',
                'description' => 'Dense montane forest hike with ancient bamboo groves, crystal-clear mountain streams, and spectacular views of Mount Kenya\'s twin peaks.',
                'type' => 'hiking',
                'difficulty' => 'challenging',
                'region' => 'Central Kenya',
                'start_point_name' => 'Sirimon Gate',
                'start_latitude' => -0.1521,
                'start_longitude' => 37.3084,
                'end_point_name' => 'Mackinders Camp',
                'end_latitude' => -0.1700,
                'end_longitude' => 37.3300,
                'waypoints' => [
                    ['lat' => -0.1521, 'lng' => 37.3084, 'name' => 'Sirimon Gate', 'order' => 1],
                    ['lat' => -0.1600, 'lng' => 37.3200, 'name' => 'Old Moses Camp', 'order' => 2],
                    ['lat' => -0.1700, 'lng' => 37.3300, 'name' => 'Mackinders Camp', 'order' => 3],
                ],
                'distance_km' => 12.3,
                'duration_minutes' => 300,
                'elevation_gain_meters' => 800.0,
                'highlights' => ['Ancient cedar trees', 'Mountain streams', 'Wildlife corridors', 'Bamboo grove', 'Peak views on clear days'],
                'cover_photo_url' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800',
                'rating' => 4.9,
                'completed_count' => 89,
                'is_featured' => true,
                'xp_reward' => 350,
            ],
            [
                'slug' => 'coastal-heritage-path',
                'name' => 'Coastal Heritage Path',
                'description' => 'Historic walking route through Old Town Mombasa — a UNESCO-listed urban landscape of Swahili architecture, spice markets, and Portuguese fortifications.',
                'type' => 'walking',
                'difficulty' => 'easy',
                'region' => 'Coastal Kenya',
                'start_point_name' => 'Fort Jesus',
                'start_latitude' => -4.0435,
                'start_longitude' => 39.6682,
                'end_point_name' => 'Spice Market, Old Town',
                'end_latitude' => -4.0550,
                'end_longitude' => 39.6750,
                'waypoints' => [
                    ['lat' => -4.0435, 'lng' => 39.6682, 'name' => 'Fort Jesus', 'order' => 1],
                    ['lat' => -4.0500, 'lng' => 39.6700, 'name' => 'Old Town', 'order' => 2],
                    ['lat' => -4.0550, 'lng' => 39.6750, 'name' => 'Spice Market', 'order' => 3],
                ],
                'distance_km' => 3.2,
                'duration_minutes' => 120,
                'highlights' => ['Swahili architecture', 'Spice markets', 'Ocean views', 'Fort Jesus UNESCO site', 'Biashara Street'],
                'cover_photo_url' => 'https://images.unsplash.com/photo-1523805009345-7448845a9e53?w=800',
                'rating' => 4.6,
                'completed_count' => 512,
                'is_featured' => false,
                'xp_reward' => 120,
            ],
        ];

        foreach ($routes as $data) {
            $route = DiscoverRoute::updateOrCreate(['slug' => $data['slug']], $data);
            $this->seedRouteSegments($route);
        }

        $this->command?->info('✅ Routes seeded: ' . count($routes));
    }

    private function seedRouteSegments($route): void
    {
        RouteSegment::where('route_id', $route->id)->delete();

        $segments = [
            'rift-valley-panorama' => [
                ['name' => 'Crescent Island Viewpoint', 'description' => 'Panoramic view of Lake Naivasha', 'type' => 'viewpoint', 'start_latitude' => -0.7500, 'start_longitude' => 36.3500, 'end_latitude' => -0.7550, 'end_longitude' => 36.3550, 'distance_km' => 0.8, 'points_reward' => 75, 'discovered_by_count' => 156, 'sort_order' => 1],
                ['name' => 'Flamingo Flats', 'description' => 'Lake Nakuru flamingo colony viewpoint', 'type' => 'wildlife', 'start_latitude' => -0.3600, 'start_longitude' => 36.0800, 'end_latitude' => -0.3700, 'end_longitude' => 36.0850, 'distance_km' => 1.2, 'points_reward' => 100, 'discovered_by_count' => 89, 'sort_order' => 2],
            ],
            'coastal-heritage-path' => [
                ['name' => 'Fort Jesus Ramparts', 'description' => 'Historic Portuguese fort with ocean views', 'type' => 'historical', 'start_latitude' => -4.0435, 'start_longitude' => 39.6682, 'end_latitude' => -4.0440, 'end_longitude' => 39.6690, 'distance_km' => 0.3, 'points_reward' => 100, 'discovered_by_count' => 423, 'sort_order' => 1],
                ['name' => 'Old Town Spice Quarter', 'description' => 'Ancient Swahili trading quarter', 'type' => 'historical', 'start_latitude' => -4.0500, 'start_longitude' => 39.6700, 'end_latitude' => -4.0520, 'end_longitude' => 39.6720, 'distance_km' => 0.5, 'points_reward' => 75, 'discovered_by_count' => 287, 'sort_order' => 2],
            ],
        ];

        $routeSegments = $segments[$route->slug] ?? [];
        foreach ($routeSegments as $seg) {
            RouteSegment::create(array_merge($seg, ['route_id' => $route->id]));
        }
    }
}
