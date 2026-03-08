@extends("admin.layout.master")
@section("content")
<main id="main" class="main">
  <div class="pagetitle d-flex justify-content-between align-items-center flex-wrap">
    <div>
      <h1><i class="bi bi-bar-chart-line-fill text-primary"></i> Reports Hub <span class="badge bg-primary fs-6">Advanced</span></h1>
      <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li><li class="breadcrumb-item active">Reports</li></ol></nav>
    </div>
    <div>
      <span class="badge bg-light text-dark border"><i class="bi bi-gear me-1"></i> View: Advanced</span>
    </div>
  </div>

  <style>
    .report-card{border:none;border-radius:14px;transition:transform .2s,box-shadow .2s;overflow:hidden;height:100%}
    .report-card:hover{transform:translateY(-4px);box-shadow:0 8px 25px rgba(0,0,0,.12)}
    .report-card .card-body{padding:28px 24px}
    .rc-icon{width:56px;height:56px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;margin-bottom:16px}
    .rc-finance .rc-icon{background:linear-gradient(135deg,#e8f5e9,#c8e6c9);color:#2e7d32}
    .rc-fees .rc-icon{background:linear-gradient(135deg,#fff3e0,#ffe0b2);color:#e65100}
    .rc-attendance .rc-icon{background:linear-gradient(135deg,#e3f2fd,#bbdefb);color:#1565c0}
    .rc-students .rc-icon{background:linear-gradient(135deg,#f3e5f5,#e1bee7);color:#7b1fa2}
    .rc-exams .rc-icon{background:linear-gradient(135deg,#e0f7fa,#b2ebf2);color:#00838f}
    .rc-archived .rc-icon{background:linear-gradient(135deg,#fce4ec,#f8bbd0);color:#c62828}
    .rc-vouchers .rc-icon{background:linear-gradient(135deg,#fff8e1,#ffecb3);color:#f57f17}
    .rc-cards .rc-icon{background:linear-gradient(135deg,#e8eaf6,#c5cae9);color:#283593}
    .report-card h5{font-weight:700;font-size:17px;margin-bottom:6px}
    .report-card p{color:#6c757d;font-size:13px;margin-bottom:16px;min-height:40px}
    .report-card .badge{font-size:12px;padding:5px 12px;border-radius:20px}
    .stat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px;margin-top:20px}
    .stat-box{background:#f8f9fa;border-radius:10px;padding:14px;text-align:center}
    .stat-box .num{font-size:26px;font-weight:800;color:#2c3e50}
    .stat-box .lbl{font-size:12px;color:#7f8c8d;text-transform:uppercase;letter-spacing:.5px}
  </style>

  {{-- Quick Stats --}}
  <section class="section">
    <div class="stat-grid">
      <div class="stat-box"><div class="num">{{ number_format($stats['students']) }}</div><div class="lbl">Students</div></div>
      <div class="stat-box"><div class="num">{{ number_format($stats['teachers']) }}</div><div class="lbl">Teachers</div></div>
      <div class="stat-box"><div class="num">{{ number_format($stats['classes']) }}</div><div class="lbl">Classes</div></div>
      <div class="stat-box"><div class="num">{{ number_format($stats['vouchers']) }}</div><div class="lbl">Vouchers</div></div>
      <div class="stat-box"><div class="num">{{ number_format($stats['exams']) }}</div><div class="lbl">Exams</div></div>
      <div class="stat-box"><div class="num text-danger">{{ number_format($stats['trashed']) }}</div><div class="lbl">Archived</div></div>
    </div>
  </section>

  {{-- All Report Cards --}}
  <section class="section">
    <h5 class="fw-bold mb-3"><i class="bi bi-grid me-2 text-primary"></i>All Reports</h5>
    <div class="row g-4">

      <div class="col-md-6 col-lg-4">
        <a href="{{ route('reports.finance') }}" class="text-decoration-none">
          <div class="card report-card rc-finance">
            <div class="card-body">
              <div class="rc-icon"><i class="bi bi-cash-coin"></i></div>
              <h5>Finance Report</h5>
              <p>Income, expenses, profit/loss breakdown with monthly trends, category analysis and charts.</p>
              <span class="badge bg-success bg-opacity-10 text-success">₨ Income vs Expense</span>
            </div>
          </div>
        </a>
      </div>

      <div class="col-md-6 col-lg-4">
        <a href="{{ route('reports.fees') }}" class="text-decoration-none">
          <div class="card report-card rc-fees">
            <div class="card-body">
              <div class="rc-icon"><i class="bi bi-receipt-cutoff"></i></div>
              <h5>Fee Collection</h5>
              <p>Monthly fee collection status, outstanding amounts, class-wise breakdown, paid vs unpaid analytics.</p>
              <span class="badge bg-warning bg-opacity-10 text-warning">Collection Status</span>
            </div>
          </div>
        </a>
      </div>

      <div class="col-md-6 col-lg-4">
        <a href="{{ route('reports.attendance') }}" class="text-decoration-none">
          <div class="card report-card rc-attendance">
            <div class="card-body">
              <div class="rc-icon"><i class="bi bi-calendar-check"></i></div>
              <h5>Attendance Report</h5>
              <p>Class-wise and student-wise attendance rates, trends & absences analysis with status indicators.</p>
              <span class="badge bg-primary bg-opacity-10 text-primary">Present / Absent</span>
            </div>
          </div>
        </a>
      </div>

      <div class="col-md-6 col-lg-4">
        <a href="{{ route('reports.students') }}" class="text-decoration-none">
          <div class="card report-card rc-students">
            <div class="card-body">
              <div class="rc-icon"><i class="bi bi-people-fill"></i></div>
              <h5>Student Report</h5>
              <p>Student demographics, class distribution charts, gender breakdown, active & archived students.</p>
              <span class="badge bg-purple bg-opacity-10 text-purple" style="background:rgba(123,31,162,.1)!important;color:#7b1fa2!important">Demographics</span>
            </div>
          </div>
        </a>
      </div>

      <div class="col-md-6 col-lg-4">
        <a href="{{ route('reports.exams') }}" class="text-decoration-none">
          <div class="card report-card rc-exams">
            <div class="card-body">
              <div class="rc-icon"><i class="bi bi-journal-bookmark"></i></div>
              <h5>Exam Report</h5>
              <p>Exam summaries, score distribution, top 10 performers with ranked badges, per-exam filter.</p>
              <span class="badge bg-info bg-opacity-10 text-info">Results & Top Performers</span>
            </div>
          </div>
        </a>
      </div>

      <div class="col-md-6 col-lg-4">
        <a href="{{ route('reports.archived') }}" class="text-decoration-none">
          <div class="card report-card rc-archived">
            <div class="card-body">
              <div class="rc-icon"><i class="bi bi-archive"></i></div>
              <h5>Archived Records</h5>
              <p>All soft-deleted students, teachers, vouchers, classes, subjects in a tabbed overview.</p>
              <span class="badge bg-danger bg-opacity-10 text-danger">Deleted / Restored</span>
            </div>
          </div>
        </a>
      </div>
    </div>

    {{-- Voucher & Invoice Reports --}}
    <h5 class="fw-bold mt-4 mb-3"><i class="bi bi-receipt me-2 text-warning"></i>Vouchers & Invoices</h5>
    <div class="row g-4">

      <div class="col-md-6 col-lg-4">
        <a href="{{ route('voucher-status.index') }}" class="text-decoration-none">
          <div class="card report-card rc-vouchers">
            <div class="card-body">
              <div class="rc-icon"><i class="bi bi-clipboard-check"></i></div>
              <h5>Voucher Status</h5>
              <p>Complete overview of all student fee vouchers — paid, unpaid, pending with filters by class, month & date range.</p>
              <span class="badge bg-warning bg-opacity-10 text-warning">Paid / Unpaid / Pending</span>
            </div>
          </div>
        </a>
      </div>

      <div class="col-md-6 col-lg-4">
        <a href="{{ route('fee_voucher') }}" class="text-decoration-none">
          <div class="card report-card rc-vouchers">
            <div class="card-body">
              <div class="rc-icon"><i class="bi bi-file-earmark-text"></i></div>
              <h5>Monthly Invoices</h5>
              <p>Class-wise monthly fee voucher generation and management. View, print or generate new invoices.</p>
              <span class="badge bg-info bg-opacity-10 text-info">Class Invoices</span>
            </div>
          </div>
        </a>
      </div>

      <div class="col-md-6 col-lg-4">
        <a href="{{ route('voucher.index') }}" class="text-decoration-none">
          <div class="card report-card rc-vouchers">
            <div class="card-body">
              <div class="rc-icon"><i class="bi bi-journal-plus"></i></div>
              <h5>Journal Vouchers</h5>
              <p>Income and expense journal vouchers — view all transactions, add new vouchers, print challans.</p>
              <span class="badge bg-success bg-opacity-10 text-success">Income / Expense</span>
            </div>
          </div>
        </a>
      </div>
    </div>

    {{-- Academic Reports --}}
    <h5 class="fw-bold mt-4 mb-3"><i class="bi bi-mortarboard me-2 text-info"></i>Academic Reports</h5>
    <div class="row g-4">

      <div class="col-md-6 col-lg-4">
        <a href="{{ route('report-cards.config') }}" class="text-decoration-none">
          <div class="card report-card rc-cards">
            <div class="card-body">
              <div class="rc-icon"><i class="bi bi-sliders"></i></div>
              <h5>Report Card Config</h5>
              <p>Configure report card layout — toggle grade, GPA, position, remarks, and other display options.</p>
              <span class="badge bg-primary bg-opacity-10 text-primary">Configuration</span>
            </div>
          </div>
        </a>
      </div>

      <div class="col-md-6 col-lg-4">
        <a href="{{ route('report-cards.generate') }}" class="text-decoration-none">
          <div class="card report-card rc-cards">
            <div class="card-body">
              <div class="rc-icon"><i class="bi bi-printer"></i></div>
              <h5>Generate Report Cards</h5>
              <p>Generate and print student report cards by class and exam — with grade calculation.</p>
              <span class="badge bg-info bg-opacity-10 text-info">Print / PDF</span>
            </div>
          </div>
        </a>
      </div>

      <div class="col-md-6 col-lg-4">
        <a href="{{ route('attendance_report') }}" class="text-decoration-none">
          <div class="card report-card rc-attendance">
            <div class="card-body">
              <div class="rc-icon"><i class="bi bi-calendar2-week"></i></div>
              <h5>Daily Attendance</h5>
              <p>Daily class-wise attendance marking report — view present/absent/leave for any date.</p>
              <span class="badge bg-primary bg-opacity-10 text-primary">Daily View</span>
            </div>
          </div>
        </a>
      </div>
    </div>
  </section>
</main>
@endsection
