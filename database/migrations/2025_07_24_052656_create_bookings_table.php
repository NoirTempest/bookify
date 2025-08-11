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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_type_id')->constrained('asset_types');
            $table->foreignId('asset_detail_id')->constrained('asset_details');
            $table->foreignId('user_id')->constrained('users');
            $table->string('asset_name')->nullable(); // ✅ Added this
            $table->text('purpose');
            $table->integer('no_of_seats');
            $table->text('destination');
            $table->date('scheduled_date');
            $table->time('time_from');
            $table->time('time_to');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'declined', 'cancelled']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
