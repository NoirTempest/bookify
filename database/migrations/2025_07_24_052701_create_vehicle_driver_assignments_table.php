<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_driver_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings');
            $table->foreignId('driver_id')->constrained('drivers');
            $table->foreignId('asset_detail_id')->constrained('asset_details');
            $table->date('assigned_date');
            $table->foreignId('assigned_by')->constrained('users');
            $table->decimal('odometer_start', 10, 2)->nullable();
            $table->decimal('odometer_end', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_driver_assignments');
    }
};
