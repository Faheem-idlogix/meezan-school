<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('whatsapp_logs')) {
            Schema::create('whatsapp_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->default(1);
                $table->string('to');                              // phone number sent to
                $table->string('recipient_name')->nullable();       // student/teacher name
                $table->string('recipient_type')->nullable();       // student / teacher
                $table->unsignedBigInteger('recipient_id')->nullable(); // student_id or teacher_id
                $table->string('message_type')->nullable();         // diary / notice / test / manual
                $table->text('message');
                $table->enum('status', ['sent', 'failed', 'pending'])->default('pending');
                $table->text('api_response')->nullable();
                $table->string('provider')->nullable();             // ultramsg / twilio / wati
                $table->timestamps();

                $table->index(['school_id', 'status']);
                $table->index('recipient_type');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
