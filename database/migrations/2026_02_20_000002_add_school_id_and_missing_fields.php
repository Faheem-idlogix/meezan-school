<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tables that need school_id
    private array $tables = [
        'users', 'students', 'teachers', 'class_rooms', 'subjects',
        'sessions', 'fees', 'vouchers', 'attendances', 'exams',
        'exam_results', 'student_fees', 'notices', 'class_fee_vouchers',
        'class_subjects',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'school_id')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    // Use ->after('id') only if the table has an 'id' column, else use ->first()
                    if (Schema::hasColumn($table, 'id')) {
                        $t->unsignedBigInteger('school_id')->default(1)->after('id');
                    } else {
                        $t->unsignedBigInteger('school_id')->default(1)->first();
                    }
                    $t->index('school_id');
                });
            }
        }

        // ── Missing student fields ───────────────────────────────────
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'date_of_birth'))
                $table->date('date_of_birth')->nullable();
            if (!Schema::hasColumn('students', 'gender'))
                $table->enum('gender', ['male', 'female', 'other'])->nullable();
            if (!Schema::hasColumn('students', 'blood_group'))
                $table->string('blood_group', 5)->nullable();
            if (!Schema::hasColumn('students', 'religion'))
                $table->string('religion')->nullable();
            if (!Schema::hasColumn('students', 'address'))
                $table->text('address')->nullable();
            if (!Schema::hasColumn('students', 'city'))
                $table->string('city')->nullable();
            if (!Schema::hasColumn('students', 'guardian_name'))
                $table->string('guardian_name')->nullable();
            if (!Schema::hasColumn('students', 'guardian_cnic'))
                $table->string('guardian_cnic')->nullable();
            if (!Schema::hasColumn('students', 'admission_date'))
                $table->date('admission_date')->nullable();
            if (!Schema::hasColumn('students', 'student_id_no'))
                $table->string('student_id_no')->nullable();
            if (!Schema::hasColumn('students', 'previous_school'))
                $table->string('previous_school')->nullable();
            if (!Schema::hasColumn('students', 'medical_notes'))
                $table->text('medical_notes')->nullable();
            if (!Schema::hasColumn('students', 'emergency_contact'))
                $table->string('emergency_contact')->nullable();
            if (!Schema::hasColumn('students', 'discount_percent'))
                $table->decimal('discount_percent', 5, 2)->default(0);
            if (!Schema::hasColumn('students', 'scholarship_note'))
                $table->string('scholarship_note')->nullable();
        });

        // ── Missing teacher fields ───────────────────────────────────
        Schema::table('teachers', function (Blueprint $table) {
            if (!Schema::hasColumn('teachers', 'date_of_birth'))
                $table->date('date_of_birth')->nullable();
            if (!Schema::hasColumn('teachers', 'gender'))
                $table->enum('gender', ['male', 'female', 'other'])->nullable();
            if (!Schema::hasColumn('teachers', 'blood_group'))
                $table->string('blood_group', 5)->nullable();
            if (!Schema::hasColumn('teachers', 'cnic'))
                $table->string('cnic')->nullable();
            if (!Schema::hasColumn('teachers', 'qualification'))
                $table->string('qualification')->nullable();
            if (!Schema::hasColumn('teachers', 'specialization'))
                $table->string('specialization')->nullable();
            if (!Schema::hasColumn('teachers', 'experience_years'))
                $table->integer('experience_years')->default(0);
            if (!Schema::hasColumn('teachers', 'joining_date'))
                $table->date('joining_date')->nullable();
            if (!Schema::hasColumn('teachers', 'basic_salary'))
                $table->decimal('basic_salary', 10, 2)->default(0);
            if (!Schema::hasColumn('teachers', 'employee_id'))
                $table->string('employee_id')->nullable();
            if (!Schema::hasColumn('teachers', 'address'))
                $table->text('address')->nullable();
            if (!Schema::hasColumn('teachers', 'bank_account'))
                $table->string('bank_account')->nullable();
            if (!Schema::hasColumn('teachers', 'bank_name'))
                $table->string('bank_name')->nullable();
        });
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'school_id')) {
                Schema::table($table, fn(Blueprint $t) => $t->dropColumn('school_id'));
            }
        }
    }
};
