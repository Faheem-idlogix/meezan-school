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
        Schema::table('student_fees', function (Blueprint $table) {
            $table->integer('received_payment_fee')->nullable();
            $table->integer('fee_charges_left')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            //
        });
    }
};
