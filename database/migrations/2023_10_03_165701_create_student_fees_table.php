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
        Schema::create('student_fees', function (Blueprint $table) {
            $table->id('student_fee_id');
            $table->foreignId('student_id')->on('students');
            $table->foreignId('class_fee_voucher_id')->on('class_fee_vouchers');
            $table->integer('voucher_no')->nullable();
            $table->string('fee_month')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('submit_date')->nullable();
            $table->integer('total_fee')->nullable();
            $table->integer('stationery_charges')->nullable();
            $table->integer('test_series_charges')->nullable();
            $table->integer('exam_charges')->nullable();
            $table->integer('fine')->nullable();
            $table->integer('arrears')->nullable();
            $table->integer('academic_fee')->nullable();
            $table->text('note')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_fees');
    }
};
