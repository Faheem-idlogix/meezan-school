<?php

namespace App\Services;

use App\Models\User;

class MenuService
{
    /**
     * Build the full sidebar menu structure.
     * Each item knows which permission(s) it requires.
     * The renderer will skip items the user can't access.
     */
    public static function getSidebar(): array
    {
        return [
            // ══════════ SECTION: Academic ══════════
            ['type' => 'heading', 'label' => 'Academic'],

            [
                'label'  => 'Dashboard',
                'icon'   => 'bi bi-grid-1x2-fill',
                'route'  => 'home',
                'permission' => null,  // everyone sees dashboard
            ],

            [
                'label'  => 'Students',
                'icon'   => 'bi bi-people-fill',
                'id'     => 'nav-students',
                'permission' => ['students.view', 'students.create'],
                'children' => [
                    ['label' => 'All Students',  'route' => 'student.index',  'permission' => 'students.view'],
                    ['label' => 'Add Student',   'route' => 'student.create', 'permission' => 'students.create'],
                ],
            ],

            [
                'label'  => 'Admission',
                'icon'   => 'bi bi-person-plus-fill',
                'id'     => 'nav-admission',
                'permission' => ['admission.view', 'admission.create'],
                'children' => [
                    ['label' => 'Enquiries',   'route' => 'admission.index',  'permission' => 'admission.view'],
                    ['label' => 'New Enquiry',  'route' => 'admission.create', 'permission' => 'admission.create'],
                ],
            ],

            [
                'label'  => 'Student Lifecycle',
                'icon'   => 'bi bi-person-badge-fill',
                'id'     => 'nav-student-life',
                'permission' => ['behavior.view', 'transfer_certificates.view', 'alumni.view'],
                'children' => [
                    ['label' => 'Behavior Records',       'route' => 'behavior.index',              'permission' => 'behavior.view'],
                    ['label' => 'Transfer Certificates',  'route' => 'transfer-certificate.index',  'permission' => 'transfer_certificates.view'],
                    ['label' => 'Alumni Directory',       'route' => 'alumni.index',                'permission' => 'alumni.view'],
                ],
            ],

            [
                'label'  => 'Teachers',
                'icon'   => 'bi bi-person-workspace',
                'id'     => 'nav-teachers',
                'permission' => ['teachers.view', 'teachers.create'],
                'children' => [
                    ['label' => 'All Teachers', 'route' => 'teacher.index',  'permission' => 'teachers.view'],
                    ['label' => 'Add Teacher',  'route' => 'teacher.create', 'permission' => 'teachers.create'],
                ],
            ],

            [
                'label'  => 'Classes & Subjects',
                'icon'   => 'bi bi-journal-text',
                'id'     => 'nav-classes',
                'permission' => ['classes.view', 'subjects.view'],
                'children' => [
                    ['label' => 'All Classes',     'route' => 'class.index',          'permission' => 'classes.view'],
                    ['label' => 'Add Class',       'route' => 'class.create',         'permission' => 'classes.create'],
                    ['label' => 'Subjects',        'route' => 'subject.index',        'permission' => 'subjects.view'],
                    ['label' => 'Class Subjects',  'route' => 'class_subject.index',  'permission' => 'subjects.view'],
                    ['label' => 'Sessions',        'route' => 'session.index',        'permission' => 'sessions.view'],
                ],
            ],

            [
                'label'  => 'Timetable',
                'icon'   => 'bi bi-calendar3',
                'id'     => 'nav-timetable',
                'permission' => ['timetable.view', 'timetable.create'],
                'children' => [
                    ['label' => 'View Timetable', 'route' => 'timetable.index',  'permission' => 'timetable.view'],
                    ['label' => 'Add Period',     'route' => 'timetable.create', 'permission' => 'timetable.create'],
                ],
            ],

            [
                'label'  => 'Attendance',
                'icon'   => 'bi bi-calendar-check-fill',
                'id'     => 'nav-attendance',
                'permission' => ['attendance.view', 'attendance.create'],
                'children' => [
                    ['label' => 'Mark Attendance',    'route' => 'attendance',             'permission' => 'attendance.create'],
                    ['label' => 'Attendance Report',  'route' => 'get_attendance_report',  'permission' => 'attendance.view'],
                ],
            ],

            [
                'label'  => 'Leave Management',
                'icon'   => 'bi bi-calendar-x-fill',
                'id'     => 'nav-leave',
                'permission' => ['leave.view', 'leave.create'],
                'children' => [
                    ['label' => 'All Leaves',        'route' => 'leave.index',  'permission' => 'leave.view'],
                    ['label' => 'Add Leave Request', 'route' => 'leave.create', 'permission' => 'leave.create'],
                ],
            ],

            [
                'label'  => 'Exams & Results',
                'icon'   => 'bi bi-pencil-square',
                'id'     => 'nav-exams',
                'permission' => ['exams.view', 'exam_results.view', 'exam_schedules.view', 'grading.view', 'report_cards.view'],
                'children' => [
                    ['label' => 'Exam List',          'route' => 'exam.index',              'permission' => 'exams.view'],
                    ['label' => 'Add Exam',           'route' => 'exam.create',             'permission' => 'exams.create'],
                    ['label' => 'Results',            'route' => 'exam_result.index',       'permission' => 'exam_results.view'],
                    ['label' => 'Result Card',        'route' => 'result_card',             'permission' => 'exam_results.view'],
                    ['label' => 'Exam Schedule',      'route' => 'exam-schedules.index',    'permission' => 'exam_schedules.view'],
                    ['label' => 'Grading Systems',    'route' => 'grading-systems.index',   'permission' => 'grading.view'],
                    ['label' => 'Report Cards',       'route' => 'report-cards.generate',   'permission' => 'report_cards.view'],
                    ['label' => 'Report Card Config', 'route' => 'report-cards.config',     'permission' => 'report_cards.manage'],
                ],
            ],

            [
                'label'  => 'Daily Diary',
                'icon'   => 'bi bi-journal-bookmark-fill',
                'id'     => 'nav-diary',
                'permission' => ['diary.view', 'diary.create'],
                'children' => [
                    ['label' => 'View Diary', 'route' => 'diary.index',  'permission' => 'diary.view'],
                    ['label' => 'New Entry',  'route' => 'diary.create', 'permission' => 'diary.create'],
                ],
            ],

            // ══════════ SECTION: Finance ══════════
            ['type' => 'heading', 'label' => 'Finance', 'permission' => ['finance.view', 'fees.view', 'payroll.view']],

            [
                'label'  => 'Finance Hub',
                'icon'   => 'bi bi-graph-up-arrow',
                'route'  => 'finance.index',
                'permission' => 'finance.view',
            ],

            [
                'label'  => 'Fee Management',
                'icon'   => 'bi bi-cash-coin',
                'id'     => 'nav-finance',
                'permission' => ['fees.view', 'fees.create', 'fee_structure.view', 'fee_discounts.view', 'fee_installments.view', 'late_fee.view'],
                'children' => [
                    ['label' => 'Create Monthly Invoice', 'route' => 'fee_voucher_create',       'permission' => 'fees.create'],
                    ['label' => 'Monthly Invoices',       'route' => 'fee_voucher',              'permission' => 'fees.view'],
                    ['label' => 'Student Voucher',        'route' => 'create_student_fee',       'permission' => 'fees.create'],
                    ['label' => 'Journal Vouchers',       'route' => 'voucher.index',            'permission' => 'finance.view'],
                    ['label' => 'Fee Structure',          'route' => 'fee-structures.index',     'permission' => 'fee_structure.view'],
                    ['label' => 'Discounts & Scholarships','route' => 'fee-discounts.index',     'permission' => 'fee_discounts.view'],
                    ['label' => 'Installment Plans',      'route' => 'fee-installments.index',   'permission' => 'fee_installments.view'],
                    ['label' => 'Late Fee Rules',         'route' => 'late-fee-rules.index',     'permission' => 'late_fee.view'],
                ],
            ],

            [
                'label'  => 'Payroll',
                'icon'   => 'bi bi-cash-stack',
                'id'     => 'nav-payroll',
                'permission' => ['payroll.view', 'payroll.create'],
                'children' => [
                    ['label' => 'Monthly Payroll',   'route' => 'payroll.index',     'permission' => 'payroll.view'],
                    ['label' => 'Generate Payroll',  'route' => 'payroll.create',    'permission' => 'payroll.create'],
                    ['label' => 'Salary Advances',   'route' => 'payroll.advances',  'permission' => 'payroll.view'],
                ],
            ],

            // ══════════ SECTION: Communication ══════════
            ['type' => 'heading', 'label' => 'Communication', 'permission' => ['whatsapp.view', 'notices.view', 'notifications.view']],

            [
                'label'  => 'WhatsApp Hub',
                'icon'   => 'bi bi-whatsapp',
                'route'  => 'whatsapp.index',
                'permission' => 'whatsapp.view',
            ],

            [
                'label'  => 'Notices',
                'icon'   => 'bi bi-megaphone-fill',
                'id'     => 'nav-notices',
                'permission' => ['notices.view', 'notices.create'],
                'children' => [
                    ['label' => 'All Notices', 'route' => 'notice.index',  'permission' => 'notices.view'],
                    ['label' => 'New Notice',  'route' => 'notice.create', 'permission' => 'notices.create'],
                ],
            ],

            [
                'label'  => 'Notifications',
                'icon'   => 'bi bi-bell-fill',
                'id'     => 'nav-notifications',
                'permission' => ['notifications.view', 'notifications.create'],
                'children' => [
                    ['label' => 'All Notifications',  'route' => 'notifications.index',  'permission' => 'notifications.view'],
                    ['label' => 'Send Notification',  'route' => 'notifications.create', 'permission' => 'notifications.create'],
                    ['label' => 'My Notifications',   'route' => 'notifications.my'],
                ],
            ],

            // ══════════ SECTION: Reports & Docs ══════════
            ['type' => 'heading', 'label' => 'Reports & Docs', 'permission' => ['reports.view']],

            [
                'label'  => 'Reports',
                'icon'   => 'bi bi-bar-chart-line-fill',
                'id'     => 'nav-reports',
                'permission' => ['reports.view'],
                'children' => [
                    ['label' => 'Reports Hub',      'route' => 'reports.index',      'permission' => 'reports.view'],
                    ['label' => 'Finance Report',   'route' => 'reports.finance',    'permission' => 'reports.finance'],
                    ['label' => 'Fee Collection',   'route' => 'reports.fees',       'permission' => 'reports.fees'],
                    ['label' => 'Attendance Report', 'route' => 'reports.attendance', 'permission' => 'reports.attendance'],
                    ['label' => 'Student Report',   'route' => 'reports.students',   'permission' => 'reports.students'],
                    ['label' => 'Exam Report',      'route' => 'reports.exams',      'permission' => 'reports.exams'],
                    ['label' => 'Archived Records', 'route' => 'reports.archived',   'permission' => 'reports.view'],
                ],
            ],

            [
                'label'  => 'Documentation',
                'icon'   => 'bi bi-book-fill',
                'route'  => 'documentation',
                'permission' => null, // everyone
            ],

            // ══════════ SECTION: Administration ══════════
            ['type' => 'heading', 'label' => 'Administration', 'permission' => ['users.view', 'roles.view', 'settings.view', 'activity_logs.view']],

            [
                'label'  => 'User Management',
                'icon'   => 'bi bi-shield-lock-fill',
                'id'     => 'nav-users',
                'permission' => ['users.view', 'users.create'],
                'children' => [
                    ['label' => 'All Users', 'route' => 'users.index',  'permission' => 'users.view'],
                    ['label' => 'Add User',  'route' => 'users.create', 'permission' => 'users.create'],
                ],
            ],

            [
                'label'  => 'Roles & Permissions',
                'icon'   => 'bi bi-key-fill',
                'id'     => 'nav-roles',
                'permission' => ['roles.view', 'roles.create'],
                'children' => [
                    ['label' => 'All Roles',  'route' => 'roles.index',  'permission' => 'roles.view'],
                    ['label' => 'Add Role',   'route' => 'roles.create', 'permission' => 'roles.create'],
                ],
            ],

            [
                'label'  => 'Settings & WhatsApp',
                'icon'   => 'bi bi-gear-fill',
                'route'  => 'settings.index',
                'permission' => 'settings.view',
            ],

            [
                'label'  => 'Activity Logs',
                'icon'   => 'bi bi-clock-history',
                'route'  => 'activity_logs.index',
                'permission' => 'activity_logs.view',
            ],

            // ══════════ SECTION: Super Admin ══════════
            ['type' => 'heading', 'label' => 'Super Admin', 'role' => 'super_admin'],

            [
                'label'  => 'Schools',
                'icon'   => 'bi bi-buildings',
                'id'     => 'nav-superadmin',
                'role'   => 'super_admin',
                'children' => [
                    ['label' => 'Super Dashboard', 'route' => 'super_admin.dashboard',      'role' => 'super_admin'],
                    ['label' => 'Manage Schools',  'route' => 'super_admin.schools',         'role' => 'super_admin'],
                    ['label' => 'Add School',      'route' => 'super_admin.schools.create',  'role' => 'super_admin'],
                    ['label' => 'Plans',           'route' => 'super_admin.plans',           'role' => 'super_admin'],
                ],
            ],
        ];
    }

