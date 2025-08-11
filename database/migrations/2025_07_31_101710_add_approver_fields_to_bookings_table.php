<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApproverFieldsToBookingsTable extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('first_approver_name')->nullable()->after('status');
            $table->timestamp('first_approved_at')->nullable()->after('first_approver_name');

            $table->string('second_approver_name')->nullable()->after('first_approved_at');
            $table->timestamp('second_approved_at')->nullable()->after('second_approver_name');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'first_approver_name',
                'first_approved_at',
                'second_approver_name',
                'second_approved_at'
            ]);
        });
    }
}

