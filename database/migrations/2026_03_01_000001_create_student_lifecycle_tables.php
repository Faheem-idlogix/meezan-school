<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Student Lifecycle Management — Admissions, Documents, Promotions, Behavior, Alumni, Transfer Certificates
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Admission Enquiries ──
        Schema::create('admission_enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('student_name');
            $table->string('father_name')->nullable();
            $table->string('contact_no');
            $table->string('email')->nullable();
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('previous_school')->nullable();
            $table->string('class_applied')->nullable();
            $table->foreignId('class_room_id')->nullable()->constrained('class_rooms')->nullOnDelete();
            $table->text('address')->nullable();
            $table->enum('status', ['enquiry', 'test_scheduled', 'test_taken', 'approved', 'rejected', 'enrolled'])->default('enquiry');
            $table->date('enquiry_date')->nullable();
            $table->date('test_date')->nullable();
            $table->decimal('test_marks', 5, 2)->nullable();
            $table->text('test_remarks')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('school_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // ── Student Documents ──
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('document_type'); // b_form, birth_certificate, leaving_certificate, photo, medical_report, etc.
            $table->string('document_title');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('verified_at')->nullable();
            $table->text('remarks')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // ── Student Behavior Records ──
        Schema::create('student_behaviors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_room_id')->nullable()->constrained('class_rooms')->nullOnDelete();
            $table->enum('type', ['positive', 'negative', 'neutral'])->default('neutral');
            $table->string('category'); // discipline, academic, social, sports, etc.
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('points')->default(0); // positive or negative
            $table->date('incident_date');
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('action_taken', ['none', 'verbal_warning', 'written_warning', 'parent_meeting', 'suspension', 'other'])->default('none');
            $table->text('action_details')->nullable();
            $table->foreignId('school_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // ── Transfer Certificates ──
        Schema::create('transfer_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('tc_number')->unique();
            $table->date('issue_date');
            $table->date('leaving_date')->nullable();
            $table->string('reason')->nullable(); // transfer, leaving, migration, etc.
            $table->text('conduct')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['draft', 'issued', 'cancelled'])->default('draft');
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('school_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // ── Alumni Records ──
        Schema::create('alumni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('student_name');
            $table->string('father_name')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('email')->nullable();
            $table->string('batch_year')->nullable(); // e.g., "2023-2024"
            $table->string('last_class')->nullable();
            $table->string('passing_year')->nullable();
            $table->string('current_institution')->nullable();
            $table->text('achievements')->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();
            $table->foreignId('school_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // ── Add admission_enquiry_id to students ──
        if (!Schema::hasColumn('students', 'admission_enquiry_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->foreignId('admission_enquiry_id')->nullable()->after('id');
                $table->string('student_id_card')->nullable()->after('student_status');
                $table->string('roll_number')->nullable()->after('student_id_card');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('students', 'admission_enquiry_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn(['admission_enquiry_id', 'student_id_card', 'roll_number']);
            });
        }
        Schema::dropIfExists('alumni');
        Schema::dropIfExists('transfer_certificates');
        Schema::dropIfExists('student_behaviors');
        Schema::dropIfExists('student_documents');
        Schema::dropIfExists('admission_enquiries');
    }
};
