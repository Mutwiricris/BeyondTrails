<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id'); // organizer
            $table->string('category')->nullable();
            $table->string('type')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('location_type')->nullable(); // 'general' or 'specific'
            $table->string('general_area')->nullable();
            $table->string('location_name')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->date('date')->nullable();
            $table->string('time_type')->nullable(); // 'flexible' or 'specific'
            $table->string('specific_time')->nullable();
            $table->integer('duration_hours')->nullable();
            $table->integer('min_age')->default(18);
            $table->integer('max_age')->default(65);
            $table->string('privacy')->default('open'); // 'open', 'private', 'invite_only'
            $table->integer('max_capacity')->default(20);
            $table->string('join_approval')->default('instant'); // 'instant', 'host_approval'
            $table->json('tags')->nullable();
            $table->boolean('is_host_verified')->default(false);
            $table->string('status')->default('upcoming'); // 'upcoming', 'ongoing', 'completed', 'cancelled'
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
