<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('display_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('bio')->nullable();
            $table->string('photo_url')->nullable();
            $table->string('photo_thumbnail_url')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();
            $table->string('home_country')->nullable();
            $table->string('referral_source')->nullable();
            $table->string('id_number')->nullable();
            $table->string('passport_number')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Kenya');
            $table->string('postal_code')->nullable();
            $table->json('interests')->nullable();
            $table->json('languages')->nullable();
            $table->json('travel_styles')->nullable();
            $table->json('activity_preferences')->nullable();
            $table->json('dietary_restrictions')->nullable();
            $table->json('accessibility_needs')->nullable();
            $table->string('preferred_currency')->default('KES');
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('location_enabled')->default(false);
            $table->boolean('show_distance_away')->default(true);
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            $table->string('travel_style')->nullable();
            $table->boolean('travel_insurance')->default(false);
            $table->string('explorer_level')->default('explorer');
            $table->integer('current_xp')->default(0);
            $table->integer('streak_days')->default(0);
            $table->json('unlocked_badges')->nullable();
            $table->string('role')->default('traveler');
            $table->integer('profile_completion')->default(0);
            $table->boolean('is_profile_public')->default(true);
            $table->boolean('share_location_with_friends')->default(false);
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->softDeletes();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->json('selectables')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
