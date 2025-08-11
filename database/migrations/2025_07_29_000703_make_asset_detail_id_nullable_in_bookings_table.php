<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['asset_detail_id']); // Drop foreign key first
            $table->unsignedBigInteger('asset_detail_id')->nullable()->change(); // Make it nullable
            $table->foreign('asset_detail_id')->references('id')->on('asset_details'); // Add FK back
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['asset_detail_id']);
            $table->unsignedBigInteger('asset_detail_id')->nullable(false)->change(); // Restore NOT NULL
            $table->foreign('asset_detail_id')->references('id')->on('asset_details');
        });
    }
};

