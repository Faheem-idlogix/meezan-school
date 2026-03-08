# Meezan School Management System

A comprehensive Laravel-based school management system with multi-role access, fee management, exam results, attendance tracking, finance hub, report generation, WhatsApp integration, and more.

## Tech Stack

- **Backend:** Laravel 10.x, PHP 8.1+
- **Database:** MySQL (`meezan_school_db`)
- **Frontend:** NiceAdmin Bootstrap 5.3.8, Chart.js, Select2, TinyMCE
- **PDF:** Barryvdh/DomPDF (A4 landscape)
- **Auth:** Custom RBAC with `CheckPermission` middleware

## Quick Setup

```bash
composer install
cp .env.example .env    # configure DB credentials
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

Default super admin: see seeder for credentials.

---

## System Roles

| Role | Access |
|------|--------|
| **super_admin** | Full system + school/plan management |
| **admin** | Full access except super-admin panel |
| **teacher** | Students, attendance, diary, leave, exams, behavior, reports |
| **accountant** | Finance, fees, payroll, reports, voucher status |
| **student** | Own profile, attendance, diary, exams, report cards, fees |
| **receptionist** | Students, admission, notices, attendance, fees, WhatsApp |

---

## Module Overview

### Academic

| Module | Route | Controller | Description |
|--------|-------|------------|-------------|
| Students | `student.*` | StudentController | CRUD, class assignment, soft-delete/archive |
| Admission | `admission.*` | AdmissionController | Enquiry → test → approve → enroll pipeline |
| Teachers | `teacher.*` | TeacherController | CRUD, subject assignment |
| Classes | `class.*` | ClassRoomController | Class + section management |
| Subjects | `subject.*`, `class_subject.*` | SubjectController, ClassSubjectController | Subject CRUD, class-subject mapping |
| Sessions | `session.*` | SessionController | Academic session/year management |
| Timetable | `timetable.*` | TimetableController | Period-based schedule management |
| Attendance | `attendance`, `attendance_report` | AttendanceController | Mark + report by class/date |
| Leave | `leave.*` | LeaveController | Request, approve, reject workflow |
| Exams | `exam.*`, `exam_result.*` | ExamController, ExamResultController | Exam CRUD, bulk mark entry, AJAX class data |
| Exam Schedules | `exam-schedules.*` | ExamScheduleController | Date/time scheduling per exam |
| Grading | `grading-systems.*` | GradingSystemController | Grade rules (A/B/C/F thresholds) |
| Report Cards | `report-cards.*` | ReportCardController | Config + generate per student |
| Daily Diary | `diary.*` | DiaryController | Daily entries with WhatsApp broadcast |
| Student Behavior | `behavior.*` | StudentBehaviorController | Behavior tracking with categories |
| Student Documents | `student.documents` | StudentDocumentController | Upload, verify, manage docs |
| Transfer Certificates | `transfer-certificate.*` | TransferCertificateController | Issue + PDF generation |
| Alumni | `alumni.*` | AlumniController | Graduated student directory |

### Finance

| Module | Route | Controller | Description |
|--------|-------|------------|-------------|
| Finance Hub | `finance.*` | FinanceController | Income/expense dashboard, chart data |
| Monthly Invoices | `fee_voucher` | ClassFeeVoucherController | Class-wide fee voucher generation |
| Student Fees | `student_fee.*` | StudentFeeController | Individual fee CRUD, payment recording |
| Journal Vouchers | `voucher.*` | VoucherController | Income/expense voucher + PDF |
| Voucher Status | `voucher-status.*` | VoucherStatusController | **Paid/Unpaid/Pending tracking with filters** |
| Fee Structure | `fee-structures.*` | FeeStructureController | Template-based fee definitions |
| Discounts | `fee-discounts.*` | FeeDiscountController | Scholarship/discount assignment |
| Installments | `fee-installments.*` | FeeInstallmentController | Payment plan management |
| Late Fee Rules | `late-fee-rules.*` | LateFeeRuleController | Auto late-fee configuration |
| Payroll | `payroll.*` | PayrollController | Teacher salary, advances, payslips |

### Communication

| Module | Route | Controller | Description |
|--------|-------|------------|-------------|
| WhatsApp Hub | `whatsapp.*` | WhatsAppController | Sync, test, bulk send, broadcast |
| Notices | `notice.*` | NoticeController | School-wide announcements |
| Notifications | `notifications.*` | NotificationController | In-app + navbar notifications |

### Reports & Analytics

| Module | Route | Controller | Description |
|--------|-------|------------|-------------|
| Reports Hub | `reports.index` | ReportController | Central dashboard for all reports |
| Finance Report | `reports.finance` | ReportController | Income/expense/profit with period filters |
| Fee Collection | `reports.fees` | ReportController | Billed vs received, monthly breakdown |
| Attendance Report | `reports.attendance` | ReportController | Class-wise/student-wise rates |
| Student Report | `reports.students` | ReportController | Demographics, class distribution |
| Exam Report | `reports.exams` | ReportController | Score distribution, top performers |
| Archived Records | `reports.archived` | ReportController | Soft-deleted students summary |
| Voucher Status | `voucher-status.index` | VoucherStatusController | Full voucher tracking with export |

### Administration

| Module | Route | Controller | Description |
|--------|-------|------------|-------------|
| User Management | `users.*` | UserManagementController | CRUD with role assignment |
| Roles & Permissions | `roles.*` | RoleController | RBAC management |
| Settings | `settings.*` | SettingController | School info, report mode, invoice layout |
| Error Logs | `error-logs.*` | SystemErrorLogController | Auto-captured PHP errors with filters |
| Activity Logs | `activity_logs.*` | ActivityLogController | User action audit trail |
| Global Search | `global.search` | GlobalSearchController | Cross-module search + AJAX suggest |

### Super Admin

| Module | Route | Description |
|--------|-------|-------------|
| Dashboard | `super_admin.dashboard` | Multi-school overview |
| Schools | `super_admin.schools.*` | School CRUD |
| Plans | `super_admin.plans.*` | Subscription plan management |

---

## Voucher Status Report — Detailed Guide

**URL:** `/voucher-status`  
**Sidebar:** Reports → Voucher Status  **OR**  Fee Management → Voucher Status

This is the primary page for tracking which vouchers are **Paid**, **Unpaid**, or **Pending**.

### What You See

1. **Summary Cards** — Total / Paid / Unpaid / Pending counts (overall)
2. **Financial Summary** — Filtered totals: Billed, Received, Outstanding Balance
3. **Collection Rate Bar** — Visual paid vs unpaid progress bar
4. **Filter Panel** — 6 filters:
   - **Status** — Paid / Unpaid / Pending
   - **Class** — Filter by specific classroom
   - **Fee Month** — Filter by month (e.g., January 2026)
   - **Date Range** — From date / To date on issue date
   - **Search** — By voucher number or student name
5. **Voucher Table** — Columns: #, Voucher No, Student, Father Name, Class, Fee Month, Issue Date, Due Date, Total Fee, Received, Balance, Status, Action
6. **Export CSV** — Downloads filtered data as CSV file
7. **Print** — Browser print with clean layout

### How to Use

1. Go to **Reports → Voucher Status** in the sidebar
2. Use filters to narrow down (e.g., select "Unpaid" + a specific class)
3. Click **Apply Filters** — the cards, bar, and table all update
4. Click **Export CSV** to download the filtered result
5. Click the **eye icon** on any row to edit that student's fee record

---

## Report View Modes

The system supports **two view modes** controlled via Settings:

### Settings → Report & Invoice View

| Setting | Values | Effect |
|---------|--------|--------|
| **Report View Mode** | `basic` / `advanced` | Switches report blade files (basic vs advanced with charts/analytics) |
| **Invoice Layout** | `compact` / `detailed` | Switches fee challan PDF template |
| **Show Fee Breakdown** | On/Off | Show component-wise breakdown on invoices |
| **Show Payment History** | On/Off | Show payment history on student invoices |

### Advanced Reports Include

When `report_view_mode = advanced`:
- **Reports Hub** — All reports + voucher/invoice links + academic report cards in card grid
- **Finance** — 6 stat cards, monthly trend chart + line overlay, category pie, voucher detail table
- **Fees** — Collection rate bar, billed vs received chart, status pie, class-wise breakdown
- **Attendance** — Gauge bar, distribution chart, class-wise progress, student-wise badges
- **Students** — Gender pie, class distribution bar chart, full student table
- **Exams** — Score distribution pie, exam summary with progress bars, top 10 ranked

### Advanced Fee Challan PDF

When `invoice_layout = detailed`, the generated PDF challan includes:
- Professional school header with logo and contact info
- Color-coded status badges (Paid/Unpaid/Pending)
- Itemized fee breakdown (only non-zero charges shown)
- Payment summary with balance calculation
- School Copy + Student Copy side-by-side

---

## Key Technical Details

### Settings Helper
```php
setting('key', 'default')  // cached 5 minutes
```

### Permission Middleware
```php
Route::middleware('permission:fees.view')->group(...)
// super_admin role bypasses all permission checks
```

### PDF Generation
```php
PDF::loadView('admin.report.student_fee', ['data' => $data])
   ->setPaper('a4', 'landscape')->stream('voucher.pdf');
```

### Models Count: 46 | Controllers: 47 | Blade Directories: 39+

---

## File Structure

```
app/
├── Http/Controllers/     # 47 controllers
├── Models/               # 46 Eloquent models
├── Services/             # MenuService (sidebar), others
├── Helpers/helpers.php   # setting(), school_logo(), etc.
├── Traits/               # Shared model traits
├── Mail/                 # DatabaseBackupMail
├── Policies/             # Authorization policies
└── Providers/            # Service providers

resources/views/admin/
├── layout/master.blade.php          # Main layout with sidebar
├── pages/                           # 39+ module view directories
│   ├── reports/                     # Basic report views
│   │   └── advanced/                # Advanced report views (6 files)
│   ├── voucher_status/index.blade.php  # Voucher tracking page
│   ├── invoice/                     # Fee voucher CRUD
│   ├── voucher/                     # Journal voucher views
│   ├── settings/index.blade.php     # All settings inc. report mode
│   └── ...
└── report/                          # PDF templates
    ├── student_fee.blade.php        # Basic fee challan
    ├── student_fee_advanced.blade.php # Detailed fee challan
    └── result_card.blade.php        # Result card PDF
```
