<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('pc_id')->constrained()->onDelete('cascade');
            
            // Booking Details
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->decimal('hours', 5, 2);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            
            // Status: pending (temporary), confirmed, completed, expired, cancelled
            $table->enum('status', ['pending', 'confirmed', 'completed', 'expired', 'cancelled','temporary'])->default('pending');
            
            // Approval
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            // Booking expiration (1 hour timer)
            $table->timestamp('expires_at')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            // Check-in
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'start_time', 'end_time']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
