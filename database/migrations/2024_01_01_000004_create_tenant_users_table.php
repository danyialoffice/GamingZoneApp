<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['tenant_id', 'user_id']);
            $table->index(['tenant_id', 'user_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_users');
    }
};
