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
        Schema::create('activity_game_packs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('game_type')->index(); // trivia_taboo, charades, reflex, bingo, sidequest, social
            $table->string('occasion')->index();  // outdoor, bbq, birthday, roadtrip, party, family
            $table->integer('min_players')->default(2);
            $table->integer('max_players')->default(20);
            $table->integer('duration_minutes')->default(15);
            $table->string('difficulty')->default('all_levels');
            $table->string('icon_name')->default('sports_esports');
            $table->string('color_hex')->default('#2E7D32');
            $table->boolean('is_featured')->default(false);
            $table->json('tags')->nullable();
            $table->text('rules')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_game_cards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pack_id')->constrained('activity_game_packs')->onDelete('cascade');
            $table->string('category')->nullable();
            $table->text('prompt');
            $table->string('answer')->nullable();
            $table->json('forbidden_words')->nullable();
            $table->text('hint')->nullable();
            $table->integer('points')->default(1);
            $table->integer('timer_seconds')->default(60);
            $table->json('metadata')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('activity_game_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('pack_id')->constrained('activity_game_packs')->onDelete('cascade');
            $table->json('team_names')->nullable();
            $table->json('scores')->nullable();
            $table->integer('cards_played')->default(0);
            $table->integer('duration_seconds')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_game_sessions');
        Schema::dropIfExists('activity_game_cards');
        Schema::dropIfExists('activity_game_packs');
    }
};
