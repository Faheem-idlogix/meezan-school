<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->enum('type', ['income', 'expense'])->default('income')->after('school_id');
            $table->string('category')->nullable()->after('type');       // e.g. Fee, Salary, Utility, Misc
            $table->string('description')->nullable()->after('category'); // narrative
            $table->string('reference_no')->nullable()->after('description'); // cheque/bank ref
            $table->date('voucher_date')->nullable()->after('reference_no');
            $table->enum('payment_mode', ['cash', 'bank', 'cheque', 'online'])->default('cash')->after('voucher_date');
        });

        // Back-fill existing journal vouchers as income (they're fee receipts from students)
        \DB::table('vouchers')->update([
            'type'         => 'income',
            'category'     => 'Student Fee',
            'voucher_date' => \DB::raw('DATE(created_at)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['type', 'category', 'description', 'reference_no', 'voucher_date', 'payment_mode']);
        });
    }
};

