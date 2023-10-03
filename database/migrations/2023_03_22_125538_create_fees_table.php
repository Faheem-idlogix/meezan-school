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
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->on('sessions');
            $table->date('fee_month')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('submit_date')->nullable();
            $table->integer('total_fee')->nullable();
            $table->integer('discount')->nullable();
            $table->integer('test_series_charges')->nullable();
            $table->integer('exam_charges')->nullable();
            $table->integer('practical_fee')->nullable();
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
