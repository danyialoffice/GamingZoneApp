<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add booking group ID for linking related bookings (multiple PCs booked together)
            $table->string('booking_group_id')->nullable()->after('id');
            
            // Add index for faster grouping queries
            $table->index(['booking_group_id', 'tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['booking_group_id', 'tenant_id']);
            $table->dropColumn('booking_group_id');
        });
    }
};
