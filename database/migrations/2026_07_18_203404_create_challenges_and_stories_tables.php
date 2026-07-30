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
        Schema::create('challenges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description');
            $table->integer('points_reward')->default(100);
            $table->string('icon')->nullable();
            $table->string('difficulty')->default('moderate');
            $table->integer('target_value')->default(1);
            $table->string('type')->default('distance');
            $table->timestamps();
        });

        Schema::create('user_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('challenge_id')->constrained('challenges')->onDelete('cascade');
            $table->string('status')->default('in_progress'); // in_progress, completed
            $table->integer('progress')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('stories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('location_name')->nullable();
            $table->string('emoji')->default('🌲');
            $table->timestamps();
        });

        Schema::create('story_frames', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('story_id')->constrained('stories')->onDelete('cascade');
            $table->string('image_url');
            $table->string('caption')->nullable();
            $table->integer('duration_seconds')->default(5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('story_frames');
        Schema::dropIfExists('stories');
        Schema::dropIfExists('user_challenges');
        Schema::dropIfExists('challenges');
    }
};
