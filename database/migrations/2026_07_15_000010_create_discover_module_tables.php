<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('wishlist_items');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('local_tips');
        Schema::dropIfExists('nearby_attractions');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('route_segments');
        Schema::dropIfExists('discover_routes');
        Schema::dropIfExists('hidden_gems');
        Schema::dropIfExists('destination_accommodations');
        Schema::dropIfExists('destination_itinerary');
        Schema::dropIfExists('destinations');
        Schema::dropIfExists('operators');
        Schema::enableForeignKeyConstraints();

        // ── 1. operators ──────────────────────────────────────────────────────
        Schema::create('operators', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->json('gallery')->default('[]');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Kenya');
            $table->string('business_type')->nullable(); // Tour Operator, Hotel, Camp, Lodge
            $table->json('specializations')->default('[]');
            $table->json('certifications')->default('[]');
            $table->json('services')->default('[]');
            $table->json('languages')->default('["English","Swahili"]');
            $table->json('operating_hours')->nullable();
            $table->json('social_links')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->text('payment_terms')->nullable();
            $table->text('safety_measures')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('verification_badge')->nullable();
            $table->string('license_number')->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('review_count')->default(0);
            $table->integer('total_bookings')->default(0);
            $table->integer('tours_offered')->default(0);
            $table->integer('accommodations_offered')->default(0);
            $table->date('member_since')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['is_verified', 'is_active']);
            $table->index(['rating']);
        });

        // ── 2. destinations ───────────────────────────────────────────────────
        Schema::create('destinations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('short_description')->nullable();
            $table->string('category'); // Wildlife|Beach|Mountain|Culture|Adventure|City
            $table->string('location');  // Human-readable e.g. "Narok County"
            $table->string('county')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->default('Kenya');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('cover_image_url')->nullable();
            $table->json('gallery')->default('[]');
            $table->integer('price_kes')->default(0);
            $table->decimal('price_usd', 8, 2)->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('review_count')->default(0);
            $table->integer('duration_days')->default(1);
            $table->string('duration_label')->default('1 Day'); // e.g. "3 Days"
            $table->integer('group_size_max')->default(6);
            $table->string('difficulty')->default('Moderate'); // Easy|Moderate|Hard|Difficult
            $table->string('tour_type')->default('Safari');
            $table->json('highlights')->default('[]');
            $table->json('included')->default('[]');
            $table->json('excluded')->default('[]');
            $table->json('what_to_bring')->default('[]');
            $table->json('languages_spoken')->default('["English","Swahili"]');
            $table->text('meeting_point')->nullable();
            $table->decimal('meeting_lat', 10, 7)->nullable();
            $table->decimal('meeting_lng', 10, 7)->nullable();
            $table->json('transport_info')->nullable();
            $table->json('meal_info')->nullable();
            $table->json('health_safety_info')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->json('faqs')->default('[]');
            $table->uuid('operator_id')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('xp_reward')->default(100);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('operator_id')->references('id')->on('operators')->nullOnDelete();
            $table->index(['category', 'is_active']);
            $table->index(['is_popular', 'rating']);
            $table->index(['price_kes']);
        });

        // ── 3. destination_itinerary ──────────────────────────────────────────
        Schema::create('destination_itinerary', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('destination_id');
            $table->integer('day_number');
            $table->string('title');
            $table->text('description');
            $table->json('activities')->default('[]');
            $table->json('meals')->default('[]'); // ['Breakfast','Lunch','Dinner']
            $table->timestamps();

            $table->foreign('destination_id')->references('id')->on('destinations')->cascadeOnDelete();
            $table->index(['destination_id', 'day_number']);
        });

        // ── 4. destination_accommodations ─────────────────────────────────────
        Schema::create('destination_accommodations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('destination_id');
            $table->string('name');
            $table->string('type')->default('Lodge'); // Lodge|Camp|Tented Camp|Hotel
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->string('room_type')->nullable();
            $table->string('bed_configuration')->nullable();
            $table->json('amenities')->default('[]');
            $table->string('image_url')->nullable();
            $table->timestamps();

            $table->foreign('destination_id')->references('id')->on('destinations')->cascadeOnDelete();
        });

        // ── 5. hidden_gems ────────────────────────────────────────────────────
        Schema::create('hidden_gems', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('short_description')->nullable();
            $table->string('category'); // nature|culture|adventure|food|views
            $table->string('type')->nullable(); // e.g. "Nature", "Mountain", "Waterfall"
            $table->json('tags')->default('[]');
            $table->string('location_name')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('county')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->default('Kenya');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('cover_image_url')->nullable();
            $table->json('gallery')->default('[]');
            $table->string('difficulty')->nullable(); // Easy|Moderate|Challenging|Difficult
            $table->integer('difficulty_level')->default(1); // 1-4 for sorting
            $table->string('best_time_to_visit')->nullable();
            $table->decimal('entry_fee_citizens_kes', 10, 2)->nullable();
            $table->decimal('entry_fee_residents_kes', 10, 2)->nullable();
            $table->decimal('entry_fee_non_residents_usd', 10, 2)->nullable();
            $table->decimal('entry_fee_children_kes', 10, 2)->nullable();
            $table->boolean('is_free_entry')->default(false);
            $table->text('access_info')->nullable();
            $table->json('facilities')->default('[]');
            $table->json('amenities')->default('[]'); // [{icon, name, available}]
            $table->json('what_to_bring')->default('[]');
            $table->json('best_for')->default('[]'); // ['Solo Travelers','Couples',...]
            $table->json('transport_options')->default('[]');
            $table->text('parking_info')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('website')->nullable();
            $table->uuid('operator_id')->nullable();
            $table->foreignId('submitted_by_user_id')->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('review_count')->default(0);
            $table->integer('visitor_count')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->integer('xp_reward')->default(150);
            $table->timestamps();

            $table->foreign('operator_id')->references('id')->on('operators')->nullOnDelete();
            $table->foreign('submitted_by_user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['category', 'is_published']);
            $table->index(['difficulty_level']);
            $table->index(['is_featured', 'rating']);
            $table->index(['region']);
        });

        // ── 6. routes ─────────────────────────────────────────────────────────
        Schema::create('discover_routes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type'); // safari|scenic|hiking|cycling|walking
            $table->string('difficulty'); // easy|moderate|challenging|difficult
            $table->string('region')->nullable();
            $table->string('country')->default('Kenya');
            $table->string('start_point_name')->nullable();
            $table->decimal('start_latitude', 10, 7)->nullable();
            $table->decimal('start_longitude', 10, 7)->nullable();
            $table->string('end_point_name')->nullable();
            $table->decimal('end_latitude', 10, 7)->nullable();
            $table->decimal('end_longitude', 10, 7)->nullable();
            $table->json('waypoints')->default('[]'); // [{lat, lng, name, order}]
            $table->decimal('distance_km', 8, 2)->default(0.00);
            $table->integer('duration_minutes')->default(0);
            $table->decimal('elevation_gain_meters', 8, 2)->nullable();
            $table->json('highlights')->default('[]');
            $table->string('cover_photo_url')->nullable();
            $table->json('photos')->default('[]');
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('completed_count')->default(0);
            $table->integer('review_count')->default(0);
            $table->boolean('is_published')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('xp_reward')->default(200);
            $table->foreignId('created_by_user_id')->nullable();
            $table->timestamps();

            $table->foreign('created_by_user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['type', 'is_published']);
            $table->index(['difficulty']);
        });

        // ── 7. route_segments ─────────────────────────────────────────────────
        Schema::create('route_segments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('route_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type'); // scenic|wildlife|viewpoint|waterfall|historical|photo
            $table->decimal('start_latitude', 10, 7);
            $table->decimal('start_longitude', 10, 7);
            $table->decimal('end_latitude', 10, 7);
            $table->decimal('end_longitude', 10, 7);
            $table->decimal('distance_km', 6, 3)->default(0.000);
            $table->decimal('best_time_minutes', 8, 2)->nullable();
            $table->integer('points_reward')->default(50);
            $table->integer('discovered_by_count')->default(0);
            $table->json('photos')->default('[]');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('route_id')->references('id')->on('discover_routes')->cascadeOnDelete();
            $table->index(['route_id', 'sort_order']);
        });

        // ── 8. reviews ────────────────────────────────────────────────────────
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reviewable_type'); // destination|hidden_gem|operator|route
            $table->uuid('reviewable_id');
            $table->foreignId('user_id');
            $table->decimal('rating', 2, 1);
            $table->text('comment')->nullable();
            $table->json('photos')->default('[]');
            $table->boolean('is_verified_visit')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['reviewable_type', 'reviewable_id']);
            $table->index(['user_id']);
            $table->unique(['reviewable_type', 'reviewable_id', 'user_id']); // one review per user
        });

        // ── 9. nearby_attractions ─────────────────────────────────────────────
        Schema::create('nearby_attractions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('source_type'); // hidden_gem|destination
            $table->uuid('source_id');
            $table->string('name');
            $table->string('category');
            $table->string('category_icon')->nullable();
            $table->decimal('distance_km', 6, 2)->nullable();
            $table->string('image_url')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['source_type', 'source_id']);
        });

        // ── 10. local_tips ────────────────────────────────────────────────────
        Schema::create('local_tips', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tippable_type'); // hidden_gem|destination
            $table->uuid('tippable_id');
            $table->string('title');
            $table->text('description');
            $table->string('icon')->nullable(); // Material icon name
            $table->boolean('is_important')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['tippable_type', 'tippable_id', 'sort_order']);
        });

        // ── 11. wishlists ─────────────────────────────────────────────────────
        Schema::create('wishlists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id');
            $table->string('name')->default('My List');
            $table->string('description')->nullable();
            $table->string('cover_image_url')->nullable();
            $table->boolean('is_private')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['user_id']);
        });

        // ── 12. wishlist_items ────────────────────────────────────────────────
        Schema::create('wishlist_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('wishlist_id');
            $table->string('itemable_type'); // destination|hidden_gem|operator|route
            $table->uuid('itemable_id');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('wishlist_id')->references('id')->on('wishlists')->cascadeOnDelete();
            $table->index(['wishlist_id']);
            $table->unique(['wishlist_id', 'itemable_type', 'itemable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlist_items');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('local_tips');
        Schema::dropIfExists('nearby_attractions');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('route_segments');
        Schema::dropIfExists('discover_routes');
        Schema::dropIfExists('hidden_gems');
        Schema::dropIfExists('destination_accommodations');
        Schema::dropIfExists('destination_itinerary');
        Schema::dropIfExists('destinations');
        Schema::dropIfExists('operators');
    }
};
