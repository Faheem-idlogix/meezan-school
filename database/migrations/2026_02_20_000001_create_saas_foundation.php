<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Plans / Subscription Tiers ──────────────────────────────
        if (!Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');                         // Basic, Standard, Premium
                $table->decimal('price', 10, 2)->default(0);   // Monthly price
                $table->integer('max_students')->default(500);
                $table->integer('max_teachers')->default(50);
                $table->integer('duration_days')->default(30);
                $table->json('features')->nullable();           // {"whatsapp":true,"sms":false,...}
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // ── Schools ─────────────────────────────────────────────────
        if (!Schema::hasTable('schools')) {
            Schema::create('schools', function (Blueprint $table) {
                $table->id();
                $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
                $table->string('name');
                $table->string('subdomain')->unique()->nullable();
                $table->string('logo')->nullable();
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('principal_name')->nullable();
                $table->string('registration_no')->nullable();
                $table->string('whatsapp_number')->nullable();
                $table->enum('status', ['active', 'inactive', 'suspended', 'trial'])->default('trial');
                $table->date('subscription_start')->nullable();
                $table->date('subscription_end')->nullable();
                $table->integer('current_students')->default(0);
                $table->integer('current_teachers')->default(0);
                $table->json('settings')->nullable();           // theme, currency, etc.
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
        Schema::dropIfExists('plans');
    }
};
