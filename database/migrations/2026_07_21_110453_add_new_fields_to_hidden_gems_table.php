<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hidden_gems', function (Blueprint $table) {
            if (!Schema::hasColumn('hidden_gems', 'audio_guide_url')) $table->string('audio_guide_url')->nullable();
            if (!Schema::hasColumn('hidden_gems', 'video_url')) $table->string('video_url')->nullable();
            if (!Schema::hasColumn('hidden_gems', 'discovered_by_count')) $table->integer('discovered_by_count')->default(0);
            if (!Schema::hasColumn('hidden_gems', 'upvotes')) $table->integer('upvotes')->default(0);
            if (!Schema::hasColumn('hidden_gems', 'downvotes')) $table->integer('downvotes')->default(0);
            if (!Schema::hasColumn('hidden_gems', 'added_by_name')) $table->string('added_by_name')->nullable();
            if (!Schema::hasColumn('hidden_gems', 'is_local_guide')) $table->boolean('is_local_guide')->default(false);
            if (!Schema::hasColumn('hidden_gems', 'verification_status')) $table->string('verification_status')->default('pending'); // pending, verified, flagged, rejected
            if (!Schema::hasColumn('hidden_gems', 'location_node_id')) $table->uuid('location_node_id')->nullable();
            if (!Schema::hasColumn('hidden_gems', 'safety_notes')) $table->text('safety_notes')->nullable();
            if (!Schema::hasColumn('hidden_gems', 'requires_permit')) $table->boolean('requires_permit')->default(false);
            if (!Schema::hasColumn('hidden_gems', 'is_quest_unlock')) $table->boolean('is_quest_unlock')->default(false);
            if (!Schema::hasColumn('hidden_gems', 'accessibility')) $table->json('accessibility')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('hidden_gems', function (Blueprint $table) {
            $table->dropColumn([
                'audio_guide_url', 'video_url', 'discovered_by_count', 'upvotes', 'downvotes',
                'added_by_name', 'is_local_guide', 'verification_status', 'location_node_id',
                'safety_notes', 'requires_permit', 'is_quest_unlock', 'accessibility'
            ]);
        });
    }
};
