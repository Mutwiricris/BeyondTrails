<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'sharing_mode')) $table->string('sharing_mode')->default('explorers');
            if (!Schema::hasColumn('users', 'traveller_status')) $table->string('traveller_status')->default('offline');
            if (!Schema::hasColumn('users', 'explorer_level')) $table->string('explorer_level')->default('Explorer');
            if (!Schema::hasColumn('users', 'streak_days')) $table->integer('streak_days')->default(0);
            if (!Schema::hasColumn('users', 'last_seen_at')) $table->timestamp('last_seen_at')->nullable();
            if (!Schema::hasColumn('users', 'allow_dms')) $table->boolean('allow_dms')->default(true);
            if (!Schema::hasColumn('users', 'gems_discovered_count')) $table->integer('gems_discovered_count')->default(0);
            if (!Schema::hasColumn('users', 'bio')) $table->text('bio')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'sharing_mode', 'traveller_status', 
                'explorer_level', 'streak_days', 'last_seen_at', 
                'allow_dms', 'gems_discovered_count', 'bio'
            ]);
        });
    }
};
