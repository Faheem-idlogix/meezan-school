<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;
use App\Models\Role;

/**
 * Production-safe migration to sync ALL permissions and role assignments.
 * Uses updateOrCreate so it is idempotent — safe to re-run.
 * This ensures the database always has the complete permission set
 * matching the latest codebase modules.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Skip if tables don't exist yet (fresh install runs the create migration first)
        if (!Schema::hasTable('permissions') || !Schema::hasTable('roles')) {
            return;
        }

        // ════════════════════════════════════════
        //  1. Sync all permissions
        // ════════════════════════════════════════
        $permissions = $this->getAllPermissions();

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['name' => $perm['name']],
                [
                    'display_name' => $perm['display_name'],
                    'module'       => $perm['module'],
                    'group'        => $perm['group'],
                ]
            );
        }

        // ════════════════════════════════════════
        //  2. Ensure all roles exist
        // ════════════════════════════════════════
        $roles = [
            ['name' => 'super_admin',  'display_name' => 'Super Administrator', 'is_system' => true,  'description' => 'Full system access. Manages schools, plans, and all settings.'],
            ['name' => 'admin',        'display_name' => 'School Administrator','is_system' => true,  'description' => 'Full access to all school modules except super admin features.'],
            ['name' => 'teacher',      'display_name' => 'Teacher',            'is_system' => true,  'description' => 'Access to attendance, diary, exams, leave, and student records for assigned classes.'],
            ['name' => 'accountant',   'display_name' => 'Accountant',         'is_system' => true,  'description' => 'Access to finance, fees, payroll, and financial reports.'],
            ['name' => 'student',      'display_name' => 'Student / Parent',   'is_system' => true,  'description' => 'View-only access to own results, attendance, diary, and fee status.'],
            ['name' => 'receptionist', 'display_name' => 'Receptionist',       'is_system' => false, 'description' => 'Access to admission enquiries, student registration, and notices.'],
        ];

        foreach ($roles as $r) {
            Role::updateOrCreate(['name' => $r['name']], $r);
        }

        // ════════════════════════════════════════
        //  3. Sync role → permission assignments
        // ════════════════════════════════════════

        // Admin gets ALL permissions (except super_admin module)
        $adminRole = Role::where('name', 'admin')->first();
        $adminPerms = Permission::where('module', '!=', 'super_admin')->pluck('id')->toArray();
        $adminRole->permissions()->syncWithoutDetaching($adminPerms);

        // Teacher
        $this->assignPermsToRole('teacher', [
            'students.view', 'student_documents.view',
            'attendance.view', 'attendance.create',
            'diary.view', 'diary.create', 'diary.edit',
            'leave.view', 'leave.create',
            'exams.view',
            'exam_results.view', 'exam_results.create', 'exam_results.edit',
            'exam_schedules.view',
            'timetable.view',
            'behavior.view', 'behavior.create', 'behavior.edit',
            'notices.view',
            'reports.view', 'reports.attendance', 'reports.exams',
            'grading.view',
            'report_cards.view',
            'notifications.view', 'notifications.create',
            'classes.view', 'subjects.view', 'class_subjects.view',
        ]);

        // Accountant
        $this->assignPermsToRole('accountant', [
            'students.view',
            'finance.view', 'finance.create',
            'fees.view', 'fees.create', 'fees.edit', 'fees.collect',
            'fee_structure.view', 'fee_structure.create', 'fee_structure.edit',
            'fee_discounts.view', 'fee_discounts.create', 'fee_discounts.edit',
            'fee_installments.view', 'fee_installments.create', 'fee_installments.edit',
            'late_fee.view', 'late_fee.create', 'late_fee.edit',
            'payroll.view', 'payroll.create', 'payroll.edit',
            'reports.view', 'reports.finance', 'reports.fees',
            'notices.view',
            'classes.view',
            'voucher_status.view',
        ]);

        // Student
        $this->assignPermsToRole('student', [
            'students.view',
            'student_documents.view',
            'attendance.view',
            'diary.view',
            'exams.view',
            'exam_results.view',
            'exam_schedules.view',
            'timetable.view',
            'notices.view',
            'report_cards.view',
            'fees.view',
            'leave.view', 'leave.create',
        ]);

        // Receptionist
        $this->assignPermsToRole('receptionist', [
            'students.view', 'students.create',
            'student_documents.view', 'student_documents.upload',
            'admission.view', 'admission.create', 'admission.edit',
            'notices.view', 'notices.create',
            'attendance.view',
            'classes.view',
            'fees.view',
            'whatsapp.view',
            'notifications.view',
        ]);
    }

    public function down(): void
    {
        // Permissions are additive — no destructive rollback needed.
        // Individual permissions can be manually removed if necessary.
    }

    /**
     * Helper: assign permission names to a role (syncWithoutDetaching = won't remove existing).
     */
    private function assignPermsToRole(string $roleName, array $permNames): void
    {
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            return;
        }
        $ids = Permission::whereIn('name', $permNames)->pluck('id')->toArray();
        $role->permissions()->syncWithoutDetaching($ids);
    }

    /**
     * Complete permission registry — every module in the system.
     */
    private function getAllPermissions(): array
    {
        return [
            // ═══ ACADEMIC ═══
            ['name' => 'students.view',     'display_name' => 'View Students',      'module' => 'students',     'group' => 'Academic'],
            ['name' => 'students.create',   'display_name' => 'Create Students',    'module' => 'students',     'group' => 'Academic'],
            ['name' => 'students.edit',     'display_name' => 'Edit Students',      'module' => 'students',     'group' => 'Academic'],
            ['name' => 'students.delete',   'display_name' => 'Delete Students',    'module' => 'students',     'group' => 'Academic'],
            ['name' => 'students.restore',  'display_name' => 'Restore Deleted Students', 'module' => 'students', 'group' => 'Academic'],

            ['name' => 'student_documents.view',   'display_name' => 'View Student Documents',   'module' => 'student_documents', 'group' => 'Academic'],
            ['name' => 'student_documents.upload', 'display_name' => 'Upload Student Documents', 'module' => 'student_documents', 'group' => 'Academic'],
            ['name' => 'student_documents.verify', 'display_name' => 'Verify Student Documents', 'module' => 'student_documents', 'group' => 'Academic'],
            ['name' => 'student_documents.delete', 'display_name' => 'Delete Student Documents', 'module' => 'student_documents', 'group' => 'Academic'],

            ['name' => 'admission.view',    'display_name' => 'View Admissions',    'module' => 'admission',    'group' => 'Academic'],
            ['name' => 'admission.create',  'display_name' => 'Create Admissions',  'module' => 'admission',    'group' => 'Academic'],
            ['name' => 'admission.edit',    'display_name' => 'Edit Admissions',    'module' => 'admission',    'group' => 'Academic'],
            ['name' => 'admission.delete',  'display_name' => 'Delete Admissions',  'module' => 'admission',    'group' => 'Academic'],
            ['name' => 'admission.approve', 'display_name' => 'Approve Admissions', 'module' => 'admission',    'group' => 'Academic'],

            ['name' => 'behavior.view',     'display_name' => 'View Behavior',      'module' => 'behavior',     'group' => 'Academic'],
            ['name' => 'behavior.create',   'display_name' => 'Create Behavior',    'module' => 'behavior',     'group' => 'Academic'],
            ['name' => 'behavior.edit',     'display_name' => 'Edit Behavior',      'module' => 'behavior',     'group' => 'Academic'],
            ['name' => 'behavior.delete',   'display_name' => 'Delete Behavior',    'module' => 'behavior',     'group' => 'Academic'],

            ['name' => 'transfer_certificates.view',   'display_name' => 'View Transfer Certificates',   'module' => 'transfer_certificates', 'group' => 'Academic'],
            ['name' => 'transfer_certificates.create', 'display_name' => 'Create Transfer Certificates', 'module' => 'transfer_certificates', 'group' => 'Academic'],
            ['name' => 'transfer_certificates.issue',  'display_name' => 'Issue Transfer Certificates',  'module' => 'transfer_certificates', 'group' => 'Academic'],
            ['name' => 'transfer_certificates.delete', 'display_name' => 'Delete Transfer Certificates', 'module' => 'transfer_certificates', 'group' => 'Academic'],

            ['name' => 'alumni.view',       'display_name' => 'View Alumni',        'module' => 'alumni',       'group' => 'Academic'],
            ['name' => 'alumni.create',     'display_name' => 'Create Alumni',      'module' => 'alumni',       'group' => 'Academic'],
            ['name' => 'alumni.edit',       'display_name' => 'Edit Alumni',        'module' => 'alumni',       'group' => 'Academic'],
            ['name' => 'alumni.delete',     'display_name' => 'Delete Alumni',      'module' => 'alumni',       'group' => 'Academic'],

            ['name' => 'teachers.view',     'display_name' => 'View Teachers',      'module' => 'teachers',     'group' => 'Academic'],
            ['name' => 'teachers.create',   'display_name' => 'Create Teachers',    'module' => 'teachers',     'group' => 'Academic'],
            ['name' => 'teachers.edit',     'display_name' => 'Edit Teachers',      'module' => 'teachers',     'group' => 'Academic'],
            ['name' => 'teachers.delete',   'display_name' => 'Delete Teachers',    'module' => 'teachers',     'group' => 'Academic'],
            ['name' => 'teachers.restore',  'display_name' => 'Restore Deleted Teachers', 'module' => 'teachers', 'group' => 'Academic'],

            ['name' => 'classes.view',      'display_name' => 'View Classes',       'module' => 'classes',      'group' => 'Academic'],
            ['name' => 'classes.create',    'display_name' => 'Create Classes',     'module' => 'classes',      'group' => 'Academic'],
            ['name' => 'classes.edit',      'display_name' => 'Edit Classes',       'module' => 'classes',      'group' => 'Academic'],
            ['name' => 'classes.delete',    'display_name' => 'Delete Classes',     'module' => 'classes',      'group' => 'Academic'],
            ['name' => 'classes.restore',   'display_name' => 'Restore Deleted Classes', 'module' => 'classes', 'group' => 'Academic'],

            ['name' => 'subjects.view',     'display_name' => 'View Subjects',      'module' => 'subjects',     'group' => 'Academic'],
            ['name' => 'subjects.create',   'display_name' => 'Create Subjects',    'module' => 'subjects',     'group' => 'Academic'],
            ['name' => 'subjects.edit',     'display_name' => 'Edit Subjects',      'module' => 'subjects',     'group' => 'Academic'],
            ['name' => 'subjects.delete',   'display_name' => 'Delete Subjects',    'module' => 'subjects',     'group' => 'Academic'],

            ['name' => 'class_subjects.view',   'display_name' => 'View Class Subjects',      'module' => 'class_subjects', 'group' => 'Academic'],
            ['name' => 'class_subjects.create', 'display_name' => 'Assign Subjects to Class', 'module' => 'class_subjects', 'group' => 'Academic'],
            ['name' => 'class_subjects.delete', 'display_name' => 'Remove Class Subject',     'module' => 'class_subjects', 'group' => 'Academic'],

            ['name' => 'sessions.view',     'display_name' => 'View Sessions',      'module' => 'sessions',     'group' => 'Academic'],
            ['name' => 'sessions.create',   'display_name' => 'Create Sessions',    'module' => 'sessions',     'group' => 'Academic'],
            ['name' => 'sessions.edit',     'display_name' => 'Edit Sessions',      'module' => 'sessions',     'group' => 'Academic'],
            ['name' => 'sessions.delete',   'display_name' => 'Delete Sessions',    'module' => 'sessions',     'group' => 'Academic'],

            ['name' => 'timetable.view',    'display_name' => 'View Timetable',     'module' => 'timetable',    'group' => 'Academic'],
            ['name' => 'timetable.create',  'display_name' => 'Create Timetable',   'module' => 'timetable',    'group' => 'Academic'],
            ['name' => 'timetable.delete',  'display_name' => 'Delete Timetable',   'module' => 'timetable',    'group' => 'Academic'],

            ['name' => 'attendance.view',   'display_name' => 'View Attendance',    'module' => 'attendance',   'group' => 'Academic'],
            ['name' => 'attendance.create', 'display_name' => 'Mark Attendance',    'module' => 'attendance',   'group' => 'Academic'],

            ['name' => 'leave.view',        'display_name' => 'View Leaves',        'module' => 'leave',        'group' => 'Academic'],
            ['name' => 'leave.create',      'display_name' => 'Create Leaves',      'module' => 'leave',        'group' => 'Academic'],
            ['name' => 'leave.approve',     'display_name' => 'Approve/Reject Leaves', 'module' => 'leave',     'group' => 'Academic'],

            ['name' => 'exams.view',        'display_name' => 'View Exams',         'module' => 'exams',        'group' => 'Academic'],
            ['name' => 'exams.create',      'display_name' => 'Create Exams',       'module' => 'exams',        'group' => 'Academic'],
            ['name' => 'exams.edit',        'display_name' => 'Edit Exams',         'module' => 'exams',        'group' => 'Academic'],
            ['name' => 'exams.delete',      'display_name' => 'Delete Exams',       'module' => 'exams',        'group' => 'Academic'],

            ['name' => 'exam_results.view',   'display_name' => 'View Exam Results',   'module' => 'exam_results',   'group' => 'Academic'],
            ['name' => 'exam_results.create', 'display_name' => 'Create Exam Results', 'module' => 'exam_results',   'group' => 'Academic'],
            ['name' => 'exam_results.edit',   'display_name' => 'Edit Exam Results',   'module' => 'exam_results',   'group' => 'Academic'],
            ['name' => 'exam_results.delete', 'display_name' => 'Delete Exam Results', 'module' => 'exam_results',   'group' => 'Academic'],

            ['name' => 'exam_schedules.view',   'display_name' => 'View Exam Schedules',   'module' => 'exam_schedules', 'group' => 'Academic'],
            ['name' => 'exam_schedules.create', 'display_name' => 'Create Exam Schedules', 'module' => 'exam_schedules', 'group' => 'Academic'],
            ['name' => 'exam_schedules.delete', 'display_name' => 'Delete Exam Schedules', 'module' => 'exam_schedules', 'group' => 'Academic'],

            ['name' => 'grading.view',      'display_name' => 'View Grading Systems',   'module' => 'grading',   'group' => 'Academic'],
            ['name' => 'grading.create',    'display_name' => 'Create Grading Systems',  'module' => 'grading',   'group' => 'Academic'],
            ['name' => 'grading.edit',      'display_name' => 'Edit Grading Systems',    'module' => 'grading',   'group' => 'Academic'],
            ['name' => 'grading.delete',    'display_name' => 'Delete Grading Systems',  'module' => 'grading',   'group' => 'Academic'],

            ['name' => 'report_cards.view',   'display_name' => 'View Report Cards',    'module' => 'report_cards', 'group' => 'Academic'],
            ['name' => 'report_cards.manage', 'display_name' => 'Manage Report Cards',  'module' => 'report_cards', 'group' => 'Academic'],

            ['name' => 'diary.view',        'display_name' => 'View Diary',          'module' => 'diary',        'group' => 'Academic'],
            ['name' => 'diary.create',      'display_name' => 'Create Diary',        'module' => 'diary',        'group' => 'Academic'],
            ['name' => 'diary.edit',        'display_name' => 'Edit Diary',          'module' => 'diary',        'group' => 'Academic'],
            ['name' => 'diary.delete',      'display_name' => 'Delete Diary',        'module' => 'diary',        'group' => 'Academic'],

            // ═══ FINANCE ═══
            ['name' => 'finance.view',      'display_name' => 'View Finance Hub',   'module' => 'finance',      'group' => 'Finance'],
            ['name' => 'finance.create',    'display_name' => 'Create Vouchers',     'module' => 'finance',      'group' => 'Finance'],

            ['name' => 'fees.view',         'display_name' => 'View Fees',           'module' => 'fees',         'group' => 'Finance'],
            ['name' => 'fees.create',       'display_name' => 'Create Fee Vouchers', 'module' => 'fees',         'group' => 'Finance'],
            ['name' => 'fees.edit',         'display_name' => 'Edit Fees',           'module' => 'fees',         'group' => 'Finance'],
            ['name' => 'fees.delete',       'display_name' => 'Delete Fees',         'module' => 'fees',         'group' => 'Finance'],
            ['name' => 'fees.collect',      'display_name' => 'Collect Fee Payments','module' => 'fees',         'group' => 'Finance'],

            ['name' => 'fee_structure.view',   'display_name' => 'View Fee Structure',   'module' => 'fee_structure',   'group' => 'Finance'],
            ['name' => 'fee_structure.create', 'display_name' => 'Create Fee Structure', 'module' => 'fee_structure',   'group' => 'Finance'],
            ['name' => 'fee_structure.edit',   'display_name' => 'Edit Fee Structure',   'module' => 'fee_structure',   'group' => 'Finance'],
            ['name' => 'fee_structure.delete', 'display_name' => 'Delete Fee Structure', 'module' => 'fee_structure',   'group' => 'Finance'],

            ['name' => 'fee_discounts.view',   'display_name' => 'View Fee Discounts',   'module' => 'fee_discounts',   'group' => 'Finance'],
            ['name' => 'fee_discounts.create', 'display_name' => 'Create Fee Discounts', 'module' => 'fee_discounts',   'group' => 'Finance'],
            ['name' => 'fee_discounts.edit',   'display_name' => 'Edit Fee Discounts',   'module' => 'fee_discounts',   'group' => 'Finance'],
            ['name' => 'fee_discounts.delete', 'display_name' => 'Delete Fee Discounts', 'module' => 'fee_discounts',   'group' => 'Finance'],

            ['name' => 'fee_installments.view',   'display_name' => 'View Installments',   'module' => 'fee_installments', 'group' => 'Finance'],
            ['name' => 'fee_installments.create', 'display_name' => 'Create Installments', 'module' => 'fee_installments', 'group' => 'Finance'],
            ['name' => 'fee_installments.edit',   'display_name' => 'Edit Installments',   'module' => 'fee_installments', 'group' => 'Finance'],

            ['name' => 'late_fee.view',     'display_name' => 'View Late Fee Rules', 'module' => 'late_fee',     'group' => 'Finance'],
            ['name' => 'late_fee.create',   'display_name' => 'Create Late Fee Rules','module' => 'late_fee',    'group' => 'Finance'],
            ['name' => 'late_fee.edit',     'display_name' => 'Edit Late Fee Rules', 'module' => 'late_fee',     'group' => 'Finance'],
            ['name' => 'late_fee.delete',   'display_name' => 'Delete Late Fee Rules','module' => 'late_fee',    'group' => 'Finance'],

            ['name' => 'payroll.view',      'display_name' => 'View Payroll',        'module' => 'payroll',      'group' => 'Finance'],
            ['name' => 'payroll.create',    'display_name' => 'Create Payroll',      'module' => 'payroll',      'group' => 'Finance'],
            ['name' => 'payroll.edit',      'display_name' => 'Edit Payroll',        'module' => 'payroll',      'group' => 'Finance'],
            ['name' => 'payroll.approve',   'display_name' => 'Approve Payroll',     'module' => 'payroll',      'group' => 'Finance'],

            ['name' => 'voucher_status.view', 'display_name' => 'View Voucher Status Overview', 'module' => 'voucher_status', 'group' => 'Finance'],

            // ═══ COMMUNICATION ═══
            ['name' => 'whatsapp.view',     'display_name' => 'View WhatsApp Hub',   'module' => 'whatsapp',     'group' => 'Communication'],
            ['name' => 'whatsapp.send',     'display_name' => 'Send WhatsApp',       'module' => 'whatsapp',     'group' => 'Communication'],

            ['name' => 'notices.view',      'display_name' => 'View Notices',        'module' => 'notices',      'group' => 'Communication'],
            ['name' => 'notices.create',    'display_name' => 'Create Notices',      'module' => 'notices',      'group' => 'Communication'],
            ['name' => 'notices.edit',      'display_name' => 'Edit Notices',        'module' => 'notices',      'group' => 'Communication'],
            ['name' => 'notices.delete',    'display_name' => 'Delete Notices',      'module' => 'notices',      'group' => 'Communication'],

            ['name' => 'notifications.view',   'display_name' => 'View Notifications',   'module' => 'notifications', 'group' => 'Communication'],
            ['name' => 'notifications.create', 'display_name' => 'Send Notifications',   'module' => 'notifications', 'group' => 'Communication'],
            ['name' => 'notifications.delete', 'display_name' => 'Delete Notifications', 'module' => 'notifications', 'group' => 'Communication'],

            // ═══ REPORTS ═══
            ['name' => 'reports.view',       'display_name' => 'View Reports Hub',    'module' => 'reports',      'group' => 'Reports'],
            ['name' => 'reports.finance',    'display_name' => 'Finance Reports',     'module' => 'reports',      'group' => 'Reports'],
            ['name' => 'reports.fees',       'display_name' => 'Fee Reports',         'module' => 'reports',      'group' => 'Reports'],
            ['name' => 'reports.attendance', 'display_name' => 'Attendance Reports',  'module' => 'reports',      'group' => 'Reports'],
            ['name' => 'reports.students',   'display_name' => 'Student Reports',     'module' => 'reports',      'group' => 'Reports'],
            ['name' => 'reports.exams',      'display_name' => 'Exam Reports',        'module' => 'reports',      'group' => 'Reports'],

            // ═══ ADMINISTRATION ═══
            ['name' => 'users.view',        'display_name' => 'View Users',          'module' => 'users',        'group' => 'Administration'],
            ['name' => 'users.create',      'display_name' => 'Create Users',        'module' => 'users',        'group' => 'Administration'],
            ['name' => 'users.edit',        'display_name' => 'Edit Users',          'module' => 'users',        'group' => 'Administration'],
            ['name' => 'users.delete',      'display_name' => 'Delete Users',        'module' => 'users',        'group' => 'Administration'],
            ['name' => 'users.restore',     'display_name' => 'Restore Deleted Users','module' => 'users',       'group' => 'Administration'],

            ['name' => 'roles.view',        'display_name' => 'View Roles',          'module' => 'roles',        'group' => 'Administration'],
            ['name' => 'roles.create',      'display_name' => 'Create Roles',        'module' => 'roles',        'group' => 'Administration'],
            ['name' => 'roles.edit',        'display_name' => 'Edit Roles',          'module' => 'roles',        'group' => 'Administration'],
            ['name' => 'roles.delete',      'display_name' => 'Delete Roles',        'module' => 'roles',        'group' => 'Administration'],

            ['name' => 'settings.view',     'display_name' => 'View Settings',       'module' => 'settings',     'group' => 'Administration'],
            ['name' => 'settings.edit',     'display_name' => 'Edit Settings',       'module' => 'settings',     'group' => 'Administration'],

            ['name' => 'database_backup.view',     'display_name' => 'View Database Backups',     'module' => 'database_backup', 'group' => 'Administration'],
            ['name' => 'database_backup.create',   'display_name' => 'Create Database Backup',    'module' => 'database_backup', 'group' => 'Administration'],
            ['name' => 'database_backup.download',  'display_name' => 'Download Database Backup', 'module' => 'database_backup', 'group' => 'Administration'],
            ['name' => 'database_backup.delete',   'display_name' => 'Delete Database Backup',    'module' => 'database_backup', 'group' => 'Administration'],

            ['name' => 'activity_logs.view',   'display_name' => 'View Activity Logs',   'module' => 'activity_logs', 'group' => 'Administration'],
            ['name' => 'activity_logs.delete', 'display_name' => 'Delete Activity Logs', 'module' => 'activity_logs', 'group' => 'Administration'],

            ['name' => 'error_logs.view',   'display_name' => 'View Error Logs',   'module' => 'error_logs', 'group' => 'Administration'],
            ['name' => 'error_logs.delete', 'display_name' => 'Delete Error Logs', 'module' => 'error_logs', 'group' => 'Administration'],

            // ═══ SUPER ADMIN ═══
            ['name' => 'super_admin.dashboard',      'display_name' => 'Super Admin Dashboard', 'module' => 'super_admin', 'group' => 'Super Admin'],
            ['name' => 'super_admin.manage_schools', 'display_name' => 'Manage Schools',        'module' => 'super_admin', 'group' => 'Super Admin'],
            ['name' => 'super_admin.manage_plans',   'display_name' => 'Manage Plans',          'module' => 'super_admin', 'group' => 'Super Admin'],
        ];
    }
};
