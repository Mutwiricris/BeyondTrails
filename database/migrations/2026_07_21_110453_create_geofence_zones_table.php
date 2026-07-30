<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('geofence_zones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('center_lat', 10, 7);
            $table->decimal('center_lng', 10, 7);
            $table->integer('radius_meters')->default(100);
            $table->string('category'); // hiddenGem, destination, tour, emergency, quest
            
            $table->boolean('trigger_on_entry')->default(true);
            $table->boolean('trigger_on_dwell')->default(false);
            $table->integer('dwell_seconds')->default(0);
            $table->boolean('trigger_on_exit')->default(false);
            $table->integer('throttle_hours')->default(24);
            
            $table->string('notification_title')->nullable();
            $table->string('notification_body')->nullable();
            $table->string('notification_icon')->nullable();
            
            $table->uuidMorphs('linked_model'); // linked_model_type, linked_model_id
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category', 'is_active']);
            $table->index(['center_lat', 'center_lng']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('geofence_zones');
    }
};
