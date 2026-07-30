<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            if (!Schema::hasColumn('destinations', 'location_node_id')) $table->uuid('location_node_id')->nullable();
            if (!Schema::hasColumn('destinations', 'busyness_score')) $table->integer('busyness_score')->default(0); // 0-10
            if (!Schema::hasColumn('destinations', 'crowd_density')) $table->string('crowd_density')->default('Quiet');
            if (!Schema::hasColumn('destinations', 'current_visitors')) $table->integer('current_visitors')->default(0);
            if (!Schema::hasColumn('destinations', 'peak_hours')) $table->string('peak_hours')->nullable();
            if (!Schema::hasColumn('destinations', 'weather_note')) $table->string('weather_note')->nullable();
            if (!Schema::hasColumn('destinations', 'instant_booking')) $table->boolean('instant_booking')->default(false);
            if (!Schema::hasColumn('destinations', 'available_days')) $table->json('available_days')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn([
                'location_node_id', 'busyness_score', 'crowd_density', 'current_visitors',
                'peak_hours', 'weather_note', 'instant_booking', 'available_days'
            ]);
        });
    }
};
