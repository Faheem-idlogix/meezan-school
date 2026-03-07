<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Advanced Fee & Finance — Fee structures, installments, late fees, scholarships, reminders
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Fee Structures (class-wise / category-wise fee configuration) ──
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_room_id')->constrained('class_rooms')->cascadeOnDelete();
            $table->foreignId('session_id')->nullable()->constrained('sessions')->nullOnDelete();
            $table->string('fee_category'); // tuition, admission, exam, transport, lab, sports, etc.
            $table->string('fee_name');
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('frequency', ['monthly', 'quarterly', 'semi_annual', 'annual', 'one_time'])->default('monthly');
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->foreignId('school_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // ── Fee Discounts / Scholarships ──
        Schema::create('fee_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 10, 2);
            $table->string('applicable_to')->default('all'); // all, sibling, merit, need_based, staff_child
            $table->foreignId('class_room_id')->nullable()->constrained('class_rooms')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->foreignId('school_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // ── Student Fee Discounts (pivot) ──
        Schema::create('student_fee_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fee_discount_id')->constrained('fee_discounts')->cascadeOnDelete();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->string('approved_by')->nullable();
            $table->date('effective_from')->nullable();
            $table->date('effective_until')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        // ── Fee Installment Plans ──
        Schema::create('fee_installment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('plan_name')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->integer('number_of_installments');
            $table->decimal('installment_amount', 10, 2);
            $table->date('start_date');
            $table->enum('status', ['active', 'completed', 'defaulted', 'cancelled'])->default('active');
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('school_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // ── Fee Installments (individual installment records) ──
        Schema::create('fee_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_installment_plan_id')->constrained('fee_installment_plans')->cascadeOnDelete();
            $table->integer('installment_number');
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('late_fee', 10, 2)->default(0);
            $table->enum('status', ['pending', 'paid', 'partial', 'overdue'])->default('pending');
            $table->string('payment_method')->nullable(); // cash, bank, online
            $table->string('receipt_number')->nullable();
            $table->text('remarks')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // ── Late Fee Rules ──
        Schema::create('late_fee_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('class_room_id')->nullable()->constrained('class_rooms')->nullOnDelete();
            $table->integer('grace_days')->default(10); // days after due date before late fee applies
            $table->enum('charge_type', ['fixed', 'percentage', 'per_day'])->default('fixed');
            $table->decimal('charge_amount', 10, 2);
            $table->decimal('max_late_fee', 10, 2)->nullable(); // cap
            $table->boolean('is_active')->default(true);
            $table->foreignId('school_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // ── Fee Reminders ──
        Schema::create('fee_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('class_room_id')->nullable()->constrained('class_rooms')->nullOnDelete();
            $table->string('reminder_type'); // whatsapp, sms, email
            $table->string('fee_month')->nullable();
            $table->decimal('amount_due', 10, 2)->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->foreignId('school_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // ── Add fee_structure columns to existing student_fees ──
        if (!Schema::hasColumn('student_fees', 'late_fee_amount')) {
            Schema::table('student_fees', function (Blueprint $table) {
                $table->decimal('late_fee_amount', 10, 2)->default(0)->after('fine');
                $table->decimal('discount_amount', 10, 2)->default(0)->after('late_fee_amount');
                $table->string('payment_method')->nullable()->after('status');
                $table->string('receipt_number')->nullable()->after('payment_method');
            });
        }

        // ── Add fee category fields to class_fee_vouchers ──
        if (!Schema::hasColumn('class_fee_vouchers', 'due_date')) {
            Schema::table('class_fee_vouchers', function (Blueprint $table) {
                $table->date('due_date')->nullable()->after('month');
                $table->decimal('late_fee_per_day', 10, 2)->default(0)->after('due_date');
                $table->integer('grace_days')->default(10)->after('late_fee_per_day');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('class_fee_vouchers', 'due_date')) {
            Schema::table('class_fee_vouchers', function (Blueprint $table) {
                $table->dropColumn(['due_date', 'late_fee_per_day', 'grace_days']);
            });
        }
        if (Schema::hasColumn('student_fees', 'late_fee_amount')) {
            Schema::table('student_fees', function (Blueprint $table) {
                $table->dropColumn(['late_fee_amount', 'discount_amount', 'payment_method', 'receipt_number']);
            });
        }
        Schema::dropIfExists('fee_reminders');
        Schema::dropIfExists('late_fee_rules');
        Schema::dropIfExists('fee_installments');
        Schema::dropIfExists('fee_installment_plans');
        Schema::dropIfExists('student_fee_discounts');
        Schema::dropIfExists('fee_discounts');
        Schema::dropIfExists('fee_structures');
    }
};
