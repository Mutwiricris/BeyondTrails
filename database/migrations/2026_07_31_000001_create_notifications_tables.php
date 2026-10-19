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
        // ── 1. Notifications Table ──────────────────────────────────────────
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category')->default('gem'); // gem, activity, trip, chat, nearby
            $table->string('title');
            $table->text('body');
            $table->string('image_url')->nullable();
            $table->string('target_screen')->nullable();
            $table->json('payload')->nullable();
            $table->json('action_buttons')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
            $table->index(['category']);
            $table->index(['created_at']);
        });

        // ── 2. Notification Settings Table ──────────────────────────────────
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('chat_notifications_enabled')->default(true);
            $table->boolean('trip_reminders_enabled')->default(true);
            $table->boolean('nearby_gems_enabled')->default(true);
            $table->boolean('local_activities_enabled')->default(true);
            $table->boolean('quiet_hours_enabled')->default(true);
            $table->string('quiet_hours_start')->default('22:00');
            $table->string('quiet_hours_end')->default('07:00');
            $table->decimal('cluster_cooldown_hours', 5, 2)->default(12.0);
            $table->decimal('max_proximity_distance_km', 5, 2)->default(2.0);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
        Schema::dropIfExists('notifications');
    }
};
