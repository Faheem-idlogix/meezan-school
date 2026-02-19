<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Daily Diary ─────────────────────────────────────────────
        if (!Schema::hasTable('diaries')) {
            Schema::create('diaries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->default(1)->index();
                $table->foreignId('class_room_id')->constrained()->cascadeOnDelete();
                $table->date('diary_date');
                $table->string('title');
                $table->text('description')->nullable();
                $table->text('homework')->nullable();
                $table->text('important_notes')->nullable();
                $table->string('subject')->nullable();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->boolean('whatsapp_sent')->default(false);
                $table->timestamp('whatsapp_sent_at')->nullable();
                $table->integer('whatsapp_recipients')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // ── Timetable ───────────────────────────────────────────────
        if (!Schema::hasTable('timetables')) {
            Schema::create('timetables', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->default(1)->index();
                $table->foreignId('class_room_id')->constrained()->cascadeOnDelete();
                $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
                $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
                $table->foreignId('session_id')->nullable()->constrained()->nullOnDelete();
                $table->enum('day', ['monday','tuesday','wednesday','thursday','friday','saturday','sunday']);
                $table->time('start_time');
                $table->time('end_time');
                $table->string('room_no')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // ── Leave Requests ──────────────────────────────────────────
        if (!Schema::hasTable('leave_requests')) {
            Schema::create('leave_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->default(1)->index();
                $table->morphs('leavable');                     // student or teacher
                $table->enum('leave_type', ['sick', 'casual', 'annual', 'emergency', 'maternity', 'other']);
                $table->date('from_date');
                $table->date('to_date');
                $table->integer('total_days')->default(1);
                $table->text('reason');
                $table->string('document')->nullable();         // medical certificate etc.
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // ── Student Promotions ──────────────────────────────────────
        if (!Schema::hasTable('student_promotions')) {
            Schema::create('student_promotions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->default(1)->index();
                $table->foreignId('student_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('from_class_id');
                $table->unsignedBigInteger('to_class_id');
                $table->unsignedBigInteger('from_session_id');
                $table->unsignedBigInteger('to_session_id');
                $table->date('promotion_date');
                $table->enum('status', ['promoted', 'detained', 'transferred', 'left']);
                $table->text('remarks')->nullable();
                $table->unsignedBigInteger('promoted_by');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_promotions');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('diaries');
    }
};
