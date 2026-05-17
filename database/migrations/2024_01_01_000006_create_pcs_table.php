<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pcs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('name'); // PC-01, PC-02, etc.
            $table->text('specs')->nullable(); // CPU, GPU, RAM, etc.
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->enum('status', ['available', 'occupied', 'maintenance', 'offline'])->default('available');
            $table->string('ip_address')->nullable();
            $table->string('mac_address')->nullable();
            $table->timestamps();
            
            $table->index(['tenant_id', 'room_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pcs');
    }
};
