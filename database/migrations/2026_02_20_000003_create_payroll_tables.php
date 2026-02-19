<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Payrolls ────────────────────────────────────────────────
        if (!Schema::hasTable('payrolls')) {
            Schema::create('payrolls', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->default(1)->index();
                $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
                $table->tinyInteger('month');                    // 1-12
                $table->year('year');
                // Earnings
                $table->decimal('basic_salary', 10, 2)->default(0);
                $table->decimal('house_rent_allowance', 10, 2)->default(0);
                $table->decimal('medical_allowance', 10, 2)->default(0);
                $table->decimal('transport_allowance', 10, 2)->default(0);
                $table->decimal('bonus', 10, 2)->default(0);
                $table->decimal('other_allowances', 10, 2)->default(0);
                $table->decimal('total_earnings', 10, 2)->storedAs(
                    'basic_salary + house_rent_allowance + medical_allowance + transport_allowance + bonus + other_allowances'
                );
                // Deductions
                $table->decimal('advance_deduction', 10, 2)->default(0);
                $table->decimal('absence_deduction', 10, 2)->default(0);
                $table->decimal('tax_deduction', 10, 2)->default(0);
                $table->decimal('other_deductions', 10, 2)->default(0);
                $table->decimal('total_deductions', 10, 2)->storedAs(
                    'advance_deduction + absence_deduction + tax_deduction + other_deductions'
                );
                // Net
                $table->decimal('net_salary', 10, 2)->storedAs(
                    'basic_salary + house_rent_allowance + medical_allowance + transport_allowance + bonus + other_allowances - advance_deduction - absence_deduction - tax_deduction - other_deductions'
                );
                $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
                $table->date('paid_date')->nullable();
                $table->string('payment_method')->nullable();   // cash, bank, cheque
                $table->string('cheque_no')->nullable();
                $table->text('notes')->nullable();
                $table->integer('working_days')->default(26);
                $table->integer('present_days')->default(26);
                $table->integer('absent_days')->default(0);
                $table->integer('leave_days')->default(0);
                $table->boolean('whatsapp_sent')->default(false);
                $table->timestamp('whatsapp_sent_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->unique(['teacher_id', 'month', 'year'], 'payroll_month_unique');
            });
        }

        // ── Payroll Advances ────────────────────────────────────────
        if (!Schema::hasTable('payroll_advances')) {
            Schema::create('payroll_advances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->default(1)->index();
                $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
                $table->decimal('amount', 10, 2);
                $table->date('advance_date');
                $table->string('reason')->nullable();
                $table->boolean('is_deducted')->default(false);
                $table->tinyInteger('deduct_month')->nullable();
                $table->year('deduct_year')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_advances');
        Schema::dropIfExists('payrolls');
    }
};
