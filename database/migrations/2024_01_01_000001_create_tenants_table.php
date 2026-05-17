<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subdomain')->unique()->nullable();
            $table->string('logo')->nullable();
            $table->string('custom_color', 20)->default('#6366f1');
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            
            // Subscription
            $table->enum('subscription_plan', ['basic', 'pro', 'enterprise'])->default('basic');
            $table->enum('status', ['active', 'inactive', 'trial', 'suspended'])->default('trial');
            $table->date('subscription_start')->nullable();
            $table->date('subscription_end')->nullable();
            
            // Limits based on plan
            $table->unsignedInteger('max_rooms')->default(2);
            $table->unsignedInteger('max_pcs')->default(10);
            $table->unsignedInteger('max_staff')->default(3);
            
            // Settings
            $table->json('settings')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
