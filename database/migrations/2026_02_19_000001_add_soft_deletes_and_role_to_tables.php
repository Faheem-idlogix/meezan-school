<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add deleted_at to exams if not exists
        if (!Schema::hasColumn('exams', 'deleted_at')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add deleted_at to exam_results if not exists
        if (!Schema::hasColumn('exam_results', 'deleted_at')) {
            Schema::table('exam_results', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add role column to users table if not exists
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('admin')->after('user_type_id');
                // roles: admin, teacher, student, accountant
            });
        }

        // Add deleted_at to sessions if not exists
        if (!Schema::hasColumn('sessions', 'deleted_at')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add deleted_at to class_fee_vouchers if not exists
        if (!Schema::hasColumn('class_fee_vouchers', 'deleted_at')) {
            Schema::table('class_fee_vouchers', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add deleted_at to vouchers if not exists
        if (!Schema::hasColumn('vouchers', 'deleted_at')) {
            Schema::table('vouchers', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add whatsapp_number to students if not exists
        if (!Schema::hasColumn('students', 'whatsapp_number')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('whatsapp_number')->nullable()->after('contact_no');
            });
        }

        // Add whatsapp_number to teachers if not exists
        if (!Schema::hasColumn('teachers', 'whatsapp_number')) {
            Schema::table('teachers', function (Blueprint $table) {
                $table->string('whatsapp_number')->nullable()->after('contact_no');
            });
        }
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('exam_results', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('class_fee_vouchers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('whatsapp_number');
        });
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('whatsapp_number');
        });
    }
};
