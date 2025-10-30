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
            //
            $table->integer('notebook_charges')->nullable()->after('exam_charges');
            $table->integer('book_charges')->nullable()->after('notebook_charges');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            //
            $table->dropColumn(['notebook_charges', 'book_charges']);
        });
    }
};