    /**
     * Filter menu items based on user permissions.
     */
    public static function getFilteredSidebar(?User $user = null): array
    {
        $user = $user ?? auth()->user();
        if (!$user) return [];

        $menu = self::getSidebar();
        $filtered = [];

        foreach ($menu as $item) {
            $filteredItem = self::filterItem($item, $user);
            if ($filteredItem !== null) {
                $filtered[] = $filteredItem;
            }
        }

        // Remove headings that have no visible items after them
        return self::removeOrphanedHeadings($filtered);
    }

    /**
     * Filter a single menu item.
     */
    private static function filterItem(array $item, User $user): ?array
    {
        // Check role-based items (super_admin section)
        if (isset($item['role'])) {
            if (!$user->hasRole($item['role'])) {
                return null;
            }
            // If it has children, filter them too
            if (isset($item['children'])) {
                $item['children'] = self::filterChildren($item['children'], $user);
                if (empty($item['children'])) return null;
            }
            return $item;
        }

        // Headings with permissions
        if (($item['type'] ?? null) === 'heading') {
            if (isset($item['permission'])) {
                $perms = is_array($item['permission']) ? $item['permission'] : [$item['permission']];
                if (!$user->hasAnyPermission($perms)) {
                    return null;
                }
            }
            return $item;
        }

        // Null permission = visible to everyone
        if (!isset($item['permission']) || $item['permission'] === null) {
            if (isset($item['children'])) {
                $item['children'] = self::filterChildren($item['children'], $user);
                if (empty($item['children'])) return null;
            }
            return $item;
        }

        // Check permissions
        $perms = is_array($item['permission']) ? $item['permission'] : [$item['permission']];
        if (!$user->hasAnyPermission($perms)) {
            return null;
        }

        // Filter children
        if (isset($item['children'])) {
            $item['children'] = self::filterChildren($item['children'], $user);
            if (empty($item['children'])) return null;
        }

        return $item;
    }

    /**
     * Filter children array.
     */
    private static function filterChildren(array $children, User $user): array
    {
        $filtered = [];
        foreach ($children as $child) {
            // Role check
            if (isset($child['role'])) {
                if ($user->hasRole($child['role'])) {
                    $filtered[] = $child;
                }
                continue;
            }

            // Null permission or user has it
            if (!isset($child['permission']) || $child['permission'] === null) {
                $filtered[] = $child;
                continue;
            }

            if ($user->hasPermission($child['permission'])) {
                $filtered[] = $child;
            }
        }
        return $filtered;
    }

    /**
     * Remove section headings that have no menu items following them.
     */
    private static function removeOrphanedHeadings(array $items): array
    {
        $result = [];
        $count = count($items);

        for ($i = 0; $i < $count; $i++) {
            if (($items[$i]['type'] ?? null) === 'heading') {
                // Check if there's at least one non-heading item after this
                $hasContent = false;
                for ($j = $i + 1; $j < $count; $j++) {
                    if (($items[$j]['type'] ?? null) === 'heading') break;
                    $hasContent = true;
                    break;
                }
                if ($hasContent) {
                    $result[] = $items[$i];
                }
            } else {
                $result[] = $items[$i];
            }
        }

        return $result;
    }
}
