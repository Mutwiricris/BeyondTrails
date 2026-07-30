<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('destination_id')->nullable(); // UUID of destination
            $table->string('operator_id')->nullable();   // UUID of operator
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->unsignedSmallInteger('adults')->default(1);
            $table->unsignedSmallInteger('children')->default(0);
            $table->unsignedSmallInteger('total_participants')->default(1);
            $table->unsignedBigInteger('total_price_kes')->default(0);
            $table->unsignedBigInteger('deposit_paid_kes')->default(0);
            $table->date('booking_date')->nullable(); // the tour date
            $table->enum('payment_method', ['mpesa', 'card', 'bank_transfer', 'cash'])->nullable();
            $table->enum('payment_status', ['pending', 'partial', 'completed', 'failed'])->default('pending');
            $table->string('confirmation_code')->unique()->nullable();
            $table->text('special_requests')->nullable();
            $table->text('operator_notes')->nullable();
            $table->text('cancelled_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
