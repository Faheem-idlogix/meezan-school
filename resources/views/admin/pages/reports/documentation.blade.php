@extends("admin.layout.master")
@section("content")
<main id="main" class="main">
  <div class="pagetitle">
    <h1><i class="bi bi-book-fill text-primary"></i> Software Documentation</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li><li class="breadcrumb-item active">Documentation</li></ol></nav>
  </div>

  <style>
    .doc-hero{background:linear-gradient(135deg,#4154f1,#2c3e8c);color:#fff;border-radius:16px;padding:36px 32px;margin-bottom:28px}
    .doc-hero h2{font-weight:800;margin-bottom:8px}
    .doc-hero p{opacity:.85;font-size:15px;margin:0}
    .doc-card{border:none;border-radius:14px;transition:transform .2s,box-shadow .2s;overflow:hidden;margin-bottom:24px}
    .doc-card:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.1)}
    .doc-card .card-header{padding:16px 24px;font-weight:700;font-size:16px;border-bottom:2px solid rgba(0,0,0,.05);display:flex;align-items:center;gap:10px}
    .doc-card .card-body{padding:20px 24px}
    .doc-card .card-body p{font-size:14px;color:#555;line-height:1.7}
    .module-list{list-style:none;padding:0;margin:0}
    .module-list li{padding:10px 0;border-bottom:1px solid #f0f0f0;display:flex;align-items:flex-start;gap:10px}
    .module-list li:last-child{border:none}
    .module-list .mi{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
    .module-list strong{display:block;font-size:14px;margin-bottom:2px}
    .module-list small{color:#777;font-size:12.5px;line-height:1.5}
    .shortcut-table td,.shortcut-table th{font-size:13px;padding:8px 14px}
    .shortcut-table thead{background:#f8f9fa}
    .toc{background:#f8f9fa;border-radius:12px;padding:20px 24px;position:sticky;top:80px}
    .toc a{display:block;padding:6px 0;font-size:13px;color:#555;text-decoration:none;border-bottom:1px solid #eee}
    .toc a:hover{color:#4154f1}
    .toc a:last-child{border:none}
  </style>

  {{-- Hero --}}
  <div class="doc-hero">
    <h2>{{ setting('school_name', 'School') }} Management System</h2>
    <p>Complete documentation covering all modules, features, and workflows of the school management platform.</p>
  </div>

  <div class="row">
    {{-- Table of Contents --}}
    <div class="col-lg-3">
      <div class="toc">
        <h6 class="fw-bold mb-3"><i class="bi bi-list-nested"></i> Contents</h6>
        <a href="#overview">System Overview</a>
        <a href="#dashboard">Dashboard</a>
        <a href="#students">Student Management</a>
        <a href="#teachers">Teacher Management</a>
        <a href="#classes">Classes & Subjects</a>
        <a href="#fees">Fee Management</a>
        <a href="#finance">Finance Hub</a>
        <a href="#attendance">Attendance</a>
        <a href="#exams">Exams & Results</a>
        <a href="#payroll">Payroll</a>
        <a href="#diary">Daily Diary</a>
        <a href="#timetable">Timetable</a>
        <a href="#leaves">Leave Management</a>
        <a href="#whatsapp">WhatsApp Hub</a>
        <a href="#notices">Notices</a>
        <a href="#reports">Reports</a>
        <a href="#users">User Management</a>
        <a href="#settings">Settings</a>
        <a href="#softdelete">Soft Delete / Archiving</a>
      </div>
    </div>

    {{-- Content --}}
    <div class="col-lg-9">

      {{-- Overview --}}
      <div class="doc-card card" id="overview">
        <div class="card-header" style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);color:#1565c0">
          <i class="bi bi-info-circle-fill"></i> System Overview
        </div>
        <div class="card-body">
          <p>{{ setting('school_name', 'School') }} Management System is a comprehensive web-based solution built on <strong>Laravel 10</strong> with <strong>Bootstrap 5</strong>, <strong>Chart.js</strong>, and <strong>jQuery</strong>. It provides end-to-end management for schools including student enrollment, fee collection, attendance tracking, exam management, payroll, and more.</p>
          <div class="module-list">
            <li><div class="mi" style="background:#e8f5e9;color:#2e7d32"><i class="bi bi-check-lg"></i></div><div><strong>Role-Based Access</strong><small>Admin, Teacher, Accountant, and Super Admin roles with different permissions and dashboards.</small></div></li>
            <li><div class="mi" style="background:#e3f2fd;color:#1565c0"><i class="bi bi-shield-check"></i></div><div><strong>Soft Delete Support</strong><small>All critical records (students, teachers, vouchers, etc.) use soft deletes — nothing is permanently lost.</small></div></li>
            <li><div class="mi" style="background:#fff3e0;color:#e65100"><i class="bi bi-calendar-range"></i></div><div><strong>Period-Based Filtering</strong><small>Dashboard and reports support date range filtering (this month, 6 months, this year, custom dates).</small></div></li>
          </div>
        </div>
      </div>

      {{-- Dashboard --}}
      <div class="doc-card card" id="dashboard">
        <div class="card-header" style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);color:#2e7d32">
          <i class="bi bi-speedometer2"></i> Dashboard
        </div>
        <div class="card-body">
          <p>The main dashboard provides a bird's-eye view of the entire school. It adapts based on the logged-in user's role.</p>
          <div class="module-list">
            <li><div class="mi" style="background:#e3f2fd;color:#1565c0"><i class="bi bi-cash-stack"></i></div><div><strong>Financial Summary</strong><small>Shows income, expenses, profit/loss, outstanding fees, and voucher counts — all filtered by the selected period.</small></div></li>
            <li><div class="mi" style="background:#fff3e0;color:#e65100"><i class="bi bi-bar-chart"></i></div><div><strong>Revenue Chart</strong><small>Interactive Chart.js bar chart showing monthly income vs expense trends.</small></div></li>
            <li><div class="mi" style="background:#f3e5f5;color:#7b1fa2"><i class="bi bi-calendar-check"></i></div><div><strong>Attendance Widget</strong><small>Class-wise attendance with present/absent counts and percentage bars for the selected period.</small></div></li>
            <li><div class="mi" style="background:#e0f7fa;color:#00838f"><i class="bi bi-table"></i></div><div><strong>Fee Table</strong><small>Recent fee records with student names (clickable to profile), amounts, and payment status.</small></div></li>
            <li><div class="mi" style="background:#fce4ec;color:#c62828"><i class="bi bi-funnel"></i></div><div><strong>Date Filter Bar</strong><small>Period selector at the top: This Month, Last Month, 6 Months, This Year, Last Year, or Custom Date Range. All widgets update accordingly.</small></div></li>
          </div>
        </div>
      </div>

      {{-- Students --}}
      <div class="doc-card card" id="students">
        <div class="card-header" style="background:linear-gradient(135deg,#f3e5f5,#e1bee7);color:#7b1fa2">
          <i class="bi bi-people-fill"></i> Student Management
        </div>
        <div class="card-body">
          <p>Full CRUD for students with enrollment, profile management, and detailed student profiles.</p>
          <div class="module-list">
            <li><div class="mi" style="background:#e8f5e9;color:#2e7d32"><i class="bi bi-person-plus"></i></div><div><strong>Add / Edit Students</strong><small>Register students with name, class, gender, phone, address, guardian info, and optional photo.</small></div></li>
            <li><div class="mi" style="background:#e3f2fd;color:#1565c0"><i class="bi bi-person-badge"></i></div><div><strong>Student Profile</strong><small>Detailed profile page showing personal info, fee history, attendance history, exam results, and leave requests. Click any student name in the system to navigate here.</small></div></li>
            <li><div class="mi" style="background:#ffebee;color:#c62828"><i class="bi bi-trash3"></i></div><div><strong>Soft Delete & Restore</strong><small>Deleting a student archives them. They can be restored from Reports → Archived Records.</small></div></li>
            <li><div class="mi" style="background:#fff3e0;color:#e65100"><i class="bi bi-arrow-left-right"></i></div><div><strong>Class Transfer</strong><small>Students can be moved between classes. Their fee records follow them.</small></div></li>
          </div>
        </div>
      </div>

      {{-- Teachers --}}
      <div class="doc-card card" id="teachers">
        <div class="card-header" style="background:linear-gradient(135deg,#e0f7fa,#b2ebf2);color:#00838f">
          <i class="bi bi-person-workspace"></i> Teacher Management
        </div>
        <div class="card-body">
          <p>Manage teacher profiles with contact information, assigned classes, and subject assignments.</p>
          <div class="module-list">
            <li><div class="mi" style="background:#e8f5e9;color:#2e7d32"><i class="bi bi-person-plus"></i></div><div><strong>Add / Edit Teachers</strong><small>Register teachers with name, phone, email, CNIC, salary, and qualification details.</small></div></li>
            <li><div class="mi" style="background:#f3e5f5;color:#7b1fa2"><i class="bi bi-link-45deg"></i></div><div><strong>Class-Subject Assignment</strong><small>Assign teachers to specific classes and subjects through the Class Subjects module.</small></div></li>
          </div>
        </div>
      </div>

      {{-- Classes & Subjects --}}
      <div class="doc-card card" id="classes">
        <div class="card-header" style="background:linear-gradient(135deg,#fff3e0,#ffe0b2);color:#e65100">
          <i class="bi bi-building"></i> Classes & Subjects
        </div>
        <div class="card-body">
          <p>Create and manage classes (grades/sections) and subjects with assignment to classes.</p>
          <div class="module-list">
            <li><div class="mi" style="background:#e3f2fd;color:#1565c0"><i class="bi bi-grid"></i></div><div><strong>Class Management</strong><small>Create classes like "Class 1", "Class 2-A", etc. Each class has a name, optional section, and monthly fee amount.</small></div></li>
            <li><div class="mi" style="background:#e8f5e9;color:#2e7d32"><i class="bi bi-journal-text"></i></div><div><strong>Subject Management</strong><small>Create subjects (Urdu, English, Math, etc.) that can be assigned to classes for exam management.</small></div></li>
            <li><div class="mi" style="background:#fff3e0;color:#e65100"><i class="bi bi-link"></i></div><div><strong>Class-Subject Mapping</strong><small>Assign subjects to specific classes and optionally link a teacher to each class-subject pair.</small></div></li>
          </div>
        </div>
      </div>

      {{-- Fee Management --}}
      <div class="doc-card card" id="fees">
        <div class="card-header" style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);color:#2e7d32">
          <i class="bi bi-cash-coin"></i> Fee Management
        </div>
        <div class="card-body">
          <p>Comprehensive fee system with class-based invoicing, individual student vouchers, and payment tracking.</p>
          <div class="module-list">
            <li><div class="mi" style="background:#e3f2fd;color:#1565c0"><i class="bi bi-receipt"></i></div><div><strong>Monthly Invoice Generation</strong><small>Generate fee invoices for an entire class at once. Select class, month, and fee amount — system creates StudentFee records for all enrolled students.</small></div></li>
            <li><div class="mi" style="background:#e8f5e9;color:#2e7d32"><i class="bi bi-person-check"></i></div><div><strong>Individual Fee Entry</strong><small>Create fee records for individual students with custom amounts, discounts, and notes.</small></div></li>
            <li><div class="mi" style="background:#fff3e0;color:#e65100"><i class="bi bi-currency-exchange"></i></div><div><strong>Payment Recording</strong><small>Record full or partial payments against student fees. The system tracks total, received, and balance amounts.</small></div></li>
            <li><div class="mi" style="background:#f3e5f5;color:#7b1fa2"><i class="bi bi-file-earmark-pdf"></i></div><div><strong>Invoice PDF</strong><small>Generate printable PDF invoices for each class monthly fee voucher.</small></div></li>
          </div>
        </div>
      </div>

      {{-- Finance Hub --}}
      <div class="doc-card card" id="finance">
        <div class="card-header" style="background:linear-gradient(135deg,#e8eaf6,#c5cae9);color:#283593">
          <i class="bi bi-graph-up-arrow"></i> Finance Hub
        </div>
        <div class="card-body">
          <p>Unified financial management interface — a single place to view all income and expenses.</p>
          <div class="module-list">
            <li><div class="mi" style="background:#e8f5e9;color:#2e7d32"><i class="bi bi-table"></i></div><div><strong>Unified Voucher Table</strong><small>All financial transactions (income + expense) in one searchable, sortable table with date/type/category filters.</small></div></li>
            <li><div class="mi" style="background:#e3f2fd;color:#1565c0"><i class="bi bi-plus-circle"></i></div><div><strong>Quick Add Voucher</strong><small>Add income or expense vouchers directly from the Finance Hub with date, category, amount, payment mode, and description.</small></div></li>
            <li><div class="mi" style="background:#fff3e0;color:#e65100"><i class="bi bi-bar-chart-line"></i></div><div><strong>Financial Charts</strong><small>Interactive charts showing income vs expense trends over time.</small></div></li>
          </div>
        </div>
      </div>

      {{-- Attendance --}}
      <div class="doc-card card" id="attendance">
        <div class="card-header" style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);color:#1565c0">
          <i class="bi bi-calendar-check"></i> Attendance
        </div>
        <div class="card-body">
          <p>Daily attendance marking and historical reporting for all classes.</p>
          <div class="module-list">
            <li><div class="mi" style="background:#e8f5e9;color:#2e7d32"><i class="bi bi-check2-all"></i></div><div><strong>Mark Attendance</strong><small>Select a class and date, then mark each student as Present, Absent, Leave, or Late. Bulk marking supported.</small></div></li>
            <li><div class="mi" style="background:#fff3e0;color:#e65100"><i class="bi bi-graph-up"></i></div><div><strong>Attendance Report</strong><small>View historical attendance data by class and date range. Available in Reports → Attendance Report.</small></div></li>
          </div>
        </div>
      </div>

      {{-- Exams --}}
      <div class="doc-card card" id="exams">
        <div class="card-header" style="background:linear-gradient(135deg,#fce4ec,#f8bbd0);color:#ad1457">
          <i class="bi bi-journal-bookmark-fill"></i> Exams & Results
        </div>
        <div class="card-body">
          <p>Create exams, enter subject-wise results, and generate result cards.</p>
          <div class="module-list">
            <li><div class="mi" style="background:#e3f2fd;color:#1565c0"><i class="bi bi-journal-plus"></i></div><div><strong>Create Exams</strong><small>Define exams with name, date, and class assignment (e.g., "Mid-Term 2025", "Final Exam").</small></div></li>
            <li><div class="mi" style="background:#e8f5e9;color:#2e7d32"><i class="bi bi-pencil-square"></i></div><div><strong>Enter Results</strong><small>Enter obtained marks for each student-subject combination. System automatically calculates percentages and grades.</small></div></li>
            <li><div class="mi" style="background:#f3e5f5;color:#7b1fa2"><i class="bi bi-card-checklist"></i></div><div><strong>Result Cards</strong><small>Generate printable result cards for students showing all subjects, marks, and grades.</small></div></li>
          </div>
        </div>
      </div>

      {{-- Payroll --}}
      <div class="doc-card card" id="payroll">
        <div class="card-header" style="background:linear-gradient(135deg,#e0f2f1,#b2dfdb);color:#00695c">
          <i class="bi bi-cash-stack"></i> Payroll
        </div>
        <div class="card-body">
          <p>Monthly salary management for teachers and staff with advance tracking.</p>
          <div class="module-list">
            <li><div class="mi" style="background:#e8f5e9;color:#2e7d32"><i class="bi bi-calculator"></i></div><div><strong>Generate Monthly Payroll</strong><small>Select month and teachers — system calculates base salary, deductions, allowances, and net pay.</small></div></li>
            <li><div class="mi" style="background:#e3f2fd;color:#1565c0"><i class="bi bi-check-circle"></i></div><div><strong>Approve & Pay</strong><small>Approve payroll entries and mark them as paid. Generate PDF payslips.</small></div></li>
            <li><div class="mi" style="background:#fff3e0;color:#e65100"><i class="bi bi-wallet2"></i></div><div><strong>Salary Advances</strong><small>Record advance payments to staff which are deducted from future payroll.</small></div></li>
          </div>
        </div>
      </div>

      {{-- Diary --}}
      <div class="doc-card card" id="diary">
        <div class="card-header" style="background:linear-gradient(135deg,#fff8e1,#ffecb3);color:#f57f17">
          <i class="bi bi-journal-text"></i> Daily Diary
        </div>
        <div class="card-body">
          <p>Create daily homework and classwork entries that can be shared with parents via WhatsApp.</p>
          <div class="module-list">
            <li><div class="mi" style="background:#e3f2fd;color:#1565c0"><i class="bi bi-pencil"></i></div><div><strong>Create Diary Entry</strong><small>Select class, date, and add homework/classwork details per subject. Can include notes for parents.</small></div></li>
            <li><div class="mi" style="background:#e8f5e9;color:#2e7d32"><i class="bi bi-whatsapp"></i></div><div><strong>WhatsApp Share</strong><small>Send diary entries directly to parents' WhatsApp numbers with one click.</small></div></li>
          </div>
        </div>
      </div>

      {{-- Timetable --}}
      <div class="doc-card card" id="timetable">
        <div class="card-header" style="background:linear-gradient(135deg,#ede7f6,#d1c4e9);color:#4527a0">
          <i class="bi bi-clock-history"></i> Timetable
        </div>
        <div class="card-body">
          <p>Create and manage class timetables with day-wise period assignments.</p>
          <div class="module-list">
            <li><div class="mi" style="background:#e3f2fd;color:#1565c0"><i class="bi bi-grid-3x3"></i></div><div><strong>Class Timetable</strong><small>Define periods for each day of the week per class. Assign subjects and teachers to each slot.</small></div></li>
          </div>
        </div>
      </div>

      {{-- Leaves --}}
      <div class="doc-card card" id="leaves">
        <div class="card-header" style="background:linear-gradient(135deg,#efebe9,#d7ccc8);color:#4e342e">
          <i class="bi bi-calendar-x"></i> Leave Management
        </div>
        <div class="card-body">
          <p>Track and approve student and teacher leave requests.</p>
          <div class="module-list">
            <li><div class="mi" style="background:#fff3e0;color:#e65100"><i class="bi bi-calendar-minus"></i></div><div><strong>Leave Requests</strong><small>Submit leave applications with date range, reason, and type. Admin can approve or reject.</small></div></li>
          </div>
        </div>
      </div>

      {{-- WhatsApp Hub --}}
      <div class="doc-card card" id="whatsapp">
        <div class="card-header" style="background:linear-gradient(135deg,#e8f5e9,#a5d6a7);color:#1b5e20">
          <i class="bi bi-whatsapp"></i> WhatsApp Hub
        </div>
        <div class="card-body">
          <p>Centralized WhatsApp communication center for bulk messaging and notice broadcasting.</p>
          <div class="module-list">
            <li><div class="mi" style="background:#e3f2fd;color:#1565c0"><i class="bi bi-send"></i></div><div><strong>Send Test Message</strong><small>Test WhatsApp API connectivity by sending a message to any number.</small></div></li>
            <li><div class="mi" style="background:#e8f5e9;color:#2e7d32"><i class="bi bi-broadcast"></i></div><div><strong>Bulk Messaging</strong><small>Send messages to all parents of a class or the entire school at once.</small></div></li>
            <li><div class="mi" style="background:#fff3e0;color:#e65100"><i class="bi bi-megaphone"></i></div><div><strong>Notice Broadcast</strong><small>Select an existing notice and broadcast it via WhatsApp to all parents.</small></div></li>
          </div>
        </div>
      </div>

      {{-- Notices --}}
      <div class="doc-card card" id="notices">
        <div class="card-header" style="background:linear-gradient(135deg,#ffebee,#ffcdd2);color:#c62828">
          <i class="bi bi-megaphone-fill"></i> Notices
        </div>
        <div class="card-body">
          <p>Create and manage school notices and announcements with toggle active/inactive status.</p>
        </div>
      </div>

      {{-- Reports --}}
      <div class="doc-card card" id="reports">
        <div class="card-header" style="background:linear-gradient(135deg,#e3f2fd,#90caf9);color:#0d47a1">
          <i class="bi bi-bar-chart-line-fill"></i> Reports Module
        </div>
        <div class="card-body">
          <p>Comprehensive reporting system with multiple report types and period-based filtering.</p>
          <div class="module-list">
            <li><div class="mi" style="background:#e8f5e9;color:#2e7d32"><i class="bi bi-cash-coin"></i></div><div><strong>Finance Report</strong><small>Income vs expense analysis with monthly trends, category breakdown, and full voucher listing.</small></div></li>
            <li><div class="mi" style="background:#fff3e0;color:#e65100"><i class="bi bi-receipt"></i></div><div><strong>Fee Collection Report</strong><small>Monthly fee billing vs collection, paid/unpaid counts, and outstanding amounts.</small></div></li>
            <li><div class="mi" style="background:#e3f2fd;color:#1565c0"><i class="bi bi-calendar-check"></i></div><div><strong>Attendance Report</strong><small>Class-wise and student-wise attendance analysis with percentage rates.</small></div></li>
            <li><div class="mi" style="background:#f3e5f5;color:#7b1fa2"><i class="bi bi-people"></i></div><div><strong>Student Report</strong><small>Enrollment data, class distribution, gender breakdown, with option to include deleted records.</small></div></li>
            <li><div class="mi" style="background:#e0f7fa;color:#00838f"><i class="bi bi-journal-bookmark"></i></div><div><strong>Exam Report</strong><small>Exam-wise summary, subject analysis, and top 10 performer rankings.</small></div></li>
            <li><div class="mi" style="background:#ffebee;color:#c62828"><i class="bi bi-trash3"></i></div><div><strong>Archived Records</strong><small>View and restore soft-deleted students, teachers, vouchers, classes, subjects, and exam results.</small></div></li>
          </div>
        </div>
      </div>

      {{-- User Management --}}
      <div class="doc-card card" id="users">
        <div class="card-header" style="background:linear-gradient(135deg,#fce4ec,#f8bbd0);color:#880e4f">
          <i class="bi bi-shield-lock-fill"></i> User Management
        </div>
        <div class="card-body">
          <p>Manage system users with role-based access control.</p>
          <div class="module-list">
            <li><div class="mi" style="background:#e3f2fd;color:#1565c0"><i class="bi bi-person-gear"></i></div><div><strong>Roles</strong><small><strong>Super Admin:</strong> Full system access + multi-school management. <strong>Admin:</strong> Full school-level access. <strong>Teacher:</strong> Limited to their classes, attendance, diary. <strong>Accountant:</strong> Limited to finance and fee modules.</small></div></li>
          </div>
        </div>
      </div>

      {{-- Settings --}}
      <div class="doc-card card" id="settings">
        <div class="card-header" style="background:linear-gradient(135deg,#eceff1,#cfd8dc);color:#37474f">
          <i class="bi bi-gear-fill"></i> Settings
        </div>
        <div class="card-body">
          <p>Configure school information, WhatsApp API settings, and system preferences.</p>
          <div class="module-list">
            <li><div class="mi" style="background:#e3f2fd;color:#1565c0"><i class="bi bi-building"></i></div><div><strong>School Info</strong><small>Set school name, address, phone, logo, and other branding details.</small></div></li>
            <li><div class="mi" style="background:#e8f5e9;color:#2e7d32"><i class="bi bi-whatsapp"></i></div><div><strong>WhatsApp Config</strong><small>Configure WhatsApp API endpoint and token for messaging integration.</small></div></li>
          </div>
        </div>
      </div>

      {{-- Soft Delete --}}
      <div class="doc-card card" id="softdelete">
        <div class="card-header" style="background:linear-gradient(135deg,#ffebee,#ef9a9a);color:#b71c1c">
          <i class="bi bi-recycle"></i> Soft Delete / Archiving
        </div>
        <div class="card-body">
          <p>The system uses Laravel's Soft Delete feature for data safety. When you delete a record, it is not permanently removed — it is archived.</p>
          <div class="module-list">
            <li><div class="mi" style="background:#fff3e0;color:#e65100"><i class="bi bi-info-circle"></i></div><div><strong>How It Works</strong><small>When a student, teacher, voucher, class, subject, or exam result is deleted, the <code>deleted_at</code> timestamp is set. The record becomes hidden from normal views but remains in the database.</small></div></li>
            <li><div class="mi" style="background:#e8f5e9;color:#2e7d32"><i class="bi bi-arrow-counterclockwise"></i></div><div><strong>Restoring Records</strong><small>Go to <strong>Reports → Archived Records</strong> to see all soft-deleted data. Click "Restore" to bring any record back to active status.</small></div></li>
            <li><div class="mi" style="background:#ffebee;color:#c62828"><i class="bi bi-exclamation-triangle"></i></div><div><strong>Permanent Deletion</strong><small>Use "Delete Forever" to permanently remove a record from the database. This action cannot be undone.</small></div></li>
          </div>
          <div class="alert alert-warning mt-3 mb-0" style="border-radius:10px">
            <i class="bi bi-lightbulb-fill me-2"></i>
            <strong>Supported Models:</strong> Student, Teacher, ClassRoom, Subject, Voucher, ExamResult, Fee, Notice, Diary, LeaveRequest, Payroll
          </div>
        </div>
      </div>

    </div>
  </div>
</main>
@endsection
