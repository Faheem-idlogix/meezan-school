<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Advanced Exam System — Grading policies, GPA/CGPA, report cards, positions, analytics
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Grading Systems ──
        Schema::create('grading_systems', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Primary Grading", "Matric Grading", etc.
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('school_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // ── Grade Rules (individual grade definitions within a grading system) ──
        Schema::create('grade_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grading_system_id')->constrained()->cascadeOnDelete();
            $table->string('grade'); // A+, A, B+, B, C, D, F
            $table->string('grade_label')->nullable(); // Outstanding, Excellent, Good, etc.
            $table->decimal('min_percentage', 5, 2);
            $table->decimal('max_percentage', 5, 2);
            $table->decimal('grade_point', 3, 1)->default(0); // 4.0, 3.7, 3.3 etc for GPA
            $table->text('remarks')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // ── Exam Types (Exam categories for better organization) ──
        if (!Schema::hasColumn('exams', 'exam_type')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->string('exam_type')->default('term')->after('name'); // term, unit_test, mid_term, final, practical
                $table->string('session_year')->nullable()->after('exam_type');
                $table->foreignId('session_id')->nullable()->after('session_year');
                $table->decimal('weightage', 5, 2)->default(100)->after('session_id'); // % contribution to final result
                $table->foreignId('grading_system_id')->nullable()->after('weightage');
                $table->enum('status', ['draft', 'published', 'result_pending', 'result_published'])->default('draft')->after('grading_system_id');
                $table->text('description')->nullable()->after('status');
            });
        }

        // ── Add grade info to exam_results ──
        if (!Schema::hasColumn('exam_results', 'grade')) {
            Schema::table('exam_results', function (Blueprint $table) {
                $table->string('grade')->nullable()->after('obtained_marks');
                $table->decimal('grade_point', 3, 1)->nullable()->after('grade');
                $table->decimal('percentage', 5, 2)->nullable()->after('grade_point');
                $table->integer('class_position')->nullable()->after('percentage');
                $table->integer('subject_position')->nullable()->after('class_position');
                $table->text('teacher_remarks')->nullable()->after('subject_position');
                $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('teacher_remarks');
                $table->foreignId('approved_by')->nullable()->after('approval_status');
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            });
        }

        // ── Report Card Configurations ──
        Schema::create('report_card_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('class_room_id')->nullable()->constrained('class_rooms')->nullOnDelete();
            $table->foreignId('grading_system_id')->nullable()->constrained()->nullOnDelete();
            $table->string('school_name')->nullable();
            $table->string('school_logo')->nullable();
            $table->string('school_address')->nullable();
            $table->string('school_phone')->nullable();
            $table->boolean('show_grade')->default(true);
            $table->boolean('show_gpa')->default(true);
            $table->boolean('show_position')->default(true);
            $table->boolean('show_percentage')->default(true);
            $table->boolean('show_remarks')->default(true);
            $table->boolean('show_attendance')->default(true);
            $table->boolean('show_behavior')->default(false);
            $table->text('principal_signature')->nullable();
            $table->text('class_teacher_signature')->nullable();
            $table->text('header_note')->nullable();
            $table->text('footer_note')->nullable();
            $table->boolean('is_default')->default(false);
            $table->foreignId('school_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // ── Exam Schedules (date-sheet per exam) ──
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_room_id')->constrained('class_rooms')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->date('exam_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('room')->nullable();
            $table->integer('total_marks')->nullable();
            $table->integer('passing_marks')->nullable();
            $table->text('instructions')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_schedules');
        Schema::dropIfExists('report_card_configs');

        if (Schema::hasColumn('exam_results', 'grade')) {
            Schema::table('exam_results', function (Blueprint $table) {
                $table->dropColumn(['grade', 'grade_point', 'percentage', 'class_position', 'subject_position', 'teacher_remarks', 'approval_status', 'approved_by', 'approved_at']);
            });
        }

        if (Schema::hasColumn('exams', 'exam_type')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->dropColumn(['exam_type', 'session_year', 'session_id', 'weightage', 'grading_system_id', 'status', 'description']);
            });
        }

        Schema::dropIfExists('grade_rules');
        Schema::dropIfExists('grading_systems');
    }
};
