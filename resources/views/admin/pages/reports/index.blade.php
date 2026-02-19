@extends("admin.layout.master")
@section("content")
<main id="main" class="main">
  <div class="pagetitle">
    <h1><i class="bi bi-bar-chart-line-fill text-primary"></i> Reports Hub</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li><li class="breadcrumb-item active">Reports</li></ol></nav>
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

  {{-- Report Cards --}}
  <section class="section">
    <div class="row g-4">

      <div class="col-md-6 col-lg-4">
        <a href="{{ route('reports.finance') }}" class="text-decoration-none">
          <div class="card report-card rc-finance">
            <div class="card-body">
              <div class="rc-icon"><i class="bi bi-cash-coin"></i></div>
              <h5>Finance Report</h5>
              <p>Income, expenses, profit/loss breakdown with monthly trends and category analysis.</p>
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
              <p>Monthly fee collection status, outstanding amounts, paid vs unpaid analytics.</p>
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
              <p>Class-wise and student-wise attendance rates, trends & absences analysis.</p>
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
              <p>Student enrollment, class distribution, gender breakdown, and active/deleted records.</p>
              <span class="badge bg-purple bg-opacity-10" style="color:#7b1fa2;background:rgba(123,31,162,.1)!important">Enrollment Data</span>
            </div>
          </div>
        </a>
      </div>

      <div class="col-md-6 col-lg-4">
        <a href="{{ route('reports.exams') }}" class="text-decoration-none">
          <div class="card report-card rc-exams">
            <div class="card-body">
              <div class="rc-icon"><i class="bi bi-journal-bookmark-fill"></i></div>
              <h5>Exam Report</h5>
              <p>Exam results summary, top performers, subject-wise analysis and averages.</p>
              <span class="badge bg-info bg-opacity-10 text-info">Results & Rankings</span>
            </div>
          </div>
        </a>
      </div>

      <div class="col-md-6 col-lg-4">
        <a href="{{ route('reports.archived') }}" class="text-decoration-none">
          <div class="card report-card rc-archived">
            <div class="card-body">
              <div class="rc-icon"><i class="bi bi-trash3-fill"></i></div>
              <h5>Archived Records</h5>
              <p>View soft-deleted students, teachers, vouchers, classes and other removed data.</p>
              <span class="badge bg-danger bg-opacity-10 text-danger">{{ $stats['trashed'] }} Records</span>
            </div>
          </div>
        </a>
      </div>

    </div>
  </section>
</main>
@endsection
