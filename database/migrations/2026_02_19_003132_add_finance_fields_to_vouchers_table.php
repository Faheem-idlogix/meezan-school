<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $cols = Schema::getColumnListing('vouchers');

        Schema::table('vouchers', function (Blueprint $table) use ($cols) {
            if (!in_array('type', $cols)) {
                $table->enum('type', ['income', 'expense'])->default('income')->after('id');
            }
            if (!in_array('category', $cols)) {
                $table->string('category')->nullable();
            }
            if (!in_array('description', $cols)) {
                $table->string('description')->nullable();
            }
            if (!in_array('reference_no', $cols)) {
                $table->string('reference_no')->nullable();
            }
            if (!in_array('voucher_date', $cols)) {
                $table->date('voucher_date')->nullable();
            }
            if (!in_array('payment_mode', $cols)) {
                $table->enum('payment_mode', ['cash', 'bank', 'cheque', 'online'])->default('cash');
            }
        });

        // Back-fill existing vouchers as income (they're fee receipts from students)
        if (Schema::hasColumn('vouchers', 'type')) {
            \DB::table('vouchers')->whereNull('category')->update([
                'type'         => 'income',
                'category'     => 'Student Fee',
                'voucher_date' => \DB::raw('DATE(created_at)'),
            ]);
        }
    }

    public function down(): void
    {
        $cols = ['type', 'category', 'description', 'reference_no', 'voucher_date', 'payment_mode'];
        $drop = array_filter($cols, fn($c) => Schema::hasColumn('vouchers', $c));

        if (!empty($drop)) {
            Schema::table('vouchers', function (Blueprint $table) use ($drop) {
                $table->dropColumn($drop);
            });
        }
    }
};

