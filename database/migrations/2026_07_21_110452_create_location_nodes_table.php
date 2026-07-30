<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location_nodes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->uuid('parent_id')->nullable();
            $table->string('tier'); // country, region, county, destination, spot
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('banner_image_url')->nullable();
            $table->text('description')->nullable();
            $table->integer('spot_count')->default(0);
            $table->integer('active_explorers')->default(0);
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('location_nodes')->nullOnDelete();
            $table->index(['tier', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_nodes');
    }
};
