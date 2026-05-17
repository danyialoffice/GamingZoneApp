<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->decimal('amount', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2);
            
            // Payment Method
            $table->enum('payment_method', ['cash', 'card', 'online', 'wallet'])->default('cash');
            $table->string('transaction_id')->nullable();
            
            // Status: pending, completed, refunded, failed
            $table->enum('status', ['pending', 'completed', 'refunded', 'failed'])->default('pending');
            
            // Payment Details
            $table->string('card_last_four')->nullable();
            $table->text('payment_details')->nullable();
            
            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['tenant_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
