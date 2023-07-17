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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('student_name');
            $table->string('student_email');
            $table->string('slug')->unique();
            $table->string('father_name')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('student_cnic')->nullable();
            $table->string('student_dob')->nullable();
            $table->string('student_admission_date')->nullable();
            $table->string('gender');
            $table->foreignId('class_room_id')->on('class_rooms');
            $table->string('student_image')->nullable();
            $table->string('student_status');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
