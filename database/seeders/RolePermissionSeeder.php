<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ════════════════════════════════════════════
        //  1. Create all permissions
        // ════════════════════════════════════════════
        $permissionsData = $this->getPermissions();

        foreach ($permissionsData as $perm) {
            Permission::updateOrCreate(
                ['name' => $perm['name']],
                [
                    'display_name' => $perm['display_name'],
                    'module'       => $perm['module'],
                    'group'        => $perm['group'],
                    'description'  => $perm['description'] ?? null,
                ]
            );
        }

        $this->command->info('✔ ' . count($permissionsData) . ' permissions seeded.');

        // ════════════════════════════════════════════
        //  2. Create roles
        // ════════════════════════════════════════════
        $roles = [
            [
                'name'         => 'super_admin',
                'display_name' => 'Super Administrator',
                'description'  => 'Full system access. Manages schools, plans, and all settings.',
                'is_system'    => true,
            ],
            [
                'name'         => 'admin',
                'display_name' => 'School Administrator',
                'description'  => 'Full access to all school modules except super admin features.',
                'is_system'    => true,
            ],
            [
                'name'         => 'teacher',
                'display_name' => 'Teacher',
                'description'  => 'Access to attendance, diary, exams, leave, and student records for assigned classes.',
                'is_system'    => true,
            ],
            [
                'name'         => 'accountant',
                'display_name' => 'Accountant',
                'description'  => 'Access to finance, fees, payroll, and financial reports.',
                'is_system'    => true,
            ],
            [
                'name'         => 'student',
                'display_name' => 'Student / Parent',
                'description'  => 'View-only access to own results, attendance, diary, and fee status.',
                'is_system'    => true,
            ],
            [
                'name'         => 'receptionist',
                'display_name' => 'Receptionist',
                'description'  => 'Access to admission enquiries, student registration, and notices.',
                'is_system'    => false,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }

        $this->command->info('✔ ' . count($roles) . ' roles seeded.');

        // ════════════════════════════════════════════
        //  3. Assign permissions to roles
        // ════════════════════════════════════════════

        // ── Admin: Everything except super_admin features ──
        $adminRole = Role::where('name', 'admin')->first();
        $allPermissions = Permission::pluck('name')->toArray();
        $adminRole->givePermission($allPermissions);
        $this->command->info('✔ Admin role → all ' . count($allPermissions) . ' permissions.');

        // ── Teacher ──
        $teacherRole = Role::where('name', 'teacher')->first();
        $teacherPerms = [
            // Students (view only)
            'students.view',
            // Attendance
            'attendance.view', 'attendance.create',
            // Diary
            'diary.view', 'diary.create', 'diary.edit',
            // Leave
            'leave.view', 'leave.create',
            // Exams
            'exams.view',
            'exam_results.view', 'exam_results.create', 'exam_results.edit',
            'exam_schedules.view',
            // Timetable
            'timetable.view',
            // Behavior
            'behavior.view', 'behavior.create', 'behavior.edit',
            // Notices
            'notices.view',
            // Reports (limited)
            'reports.view', 'reports.attendance', 'reports.exams',
            // Grading & Report Cards
            'grading.view',
            'report_cards.view',
            // Notifications
            'notifications.view', 'notifications.create',
        ];
        $teacherRole->givePermission($teacherPerms);
        $this->command->info('✔ Teacher role → ' . count($teacherPerms) . ' permissions.');

        // ── Accountant ──
        $accountantRole = Role::where('name', 'accountant')->first();
        $accountantPerms = [
            // Students (view for fee context)
            'students.view',
            // Finance
            'finance.view', 'finance.create',
            // Fees
            'fees.view', 'fees.create', 'fees.edit',
            'fee_structure.view', 'fee_structure.create', 'fee_structure.edit',
            'fee_discounts.view', 'fee_discounts.create', 'fee_discounts.edit',
            'fee_installments.view', 'fee_installments.create', 'fee_installments.edit',
            'late_fee.view', 'late_fee.create', 'late_fee.edit',
            // Payroll
            'payroll.view', 'payroll.create', 'payroll.edit',
            // Reports
            'reports.view', 'reports.finance', 'reports.fees',
            // Notices (view)
            'notices.view',
            // Classes (view for fee mapping)
            'classes.view',
        ];
        $accountantRole->givePermission($accountantPerms);
        $this->command->info('✔ Accountant role → ' . count($accountantPerms) . ' permissions.');

        // ── Student ──
        $studentRole = Role::where('name', 'student')->first();
        $studentPerms = [
            'students.view',          // own profile
            'attendance.view',        // own attendance
            'diary.view',             // class diary
            'exams.view',             // exam schedule
            'exam_results.view',      // own results
            'exam_schedules.view',    // date-sheet
            'timetable.view',         // class timetable
            'notices.view',           // school notices
            'report_cards.view',      // own report card
            'fees.view',              // own fee status
            'leave.view', 'leave.create', // apply for leave
        ];
        $studentRole->givePermission($studentPerms);
        $this->command->info('✔ Student role → ' . count($studentPerms) . ' permissions.');

        // ── Receptionist ──
        $receptionistRole = Role::where('name', 'receptionist')->first();
        $receptionistPerms = [
            'students.view', 'students.create',
            'admission.view', 'admission.create', 'admission.edit',
            'notices.view', 'notices.create',
            'attendance.view',
            'classes.view',
            'fees.view',
            'whatsapp.view',
        ];
        $receptionistRole->givePermission($receptionistPerms);
        $this->command->info('✔ Receptionist role → ' . count($receptionistPerms) . ' permissions.');

        // ════════════════════════════════════════════
        //  4. Assign roles to existing users based on their `role` column
        // ════════════════════════════════════════════
        if (Schema::hasColumn('users', 'role')) {
            $users = User::all();
            $migrated = 0;
            foreach ($users as $user) {
                $roleName = $user->role ?? 'admin';
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $user->roles()->syncWithoutDetaching([$role->id]);
                    $migrated++;
                }
            }
            $this->command->info("✔ Migrated {$migrated} existing users to role-based system.");
        }
    }

    /**
     * Complete permissions list grouped by module.
     */
    private function getPermissions(): array
    {
        return [
            // ── Students ──
            ['name' => 'students.view',     'display_name' => 'View Students',      'module' => 'students',     'group' => 'Academic'],
            ['name' => 'students.create',   'display_name' => 'Create Students',    'module' => 'students',     'group' => 'Academic'],
            ['name' => 'students.edit',     'display_name' => 'Edit Students',      'module' => 'students',     'group' => 'Academic'],
            ['name' => 'students.delete',   'display_name' => 'Delete Students',    'module' => 'students',     'group' => 'Academic'],

            // ── Admission ──
            ['name' => 'admission.view',    'display_name' => 'View Admissions',    'module' => 'admission',    'group' => 'Academic'],
            ['name' => 'admission.create',  'display_name' => 'Create Admissions',  'module' => 'admission',    'group' => 'Academic'],
            ['name' => 'admission.edit',    'display_name' => 'Edit Admissions',    'module' => 'admission',    'group' => 'Academic'],
            ['name' => 'admission.delete',  'display_name' => 'Delete Admissions',  'module' => 'admission',    'group' => 'Academic'],
            ['name' => 'admission.approve', 'display_name' => 'Approve Admissions', 'module' => 'admission',    'group' => 'Academic'],

            // ── Behavior ──
            ['name' => 'behavior.view',     'display_name' => 'View Behavior',      'module' => 'behavior',     'group' => 'Academic'],
            ['name' => 'behavior.create',   'display_name' => 'Create Behavior',    'module' => 'behavior',     'group' => 'Academic'],
            ['name' => 'behavior.edit',     'display_name' => 'Edit Behavior',      'module' => 'behavior',     'group' => 'Academic'],
            ['name' => 'behavior.delete',   'display_name' => 'Delete Behavior',    'module' => 'behavior',     'group' => 'Academic'],

            // ── Transfer Certificates ──
            ['name' => 'transfer_certificates.view',   'display_name' => 'View Transfer Certificates',   'module' => 'transfer_certificates', 'group' => 'Academic'],
            ['name' => 'transfer_certificates.create', 'display_name' => 'Create Transfer Certificates', 'module' => 'transfer_certificates', 'group' => 'Academic'],
            ['name' => 'transfer_certificates.issue',  'display_name' => 'Issue Transfer Certificates',  'module' => 'transfer_certificates', 'group' => 'Academic'],
            ['name' => 'transfer_certificates.delete', 'display_name' => 'Delete Transfer Certificates', 'module' => 'transfer_certificates', 'group' => 'Academic'],

            // ── Alumni ──
            ['name' => 'alumni.view',       'display_name' => 'View Alumni',        'module' => 'alumni',       'group' => 'Academic'],
            ['name' => 'alumni.create',     'display_name' => 'Create Alumni',      'module' => 'alumni',       'group' => 'Academic'],
            ['name' => 'alumni.edit',       'display_name' => 'Edit Alumni',        'module' => 'alumni',       'group' => 'Academic'],
            ['name' => 'alumni.delete',     'display_name' => 'Delete Alumni',      'module' => 'alumni',       'group' => 'Academic'],

            // ── Teachers ──
            ['name' => 'teachers.view',     'display_name' => 'View Teachers',      'module' => 'teachers',     'group' => 'Academic'],
            ['name' => 'teachers.create',   'display_name' => 'Create Teachers',    'module' => 'teachers',     'group' => 'Academic'],
            ['name' => 'teachers.edit',     'display_name' => 'Edit Teachers',      'module' => 'teachers',     'group' => 'Academic'],
            ['name' => 'teachers.delete',   'display_name' => 'Delete Teachers',    'module' => 'teachers',     'group' => 'Academic'],

            // ── Classes ──
            ['name' => 'classes.view',      'display_name' => 'View Classes',       'module' => 'classes',      'group' => 'Academic'],
            ['name' => 'classes.create',    'display_name' => 'Create Classes',     'module' => 'classes',      'group' => 'Academic'],
            ['name' => 'classes.edit',      'display_name' => 'Edit Classes',       'module' => 'classes',      'group' => 'Academic'],
            ['name' => 'classes.delete',    'display_name' => 'Delete Classes',     'module' => 'classes',      'group' => 'Academic'],

            // ── Subjects ──
            ['name' => 'subjects.view',     'display_name' => 'View Subjects',      'module' => 'subjects',     'group' => 'Academic'],
            ['name' => 'subjects.create',   'display_name' => 'Create Subjects',    'module' => 'subjects',     'group' => 'Academic'],
            ['name' => 'subjects.edit',     'display_name' => 'Edit Subjects',      'module' => 'subjects',     'group' => 'Academic'],
            ['name' => 'subjects.delete',   'display_name' => 'Delete Subjects',    'module' => 'subjects',     'group' => 'Academic'],

            // ── Sessions ──
            ['name' => 'sessions.view',     'display_name' => 'View Sessions',      'module' => 'sessions',     'group' => 'Academic'],
            ['name' => 'sessions.create',   'display_name' => 'Create Sessions',    'module' => 'sessions',     'group' => 'Academic'],
            ['name' => 'sessions.edit',     'display_name' => 'Edit Sessions',      'module' => 'sessions',     'group' => 'Academic'],
            ['name' => 'sessions.delete',   'display_name' => 'Delete Sessions',    'module' => 'sessions',     'group' => 'Academic'],

            // ── Timetable ──
            ['name' => 'timetable.view',    'display_name' => 'View Timetable',     'module' => 'timetable',    'group' => 'Academic'],
            ['name' => 'timetable.create',  'display_name' => 'Create Timetable',   'module' => 'timetable',    'group' => 'Academic'],
            ['name' => 'timetable.delete',  'display_name' => 'Delete Timetable',   'module' => 'timetable',    'group' => 'Academic'],

            // ── Attendance ──
            ['name' => 'attendance.view',   'display_name' => 'View Attendance',    'module' => 'attendance',   'group' => 'Academic'],
            ['name' => 'attendance.create', 'display_name' => 'Mark Attendance',    'module' => 'attendance',   'group' => 'Academic'],

            // ── Leave ──
            ['name' => 'leave.view',        'display_name' => 'View Leaves',        'module' => 'leave',        'group' => 'Academic'],
            ['name' => 'leave.create',      'display_name' => 'Create Leaves',      'module' => 'leave',        'group' => 'Academic'],
            ['name' => 'leave.approve',     'display_name' => 'Approve/Reject Leaves', 'module' => 'leave',     'group' => 'Academic'],

            // ── Exams ──
            ['name' => 'exams.view',        'display_name' => 'View Exams',         'module' => 'exams',        'group' => 'Academic'],
            ['name' => 'exams.create',      'display_name' => 'Create Exams',       'module' => 'exams',        'group' => 'Academic'],
            ['name' => 'exams.edit',        'display_name' => 'Edit Exams',         'module' => 'exams',        'group' => 'Academic'],
            ['name' => 'exams.delete',      'display_name' => 'Delete Exams',       'module' => 'exams',        'group' => 'Academic'],

            // ── Exam Results ──
            ['name' => 'exam_results.view',   'display_name' => 'View Exam Results',   'module' => 'exam_results',   'group' => 'Academic'],
            ['name' => 'exam_results.create', 'display_name' => 'Create Exam Results', 'module' => 'exam_results',   'group' => 'Academic'],
            ['name' => 'exam_results.edit',   'display_name' => 'Edit Exam Results',   'module' => 'exam_results',   'group' => 'Academic'],
            ['name' => 'exam_results.delete', 'display_name' => 'Delete Exam Results', 'module' => 'exam_results',   'group' => 'Academic'],

            // ── Exam Schedules ──
            ['name' => 'exam_schedules.view',   'display_name' => 'View Exam Schedules',   'module' => 'exam_schedules', 'group' => 'Academic'],
            ['name' => 'exam_schedules.create', 'display_name' => 'Create Exam Schedules', 'module' => 'exam_schedules', 'group' => 'Academic'],
            ['name' => 'exam_schedules.delete', 'display_name' => 'Delete Exam Schedules', 'module' => 'exam_schedules', 'group' => 'Academic'],

            // ── Grading ──
            ['name' => 'grading.view',      'display_name' => 'View Grading Systems',   'module' => 'grading',   'group' => 'Academic'],
            ['name' => 'grading.create',    'display_name' => 'Create Grading Systems',  'module' => 'grading',   'group' => 'Academic'],
            ['name' => 'grading.edit',      'display_name' => 'Edit Grading Systems',    'module' => 'grading',   'group' => 'Academic'],
            ['name' => 'grading.delete',    'display_name' => 'Delete Grading Systems',  'module' => 'grading',   'group' => 'Academic'],

            // ── Report Cards ──
            ['name' => 'report_cards.view',   'display_name' => 'View Report Cards',    'module' => 'report_cards', 'group' => 'Academic'],
            ['name' => 'report_cards.manage', 'display_name' => 'Manage Report Cards',  'module' => 'report_cards', 'group' => 'Academic'],

            // ── Diary ──
            ['name' => 'diary.view',        'display_name' => 'View Diary',          'module' => 'diary',        'group' => 'Academic'],
            ['name' => 'diary.create',      'display_name' => 'Create Diary',        'module' => 'diary',        'group' => 'Academic'],
            ['name' => 'diary.edit',        'display_name' => 'Edit Diary',          'module' => 'diary',        'group' => 'Academic'],
            ['name' => 'diary.delete',      'display_name' => 'Delete Diary',        'module' => 'diary',        'group' => 'Academic'],

            // ── Finance ──
            ['name' => 'finance.view',      'display_name' => 'View Finance Hub',   'module' => 'finance',      'group' => 'Finance'],
            ['name' => 'finance.create',    'display_name' => 'Create Vouchers',     'module' => 'finance',      'group' => 'Finance'],

            // ── Fees ──
            ['name' => 'fees.view',         'display_name' => 'View Fees',           'module' => 'fees',         'group' => 'Finance'],
            ['name' => 'fees.create',       'display_name' => 'Create Fee Vouchers', 'module' => 'fees',         'group' => 'Finance'],
            ['name' => 'fees.edit',         'display_name' => 'Edit Fees',           'module' => 'fees',         'group' => 'Finance'],
            ['name' => 'fees.delete',       'display_name' => 'Delete Fees',         'module' => 'fees',         'group' => 'Finance'],

            // ── Fee Structure ──
            ['name' => 'fee_structure.view',   'display_name' => 'View Fee Structure',   'module' => 'fee_structure',   'group' => 'Finance'],
            ['name' => 'fee_structure.create', 'display_name' => 'Create Fee Structure', 'module' => 'fee_structure',   'group' => 'Finance'],
            ['name' => 'fee_structure.edit',   'display_name' => 'Edit Fee Structure',   'module' => 'fee_structure',   'group' => 'Finance'],
            ['name' => 'fee_structure.delete', 'display_name' => 'Delete Fee Structure', 'module' => 'fee_structure',   'group' => 'Finance'],

            // ── Fee Discounts ──
            ['name' => 'fee_discounts.view',   'display_name' => 'View Fee Discounts',   'module' => 'fee_discounts',   'group' => 'Finance'],
            ['name' => 'fee_discounts.create', 'display_name' => 'Create Fee Discounts', 'module' => 'fee_discounts',   'group' => 'Finance'],
            ['name' => 'fee_discounts.edit',   'display_name' => 'Edit Fee Discounts',   'module' => 'fee_discounts',   'group' => 'Finance'],
            ['name' => 'fee_discounts.delete', 'display_name' => 'Delete Fee Discounts', 'module' => 'fee_discounts',   'group' => 'Finance'],

            // ── Fee Installments ──
            ['name' => 'fee_installments.view',   'display_name' => 'View Installments',   'module' => 'fee_installments', 'group' => 'Finance'],
            ['name' => 'fee_installments.create', 'display_name' => 'Create Installments', 'module' => 'fee_installments', 'group' => 'Finance'],
            ['name' => 'fee_installments.edit',   'display_name' => 'Edit Installments',   'module' => 'fee_installments', 'group' => 'Finance'],

            // ── Late Fee ──
            ['name' => 'late_fee.view',     'display_name' => 'View Late Fee Rules', 'module' => 'late_fee',     'group' => 'Finance'],
            ['name' => 'late_fee.create',   'display_name' => 'Create Late Fee Rules','module' => 'late_fee',    'group' => 'Finance'],
            ['name' => 'late_fee.edit',     'display_name' => 'Edit Late Fee Rules', 'module' => 'late_fee',     'group' => 'Finance'],
            ['name' => 'late_fee.delete',   'display_name' => 'Delete Late Fee Rules','module' => 'late_fee',    'group' => 'Finance'],

            // ── Payroll ──
            ['name' => 'payroll.view',      'display_name' => 'View Payroll',        'module' => 'payroll',      'group' => 'Finance'],
            ['name' => 'payroll.create',    'display_name' => 'Create Payroll',      'module' => 'payroll',      'group' => 'Finance'],
            ['name' => 'payroll.edit',      'display_name' => 'Edit Payroll',        'module' => 'payroll',      'group' => 'Finance'],
            ['name' => 'payroll.approve',   'display_name' => 'Approve Payroll',     'module' => 'payroll',      'group' => 'Finance'],

            // ── WhatsApp ──
            ['name' => 'whatsapp.view',     'display_name' => 'View WhatsApp Hub',   'module' => 'whatsapp',     'group' => 'Communication'],
            ['name' => 'whatsapp.send',     'display_name' => 'Send WhatsApp',       'module' => 'whatsapp',     'group' => 'Communication'],

            // ── Notices ──
            ['name' => 'notices.view',      'display_name' => 'View Notices',        'module' => 'notices',      'group' => 'Communication'],
            ['name' => 'notices.create',    'display_name' => 'Create Notices',      'module' => 'notices',      'group' => 'Communication'],
            ['name' => 'notices.edit',      'display_name' => 'Edit Notices',        'module' => 'notices',      'group' => 'Communication'],
            ['name' => 'notices.delete',    'display_name' => 'Delete Notices',      'module' => 'notices',      'group' => 'Communication'],

            // ── Notifications ──
            ['name' => 'notifications.view',   'display_name' => 'View Notifications',   'module' => 'notifications', 'group' => 'Communication'],
            ['name' => 'notifications.create', 'display_name' => 'Send Notifications',   'module' => 'notifications', 'group' => 'Communication'],
            ['name' => 'notifications.delete', 'display_name' => 'Delete Notifications', 'module' => 'notifications', 'group' => 'Communication'],

            // ── Reports ──
            ['name' => 'reports.view',       'display_name' => 'View Reports Hub',    'module' => 'reports',      'group' => 'Reports'],
            ['name' => 'reports.finance',    'display_name' => 'Finance Reports',     'module' => 'reports',      'group' => 'Reports'],
            ['name' => 'reports.fees',       'display_name' => 'Fee Reports',         'module' => 'reports',      'group' => 'Reports'],
            ['name' => 'reports.attendance', 'display_name' => 'Attendance Reports',  'module' => 'reports',      'group' => 'Reports'],
            ['name' => 'reports.students',   'display_name' => 'Student Reports',     'module' => 'reports',      'group' => 'Reports'],
            ['name' => 'reports.exams',      'display_name' => 'Exam Reports',        'module' => 'reports',      'group' => 'Reports'],

            // ── User Management ──
            ['name' => 'users.view',        'display_name' => 'View Users',          'module' => 'users',        'group' => 'Administration'],
            ['name' => 'users.create',      'display_name' => 'Create Users',        'module' => 'users',        'group' => 'Administration'],
            ['name' => 'users.edit',        'display_name' => 'Edit Users',          'module' => 'users',        'group' => 'Administration'],
            ['name' => 'users.delete',      'display_name' => 'Delete Users',        'module' => 'users',        'group' => 'Administration'],

            // ── Roles & Permissions ──
            ['name' => 'roles.view',        'display_name' => 'View Roles',          'module' => 'roles',        'group' => 'Administration'],
            ['name' => 'roles.create',      'display_name' => 'Create Roles',        'module' => 'roles',        'group' => 'Administration'],
            ['name' => 'roles.edit',        'display_name' => 'Edit Roles',          'module' => 'roles',        'group' => 'Administration'],
            ['name' => 'roles.delete',      'display_name' => 'Delete Roles',        'module' => 'roles',        'group' => 'Administration'],

            // ── Settings ──
            ['name' => 'settings.view',     'display_name' => 'View Settings',       'module' => 'settings',     'group' => 'Administration'],
            ['name' => 'settings.edit',     'display_name' => 'Edit Settings',       'module' => 'settings',     'group' => 'Administration'],

            // ── Activity Logs ──
            ['name' => 'activity_logs.view',   'display_name' => 'View Activity Logs',   'module' => 'activity_logs', 'group' => 'Administration'],
            ['name' => 'activity_logs.delete', 'display_name' => 'Delete Activity Logs', 'module' => 'activity_logs', 'group' => 'Administration'],
        ];
    }
}
