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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('teacher_name');
            $table->string('teacher_email');
            $table->string('slug')->unique();
            $table->string('father_name')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('teacher_cnic')->nullable();
            $table->string('gender');
            $table->string('teacher_status');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
