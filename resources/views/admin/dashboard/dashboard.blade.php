@extends('admin.layout.master')
@section('css')
<style>
  .bi-x-circle{color:red;margin-left:10px}
  .bi-check-circle{color:green;margin-left:10px}
  .bi-check-circle-fill{color:green;margin-left:10px}

  /* Quick-link cards */
  .quick-link{display:flex;align-items:center;gap:.85rem;padding:1rem 1.2rem;border-radius:8px;background:var(--skin-card-bg,#f8faff);border:1px solid var(--skin-border-subtle,#e9ecef);transition:all .2s;text-decoration:none;color:var(--skin-text-primary,#012970)}
  .quick-link:hover{background:var(--skin-hover-bg,#eef2ff);border-color:var(--skin-accent,var(--ea-primary));transform:translateY(-2px);box-shadow:0 4px 12px rgba(var(--skin-accent-rgb,65,84,241),.12)}
  .quick-link .ql-icon{width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0}
  .quick-link .ql-label{font-size:.82rem;font-weight:600}
  .quick-link .ql-sub{font-size:.72rem;color:var(--skin-text-muted,#6c757d);font-weight:400}

  /* Mini stat – distinct colored cards */
  .mini-stat{padding:.85rem 1rem;border-radius:8px;border:1px solid var(--skin-border-subtle,#e9ecef);border-left:4px solid #6c757d;position:relative}
  .mini-stat .ms-icon{position:absolute;top:.85rem;right:1rem;font-size:1.3rem;opacity:.35}
  .mini-stat .ms-label{font-size:.7rem;text-transform:uppercase;letter-spacing:.8px;font-weight:600;color:var(--skin-text-muted,#6c757d);margin-bottom:2px}
  .mini-stat .ms-value{font-size:1.2rem;font-weight:700;color:var(--skin-text-primary,inherit)}
  .mini-stat.ms-income{border-left-color:#198754;background:linear-gradient(135deg,rgba(25,135,84,.04),rgba(25,135,84,.1))}
  .mini-stat.ms-expense{border-left-color:#dc3545;background:linear-gradient(135deg,rgba(220,53,69,.04),rgba(220,53,69,.1))}
  .mini-stat.ms-profit{border-left-color:var(--skin-accent,#4154f1);background:linear-gradient(135deg,rgba(var(--skin-accent-rgb,65,84,241),.04),rgba(var(--skin-accent-rgb,65,84,241),.1))}
  .mini-stat.ms-outstanding{border-left-color:#fd7e14;background:linear-gradient(135deg,rgba(253,126,20,.04),rgba(253,126,20,.1))}
  .mini-stat.ms-vouchers{border-left-color:#6f42c1;background:linear-gradient(135deg,rgba(111,66,193,.04),rgba(111,66,193,.1))}
  .mini-stat.ms-exams{border-left-color:#0dcaf0;background:linear-gradient(135deg,rgba(13,202,240,.04),rgba(13,202,240,.1))}

  /* Period filter bar */
  .period-bar{background:var(--skin-card-bg,#fff);border:1px solid var(--skin-border-subtle,#e9ecef);border-radius:10px;padding:.6rem 1rem;margin-bottom:1.2rem}
  .period-bar .btn-period{display:inline-block;padding:.35rem .8rem;font-size:.78rem;font-weight:600;border-radius:6px;border:1px solid var(--skin-border-subtle,#dee2e6);background:var(--skin-card-bg,#fff);color:var(--skin-text-primary,#495057);transition:all .2s;text-decoration:none}
  .period-bar .btn-period:hover{background:var(--skin-hover-bg,#eef2ff);border-color:var(--skin-accent,#4154f1);color:var(--skin-accent,#4154f1)}
  .period-bar .btn-period.active{background:var(--skin-accent,#4154f1);border-color:var(--skin-accent,#4154f1);color:#fff}

  /* Attendance card */
  .att-stat{text-align:center;padding:.7rem .5rem;border-radius:8px}
  .att-stat .att-val{font-size:1.4rem;font-weight:700}
  .att-stat .att-lbl{font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;font-weight:600;color:var(--skin-text-muted,#6c757d)}
  .att-class-row:hover{background:rgba(var(--skin-accent-rgb,65,84,241),.04)}
  @keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}
  .spin{animation:spin 1s linear infinite;display:inline-block}

  /* Chart wrapper – fixed compact height */
  .chart-wrapper{position:relative;height:220px;width:100%}
</style>
@endsection
@section('content')
<main id="main" class="main">

    <div class="pagetitle d-flex align-items-center justify-content-between">
      <div>
        <h1>Dashboard</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </nav>
      </div>
      <div class="text-muted small"><i class="bi bi-calendar3 me-1"></i>{{ date('l, d M Y') }}</div>
    </div>

    <section class="section dashboard">

      {{-- ══════════ DATE FILTER BAR ══════════ --}}
      <div class="period-bar d-flex flex-wrap align-items-center gap-2">
        <span class="text-muted small me-1"><i class="bi bi-funnel me-1"></i>Filter:</span>
        <a href="{{ route('home', ['period' => 'this_month']) }}"
           class="btn-period {{ $periodKey == 'this_month' ? 'active' : '' }}">This Month</a>
        <a href="{{ route('home', ['period' => 'last_month']) }}"
           class="btn-period {{ $periodKey == 'last_month' ? 'active' : '' }}">Last Month</a>
        <a href="{{ route('home', ['period' => '6_months']) }}"
           class="btn-period {{ $periodKey == '6_months' ? 'active' : '' }}">6 Months</a>
        <a href="{{ route('home', ['period' => 'this_year']) }}"
           class="btn-period {{ $periodKey == 'this_year' ? 'active' : '' }}">This Year</a>
        <a href="{{ route('home', ['period' => 'last_year']) }}"
           class="btn-period {{ $periodKey == 'last_year' ? 'active' : '' }}">Last Year</a>

        <div class="vr mx-1 d-none d-md-block"></div>

        <form action="{{ route('home') }}" method="GET" class="d-flex align-items-center gap-2 ms-auto flex-wrap">
          <input type="hidden" name="period" value="custom">
          <input type="date" name="from" value="{{ request('from', $dateFrom->format('Y-m-d')) }}"
                 class="form-control form-control-sm" style="width:140px;font-size:.78rem">
          <span class="text-muted small">to</span>
          <input type="date" name="to" value="{{ request('to', $dateTo->format('Y-m-d')) }}"
                 class="form-control form-control-sm" style="width:140px;font-size:.78rem">
          <button type="submit" class="btn btn-sm btn-primary" style="font-size:.78rem">
            <i class="bi bi-search me-1"></i>Apply
          </button>
        </form>
      </div>
      <div class="mb-3">
        <span class="badge bg-primary" style="font-size:.78rem"><i class="bi bi-calendar-range me-1"></i>{{ $periodLabel }}</span>
      </div>

      {{-- ══════════ ROW 1 — PRIMARY STAT CARDS ══════════ --}}
      <div class="row g-3 mb-4">
        <div class="col-xxl-3 col-md-6">
          <div class="ea-stat blue">
            <div class="ea-stat-label">Total Students</div>
            <div class="ea-stat-value">{{ $totalStudents }}</div>
            <div class="ea-stat-sub"><i class="bi bi-arrow-up-short"></i>Enrolled</div>
            <i class="bi bi-people-fill ea-stat-icon"></i>
          </div>
        </div>
        <div class="col-xxl-3 col-md-6">
          <div class="ea-stat green">
            <div class="ea-stat-label">Total Teachers</div>
            <div class="ea-stat-value">{{ $totalTeachers }}</div>
            <div class="ea-stat-sub"><i class="bi bi-person-badge-fill me-1"></i>Staff</div>
            <i class="bi bi-person-workspace ea-stat-icon"></i>
          </div>
        </div>
        <div class="col-xxl-3 col-md-6">
          <div class="ea-stat orange">
            <div class="ea-stat-label">Classes</div>
            <div class="ea-stat-value">{{ $classrooms }}</div>
            <div class="ea-stat-sub"><i class="bi bi-journal-text me-1"></i>Active</div>
            <i class="bi bi-building ea-stat-icon"></i>
          </div>
        </div>
        <div class="col-xxl-3 col-md-6">
          <div class="ea-stat teal">
            <div class="ea-stat-label">School Fee | {{ $periodLabel }}</div>
            <div class="ea-fee-row">
              <div class="ea-fee-item">
                <div class="ea-fee-amt">₨ {{ number_format($feeReceived) }}</div>
                <div class="ea-fee-lbl">Collected</div>
              </div>
              <div style="font-size:1.2rem;opacity:.5;font-weight:300">/</div>
              <div class="ea-fee-item">
                <div class="ea-fee-amt">₨ {{ number_format($totalFee) }}</div>
                <div class="ea-fee-lbl">Total Billed</div>
              </div>
            </div>
            <div class="ea-stat-sub"><i class="bi bi-receipt-cutoff me-1"></i>{{ $students->count() }} fee records</div>
            <i class="bi bi-cash-coin ea-stat-icon"></i>
          </div>
        </div>
      </div>

      {{-- ══════════ ROW 2 — FINANCE MINI STATS (above attendance) ══════════ --}}
      <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
          <div class="mini-stat ms-income">
            <i class="bi bi-arrow-up-circle-fill ms-icon text-success"></i>
            <div class="ms-label">Voucher Income</div>
            <div class="ms-value text-success">₨ {{ number_format($totalIncome) }}</div>
            <div class="text-muted" style="font-size:.65rem">Journal vouchers</div>
          </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
          <div class="mini-stat ms-expense">
            <i class="bi bi-arrow-down-circle-fill ms-icon text-danger"></i>
            <div class="ms-label">Voucher Expense</div>
            <div class="ms-value text-danger">₨ {{ number_format($totalExpense) }}</div>
            <div class="text-muted" style="font-size:.65rem">Journal vouchers</div>
          </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
          <div class="mini-stat ms-profit">
            <i class="bi bi-{{ $profitLoss >= 0 ? 'graph-up-arrow' : 'graph-down-arrow' }} ms-icon {{ $profitLoss >= 0 ? 'text-primary' : 'text-danger' }}"></i>
            <div class="ms-label">Profit / Loss</div>
            <div class="ms-value {{ $profitLoss >= 0 ? 'text-primary' : 'text-danger' }}">₨ {{ number_format($profitLoss) }}</div>
            <div class="text-muted" style="font-size:.65rem">Income − Expense</div>
          </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
          <div class="mini-stat ms-outstanding">
            <i class="bi bi-exclamation-triangle-fill ms-icon text-warning"></i>
            <div class="ms-label">Fee Outstanding</div>
            <div class="ms-value" style="color:#fd7e14">₨ {{ number_format($feeOutstanding) }}</div>
            <div class="text-muted" style="font-size:.65rem">School fee unpaid</div>
          </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
          <div class="mini-stat ms-vouchers">
            <i class="bi bi-journal-bookmark-fill ms-icon" style="color:#6f42c1"></i>
            <div class="ms-label">Period Vouchers</div>
            <div class="ms-value" style="color:#6f42c1">{{ $monthVouchers }}</div>
            <div class="text-muted" style="font-size:.65rem">Income + Expense</div>
          </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
          <div class="mini-stat ms-exams">
            <i class="bi bi-mortarboard-fill ms-icon" style="color:#0dcaf0"></i>
            <div class="ms-label">Total Exams</div>
            <div class="ms-value" style="color:#0dcaf0">{{ $totalExams }}</div>
            <div class="text-muted" style="font-size:.65rem">All time</div>
          </div>
        </div>
      </div>

      {{-- ══════════ ROW 3 — ATTENDANCE (AJAX-powered) ══════════ --}}
      <div class="row g-3 mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
              <h5 class="card-title mb-0">
                <i class="bi bi-clipboard-check me-2 text-success"></i>Attendance
                <span class="text-muted fw-normal small ms-1" id="att-period-label">| Loading…</span>
              </h5>
              <div class="d-flex gap-2 flex-wrap align-items-center">
                <div class="btn-group btn-group-sm" id="att-filter-btns">
                  <button class="btn btn-outline-success active" data-filter="today">Today</button>
                  <button class="btn btn-outline-success" data-filter="yesterday">Yesterday</button>
                  <button class="btn btn-outline-success" data-filter="this_month">This Month</button>
                  <button class="btn btn-outline-success" data-filter="last_month">Last Month</button>
                </div>
                <a href="{{ route('attendance') }}" class="btn btn-sm btn-outline-success">Manage</a>
              </div>
            </div>
            <div class="card-body pt-3">
              {{-- Stat cards --}}
              <div class="row g-3" id="att-stats-row">
                <div class="col-xl col-md-4 col-6"><div class="att-stat" style="background:rgba(25,135,84,.08)"><div class="att-val text-success" id="att-present">0</div><div class="att-lbl">Present</div></div></div>
                <div class="col-xl col-md-4 col-6"><div class="att-stat" style="background:rgba(220,53,69,.08)"><div class="att-val text-danger" id="att-absent">0</div><div class="att-lbl">Absent</div></div></div>
                <div class="col-xl col-md-4 col-6"><div class="att-stat" style="background:rgba(253,126,20,.08)"><div class="att-val text-warning" id="att-leave">0</div><div class="att-lbl">On Leave</div></div></div>
                <div class="col-xl col-md-4 col-6"><div class="att-stat" style="background:rgba(13,202,240,.08)"><div class="att-val" style="color:#0dcaf0" id="att-late">0</div><div class="att-lbl">Late</div></div></div>
                <div class="col-xl col-md-4 col-6"><div class="att-stat" style="background:rgba(108,117,125,.08)"><div class="att-val text-secondary" id="att-unmarked">0</div><div class="att-lbl">Unmarked</div></div></div>
                <div class="col-xl col-md-4 col-6"><div class="att-stat" style="background:rgba(65,84,241,.08)"><div class="att-val text-primary" id="att-rate">0%</div><div class="att-lbl">Att. Rate</div></div></div>
              </div>

              {{-- Class-wise breakdown --}}
              <hr class="mt-3 mb-2">
              <p class="text-muted small mb-2 fw-semibold"><i class="bi bi-building me-1"></i>Class-wise Breakdown <small class="text-info">(click a class to see students)</small></p>
              <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" style="font-size:.82rem">
                  <thead class="table-light">
                    <tr>
                      <th>Class</th>
                      <th class="text-center">Present</th>
                      <th class="text-center">Absent</th>
                      <th class="text-center">Leave</th>
                      <th class="text-center">Late</th>
                      <th class="text-center">Total</th>
                      <th class="text-center">Rate</th>
                    </tr>
                  </thead>
                  <tbody id="att-class-tbody">
                    <tr><td colspan="7" class="text-center text-muted py-3"><i class="bi bi-arrow-repeat spin me-1"></i>Loading…</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Student Attendance Modal --}}
      <div class="modal fade" id="classAttModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="bi bi-people me-2"></i><span id="modal-class-name">Class</span></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
              <div class="p-3 bg-light border-bottom" id="modal-summary"></div>
              <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" style="font-size:.82rem">
                  <thead class="table-light">
                    <tr id="modal-thead"></tr>
                  </thead>
                  <tbody id="modal-students-tbody">
                    <tr><td colspan="8" class="text-center py-4">Loading…</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- ══════════ ROW 4 — MONTHLY CHART + QUICK LINKS ══════════ --}}
      <div class="row g-3 mb-4">
        <div class="col-lg-8">
          <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="card-title mb-0"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Financial Overview
                <span class="text-muted fw-normal small ms-1">| {{ $periodLabel }}</span>
              </h5>
              <a href="{{ route('finance.index') }}" class="btn btn-sm btn-outline-primary">Finance Hub</a>
            </div>
            <div class="card-body pt-3">
              <div class="chart-wrapper">
                <canvas id="monthlyChart"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card h-100">
            <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-lightning-charge me-2 text-warning"></i>Quick Actions</h5></div>
            <div class="card-body d-flex flex-column gap-2 pt-3">
              <a href="{{ route('finance.index') }}" class="quick-link">
                <div class="ql-icon" style="background:rgba(65,84,241,.12);color:#4154f1"><i class="bi bi-graph-up-arrow"></i></div>
                <div><div class="ql-label">Finance Hub</div><div class="ql-sub">Ledger · Expenses · Reports</div></div>
              </a>
              <a href="{{ route('fee_voucher_create') }}" class="quick-link">
                <div class="ql-icon" style="background:rgba(25,135,84,.12);color:#198754"><i class="bi bi-receipt"></i></div>
                <div><div class="ql-label">Create Monthly Invoice</div><div class="ql-sub">Generate fee vouchers</div></div>
              </a>
              <a href="{{ route('notice.create') }}" class="quick-link">
                <div class="ql-icon" style="background:rgba(253,126,20,.12);color:#fd7e14"><i class="bi bi-megaphone-fill"></i></div>
                <div><div class="ql-label">Send Notice</div><div class="ql-sub">Broadcast to parents</div></div>
              </a>
              <a href="{{ route('diary.create') }}" class="quick-link">
                <div class="ql-icon" style="background:rgba(13,202,240,.12);color:#0dcaf0"><i class="bi bi-journal-richtext"></i></div>
                <div><div class="ql-label">Daily Diary</div><div class="ql-sub">Add today's entry</div></div>
              </a>
              <a href="{{ route('whatsapp.index') }}" class="quick-link">
                <div class="ql-icon" style="background:rgba(111,66,193,.12);color:#6f42c1"><i class="bi bi-whatsapp"></i></div>
                <div><div class="ql-label">WhatsApp Hub</div><div class="ql-sub">Send messages & sync</div></div>
              </a>
            </div>
          </div>
        </div>
      </div>

      {{-- ══════════ ROW 5 — RECENT EXPENSES + NOTICES ══════════ --}}
      <div class="row g-3 mb-4">
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="card-title mb-0"><i class="bi bi-receipt-cutoff me-2 text-danger"></i>Recent Expenses</h5>
              <a href="{{ route('finance.index', ['tab' => 'ledger', 'type' => 'expense']) }}" class="btn btn-sm btn-outline-danger">View All</a>
            </div>
            <div class="card-body p-0">
              @forelse($recentExpenses as $exp)
              <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                <div class="rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center"
                     style="width:38px;height:38px;background:rgba(220,53,69,.1);">
                  <i class="bi bi-dash-circle" style="color:#dc3545;font-size:.9rem;"></i>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                  <div class="fw-semibold text-truncate" style="font-size:.84rem;color:#012970;">{{ $exp->category }} — {{ $exp->description ?? 'No description' }}</div>
                  <div class="text-muted" style="font-size:.74rem;">{{ $exp->created_at->format('d M Y') }} · {{ ucfirst($exp->payment_mode ?? 'cash') }}</div>
                </div>
                <div class="fw-bold text-danger" style="font-size:.9rem;">{{ number_format($exp->amount) }}</div>
              </div>
              @empty
              <div class="text-center py-4 text-muted">
                <i class="bi bi-receipt fs-1 mb-2 d-block" style="color:#c5cde8"></i>
                <small>No expenses recorded</small>
              </div>
              @endforelse
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="card-title mb-0"><i class="bi bi-megaphone me-2 text-primary"></i>Recent Notices</h5>
              <a href="{{ route('notice.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
              @forelse($recentNotices as $notice)
              <div class="d-flex align-items-start gap-3 p-3 border-bottom">
                <div class="rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center"
                     style="width:38px;height:38px;background:rgba(65,84,241,.1);">
                  <i class="bi bi-megaphone-fill" style="color:#4154f1;font-size:.9rem;"></i>
                </div>
                <div class="overflow-hidden">
                  <div class="fw-semibold text-truncate" style="font-size:.84rem;color:#012970;">{{ $notice->title }}</div>
                  <div class="text-muted" style="font-size:.76rem;">{{ $notice->created_at->diffForHumans() }}</div>
                </div>
              </div>
              @empty
              <div class="text-center py-5 text-muted">
                <i class="bi bi-megaphone fs-1 mb-2 d-block" style="color:#c5cde8;"></i>
                <small>No notices yet</small>
              </div>
              @endforelse
            </div>
          </div>
        </div>
      </div>

      {{-- ══════════ VOUCHER TYPES INFO ══════════ --}}
      <div class="row g-3 mb-4">
        <div class="col-12">
          <div class="alert mb-0 border-0 shadow-sm" style="background:linear-gradient(135deg,#f0f4ff,#e8ecff);border-radius:10px">
            <div class="d-flex flex-wrap gap-4 align-items-center">
              <div>
                <i class="bi bi-info-circle-fill text-primary me-1"></i>
                <strong class="small">Voucher Types Explained:</strong>
              </div>
              <div class="small">
                <span class="badge bg-success bg-opacity-10 text-success me-1">School Fee</span>
                Monthly student fee invoices (billed: ₨ {{ number_format($totalFee) }}, collected: ₨ {{ number_format($feeReceived) }})
              </div>
              <div class="small">
                <span class="badge bg-primary bg-opacity-10 text-primary me-1">Journal Voucher</span>
                Manual income/expense entries ({{ $monthVouchers }} vouchers: ₨ {{ number_format($totalIncome) }} in, ₨ {{ number_format($totalExpense) }} out)
              </div>
              <a href="{{ route('finance.index') }}" class="btn btn-sm btn-outline-primary ms-auto"><i class="bi bi-graph-up-arrow me-1"></i>All Vouchers</a>
            </div>
          </div>
        </div>
      </div>

      {{-- ══════════ ROW 6 — RECENT ACTIVITY LOGS ══════════ --}}
      <div class="row g-3 mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2 text-info"></i>Recent Activity</h5>
              <a href="{{ route('activity_logs.index') }}" class="btn btn-sm btn-outline-info">View All Logs</a>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover table-sm mb-0" style="font-size:.82rem">
                  <thead class="table-light">
                    <tr>
                      <th style="width:150px">Time</th>
                      <th style="width:120px">User</th>
                      <th style="width:100px">Action</th>
                      <th>Description</th>
                      <th style="width:120px">Model</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($recentLogs as $log)
                    <tr>
                      <td class="text-muted">
                        <i class="bi bi-clock me-1"></i>{{ $log->created_at->diffForHumans() }}
                      </td>
                      <td>
                        <span class="fw-semibold">{{ $log->user_name ?? 'System' }}</span>
                      </td>
                      <td>
                        @php
                          $actionColors = [
                            'created' => 'success',
                            'updated' => 'primary',
                            'deleted' => 'danger',
                            'restored' => 'info',
                            'force_deleted' => 'dark',
                            'login' => 'warning',
                            'logout' => 'secondary',
                          ];
                          $color = $actionColors[$log->action] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }}">{{ ucfirst($log->action) }}</span>
                      </td>
                      <td class="text-truncate" style="max-width:300px">
                        {{ $log->description }}
                      </td>
                      <td>
                        @if($log->model_type)
                          <span class="badge bg-light text-dark border">{{ class_basename($log->model_type) }}
                            @if($log->model_id) #{{ $log->model_id }} @endif
                          </span>
                        @else
                          <span class="text-muted">—</span>
                        @endif
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="5" class="text-center py-4 text-muted">
                        <i class="bi bi-clock-history fs-1 mb-2 d-block" style="color:#c5cde8"></i>
                        <small>No activity logged yet</small>
                      </td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- ══════════ ROW 6B — SMART DASHBOARD WIDGETS ══════════ --}}
      <div class="row g-3">
        {{-- Admission Pipeline --}}
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body pt-3">
              <h5 class="card-title"><i class="bi bi-person-plus-fill me-1 text-primary"></i>Admission Pipeline</h5>
              <div class="d-flex flex-column gap-2">
                <div class="d-flex justify-content-between"><span>Enquiries</span><span class="badge bg-secondary">{{ $admissionStats['enquiry'] ?? 0 }}</span></div>
                <div class="d-flex justify-content-between"><span>Test Scheduled</span><span class="badge bg-info">{{ $admissionStats['test_scheduled'] ?? 0 }}</span></div>
                <div class="d-flex justify-content-between"><span>Approved</span><span class="badge bg-success">{{ $admissionStats['approved'] ?? 0 }}</span></div>
                <div class="d-flex justify-content-between"><span>Enrolled</span><span class="badge bg-primary">{{ $admissionStats['enrolled'] ?? 0 }}</span></div>
                <div class="d-flex justify-content-between"><span>Rejected</span><span class="badge bg-danger">{{ $admissionStats['rejected'] ?? 0 }}</span></div>
                <hr class="my-1">
                <div class="d-flex justify-content-between fw-bold"><span>Total</span><span>{{ $admissionStats['total'] ?? 0 }}</span></div>
              </div>
              <a href="{{ route('admission.index') }}" class="btn btn-outline-primary btn-sm mt-2 w-100">View All Enquiries</a>
            </div>
          </div>
        </div>

        {{-- Fee Defaulters --}}
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body pt-3">
              <h5 class="card-title"><i class="bi bi-exclamation-triangle-fill me-1 text-danger"></i>Fee Alerts</h5>
              @if($overdueInstallments > 0)
              <div class="alert alert-danger py-2 mb-2">
                <strong>{{ $overdueInstallments }}</strong> overdue installments found!
              </div>
              @endif
              @if($feeDefaulters->count() > 0)
              <div class="table-responsive">
                <table class="table table-sm mb-0">
                  <thead><tr><th>Student</th><th>Due</th></tr></thead>
                  <tbody>
                    @foreach($feeDefaulters->take(5) as $plan)
                    <tr>
                      <td><small>{{ $plan->student->student_name ?? '—' }}</small></td>
                      <td><small class="text-danger fw-bold">Rs. {{ number_format($plan->remaining_amount) }}</small></td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="text-center text-muted py-3"><i class="bi bi-check-circle fs-1 text-success d-block mb-1"></i><small>No overdue payments</small></div>
              @endif
              <a href="{{ route('fee-installments.index') }}" class="btn btn-outline-danger btn-sm mt-2 w-100">View Installment Plans</a>
            </div>
          </div>
        </div>

        {{-- Exam Analytics + Behavior --}}
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body pt-3">
              <h5 class="card-title"><i class="bi bi-graph-up me-1 text-success"></i>Exam & Behavior</h5>
              @if($latestExam)
              <p class="mb-2"><small class="text-muted">Latest Exam:</small> <strong>{{ $latestExam->exam_name }}</strong></p>
              <div class="d-flex gap-3 mb-2">
                <div class="text-center flex-fill"><div class="fs-5 fw-bold text-primary">{{ $examAnalytics['avg_percentage'] }}%</div><small>Avg Score</small></div>
                <div class="text-center flex-fill"><div class="fs-5 fw-bold text-success">{{ $examAnalytics['pass_count'] }}</div><small>Passed</small></div>
                <div class="text-center flex-fill"><div class="fs-5 fw-bold text-danger">{{ $examAnalytics['fail_count'] }}</div><small>Failed</small></div>
              </div>
              @else
              <p class="text-muted">No exams yet</p>
              @endif
              <hr class="my-2">
              <p class="mb-1"><small class="text-muted">Student Behavior:</small></p>
              <div class="d-flex gap-3">
                <div class="text-center flex-fill"><div class="fs-6 fw-bold text-success">{{ $behaviorStats['positive'] }}</div><small>Positive</small></div>
                <div class="text-center flex-fill"><div class="fs-6 fw-bold text-danger">{{ $behaviorStats['negative'] }}</div><small>Negative</small></div>
                <div class="text-center flex-fill"><div class="fs-6 fw-bold text-secondary">{{ $behaviorStats['neutral'] }}</div><small>Neutral</small></div>
              </div>
              <div class="d-flex gap-2 mt-2">
                <a href="{{ route('report-cards.generate') }}" class="btn btn-outline-success btn-sm flex-fill">Report Cards</a>
                <a href="{{ route('behavior.index') }}" class="btn btn-outline-info btn-sm flex-fill">Behavior</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- ══════════ ROW 7 — FEE REPORT TABLE ══════════ --}}
      <div class="row g-3">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
              <h5 class="card-title mb-0"><i class="bi bi-table me-2 text-primary"></i>School Fee Report
                <span class="text-muted fw-normal small ms-1">| {{ $periodLabel }}</span>
              </h5>
              <div class="d-flex gap-2 align-items-center">
                <span class="badge bg-info text-white">Billed: ₨ {{ number_format($totalFee) }}</span>
                <span class="badge bg-success">Collected: ₨ {{ number_format($feeReceived) }}</span>
                <span class="badge bg-warning text-dark">Outstanding: ₨ {{ number_format($feeOutstanding) }}</span>
                <span class="badge bg-primary">{{ $students->count() }} records</span>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover datatable mb-0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Student</th>
                      <th>Class</th>
                      <th>Fee</th>
                      <th>Status</th>
                      <th>Received</th>
                      <th>Enter Amt</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  @php $sr_no = 1; @endphp
                  <tbody>
                    @foreach ($students as $student)
                    <tr id="row-{{ $student->student_fee_id }}">
                      <td class="fw-semibold">{{ $sr_no++ }}</td>
                      <td>
                        <a href="{{ route('student.show', $student->student_id) }}" class="text-decoration-none text-primary fw-semibold" title="View Profile">
                          {{ $student->student->student_name ?? '' }}
                        </a>
                      </td>
                      <td><span class="badge" style="background:#f0f4ff;color:#012970;">{{ $student->student->classroom->class_name ?? '' }}</span></td>
                      <td class="fw-semibold">{{ number_format($student->total_fee) }}</td>
                      <td class="fee-status">
                        @if($student->status == 'paid')
                          <span class="badge bg-success">Paid</span>
                        @elseif($student->status == 'unpaid')
                          <span class="badge bg-danger">Unpaid</span>
                        @elseif($student->status == 'pending')
                          <span class="badge bg-warning text-dark">Pending</span>
                        @else
                          <span class="badge bg-secondary">{{ $student->status }}</span>
                        @endif
                      </td>
                      <td class="recieved_fee">{{ $student->received_payment_fee ?? '—' }}</td>
                      <td style="width:110px">
                        <input type="text" name="recieved_amount_fee"
                          class="form-control form-control-sm recieved-amount"
                          data-student-id="{{ $student->student_fee_id }}"
                          oninput="validateNumber(this)" placeholder="0">
                      </td>
                      <td>
                        <div class="d-flex gap-1 fee-actions">
                          <button type="button" id="edit-fee" data-id="{{ $student->student_fee_id }}"
                            class="btn btn-sm btn-outline-secondary rounded-circle" data-bs-toggle="tooltip" title="Clear / Reset">
                            <i class="bi bi-x-lg edit-fee"></i>
                          </button>
                          <button type="button" id="add-fee" data-id="{{ $student->student_fee_id }}"
                            class="btn btn-sm btn-outline-primary rounded-circle" data-bs-toggle="tooltip" title="Add Partial Payment">
                            <i class="bi bi-plus-lg add-fee"></i>
                          </button>
                          <button type="button" id="submit-fee" data-id="{{ $student->student_fee_id }}"
                            class="btn btn-sm btn-outline-success rounded-circle" data-bs-toggle="tooltip" title="Mark as Fully Paid">
                            <i class="bi bi-check2-all submit-fee"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

    </section>

  </main>
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
  // ── Monthly Chart (compact, fixed 220px via CSS wrapper) ──
  (function(){
    const data = @json($monthlyChart);
    const ctx = document.getElementById('monthlyChart');
    if(!ctx) return;
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: data.map(r => r.label),
        datasets: [
          { label: 'Income',    data: data.map(r => r.income),    backgroundColor: 'rgba(25,135,84,.7)',  borderRadius: 4, barPercentage: 0.6 },
          { label: 'Expense',   data: data.map(r => r.expense),   backgroundColor: 'rgba(220,53,69,.7)',  borderRadius: 4, barPercentage: 0.6 },
          { label: 'Fee Collected', data: data.map(r => r.collected), type:'line', borderColor:'#4154f1', backgroundColor:'rgba(65,84,241,.1)', fill:true, tension:.4, pointRadius:4 },
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position:'bottom', labels:{ usePointStyle:true, padding:12, font:{size:11} } },
          tooltip: {
            callbacks: {
              label: function(c){ return c.dataset.label + ': ' + Number(c.raw).toLocaleString(); }
            }
          }
        },
        scales: {
          y: { beginAtZero:true, ticks:{ callback: v => v >= 1000 ? (v/1000)+'k' : v, font:{size:10} }, grid:{color:'rgba(0,0,0,.04)'} },
          x: { ticks:{ font:{size:10} }, grid:{display:false} }
        }
      }
    });
  })();

  // ── Fee Actions ──
  $(document).on('click', '#edit-fee', function(e) {
      e.preventDefault();
      var studentId = $(this).data('id');
      $.ajax({
          url: "{{ route('edit_fee') }}",
          type: 'post',
          data: { id: studentId, _token: '{{ csrf_token() }}' },
          success: function(response) {
            $('#row-' + studentId + ' .recieved_fee').text(response.student.received_payment_fee);
            $('#row-' + studentId + ' .fee-status').html(response.updatedStatusHTML);
          }
      });
  });

  $(document).on('click', '#add-fee', function(e) {
      e.preventDefault();
      var studentId = $(this).data('id');
      var fee = $('.recieved-amount[data-student-id="' + studentId + '"]').val();
      $.ajax({
          url: "{{ route('add_fee') }}",
          type: 'post',
          data: { id: studentId, fee: fee, _token: '{{ csrf_token() }}' },
          success: function(response) {
            $('#row-' + studentId + ' .recieved_fee').text(response.student.received_payment_fee);
            $('#row-' + studentId + ' .fee-status').html(response.updatedStatusHTML);
          }
      });
  });

  $(document).on('click', '#submit-fee', function(e) {
      e.preventDefault();
      var studentId = $(this).data('id');
      $.ajax({
          url: "{{ route('add_full_fee') }}",
          type: 'post',
          data: { id: studentId, _token: '{{ csrf_token() }}' },
          success: function(response) {
            $('#row-' + studentId + ' .recieved_fee').text(response.student.received_payment_fee);
            $('#row-' + studentId + ' .fee-status').html(response.updatedStatusHTML);
          }
      });
  });

  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
      new bootstrap.Tooltip(el, { trigger: 'hover' });
  });

  // ══════════ ATTENDANCE DASHBOARD — AJAX ══════════
  (function(){
    var currentFilter = 'today';
    var attModal = new bootstrap.Modal(document.getElementById('classAttModal'));

    // Load stats on page load
    loadAttendanceStats('today');

    // Filter button clicks
    $('#att-filter-btns').on('click', 'button', function(){
      $('#att-filter-btns button').removeClass('active');
      $(this).addClass('active');
      currentFilter = $(this).data('filter');
      loadAttendanceStats(currentFilter);
    });

    function loadAttendanceStats(filter) {
      $.ajax({
        url: "{{ route('attendance.dashboard-stats') }}",
        data: { filter: filter },
        success: function(d) {
          $('#att-period-label').text('| ' + d.label);
          $('#att-present').text(d.present.toLocaleString());
          $('#att-absent').text(d.absent.toLocaleString());
          $('#att-leave').text(d.leave.toLocaleString());
          $('#att-late').text(d.late.toLocaleString());
          $('#att-unmarked').text(d.unmarked.toLocaleString());
          $('#att-rate').text(d.rate + '%');

          var tbody = '';
          if(d.classes.length === 0){
            tbody = '<tr><td colspan="7" class="text-center text-muted py-3">No attendance data for this period</td></tr>';
          } else {
            d.classes.forEach(function(c){
              var r = c.total > 0 ? (c.present / c.total * 100).toFixed(1) : 0;
              var barClass = r >= 80 ? 'bg-success' : (r >= 50 ? 'bg-warning' : 'bg-danger');
              var name = c.class_name + (c.section_name ? ' - ' + c.section_name : '');
              tbody += '<tr class="att-class-row" role="button" data-class-id="' + c.class_id + '" style="cursor:pointer">' +
                '<td class="fw-semibold"><i class="bi bi-arrow-right-short text-primary me-1"></i>' + name + '</td>' +
                '<td class="text-center text-success fw-semibold">' + c.present + '</td>' +
                '<td class="text-center text-danger fw-semibold">' + c.absent + '</td>' +
                '<td class="text-center text-warning fw-semibold">' + c.on_leave + '</td>' +
                '<td class="text-center" style="color:#0dcaf0">' + c.late + '</td>' +
                '<td class="text-center">' + c.total + '</td>' +
                '<td class="text-center"><div class="progress" style="height:6px;min-width:60px"><div class="progress-bar ' + barClass + '" style="width:' + r + '%"></div></div><small class="text-muted">' + r + '%</small></td>' +
                '</tr>';
            });
          }
          $('#att-class-tbody').html(tbody);
        }
      });
    }

    // Click class row → show student modal
    $(document).on('click', '.att-class-row', function(){
      var classId = $(this).data('class-id');
      $('#modal-class-name').text('Loading…');
      $('#modal-students-tbody').html('<tr><td colspan="8" class="text-center py-4"><i class="bi bi-arrow-repeat spin me-1"></i>Loading students…</td></tr>');
      $('#modal-summary').html('');
      attModal.show();

      $.ajax({
        url: "{{ route('attendance.class-students') }}",
        data: { class_id: classId, filter: currentFilter },
        success: function(d) {
          $('#modal-class-name').text(d.class_name + ' — ' + d.label);

          var isSingle = d.students.length > 0 && d.students[0].is_single;

          // Summary
          var pCount = 0, aCount = 0, lCount = 0, ltCount = 0, uCount = 0;
          d.students.forEach(function(s){
            if(isSingle){
              if(s.status==='present') pCount++;
              else if(s.status==='absent') aCount++;
              else if(s.status==='leave') lCount++;
              else if(s.status==='late') ltCount++;
              else uCount++;
            } else {
              pCount += s.present; aCount += s.absent; lCount += s.leave; ltCount += s.late;
            }
          });
          var summaryHtml = '<div class="d-flex gap-3 flex-wrap" style="font-size:.82rem">' +
            '<span><i class="bi bi-people me-1"></i><strong>' + d.students.length + '</strong> Students</span>' +
            '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Present: <strong>' + pCount + '</strong></span>' +
            '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Absent: <strong>' + aCount + '</strong></span>' +
            '<span class="text-warning"><i class="bi bi-dash-circle me-1"></i>Leave: <strong>' + lCount + '</strong></span>' +
            '<span style="color:#0dcaf0"><i class="bi bi-clock me-1"></i>Late: <strong>' + ltCount + '</strong></span>';
          if(isSingle) summaryHtml += '<span class="text-secondary"><i class="bi bi-question-circle me-1"></i>Unmarked: <strong>' + uCount + '</strong></span>';
          summaryHtml += '</div>';
          $('#modal-summary').html(summaryHtml);

          // Table header
          var thead = '<th>#</th><th>Student Name</th><th>Father Name</th>';
          if(isSingle){
            thead += '<th class="text-center">Status</th>';
          } else {
            thead += '<th class="text-center">Present</th><th class="text-center">Absent</th><th class="text-center">Leave</th><th class="text-center">Late</th><th class="text-center">Rate</th>';
          }
          $('#modal-thead').html(thead);

          // Table body
          var rows = '';
          if(d.students.length === 0){
            rows = '<tr><td colspan="8" class="text-center text-muted py-3">No students found</td></tr>';
          } else {
            d.students.forEach(function(s, i){
              rows += '<tr><td>' + (i+1) + '</td><td class="fw-semibold">' + s.name + '</td><td>' + (s.father || '—') + '</td>';
              if(isSingle){
                var badge = {present:'bg-success',absent:'bg-danger',leave:'bg-warning text-dark',late:'bg-info',unmarked:'bg-secondary'};
                var label = s.status.charAt(0).toUpperCase() + s.status.slice(1);
                rows += '<td class="text-center"><span class="badge ' + (badge[s.status]||'bg-secondary') + '">' + label + '</span></td>';
              } else {
                var barCl = s.rate >= 80 ? 'bg-success' : (s.rate >= 50 ? 'bg-warning' : 'bg-danger');
                rows += '<td class="text-center text-success fw-semibold">' + s.present + '</td>' +
                  '<td class="text-center text-danger fw-semibold">' + s.absent + '</td>' +
                  '<td class="text-center text-warning fw-semibold">' + s.leave + '</td>' +
                  '<td class="text-center" style="color:#0dcaf0">' + s.late + '</td>' +
                  '<td class="text-center"><div class="progress d-inline-flex" style="height:6px;width:50px;vertical-align:middle"><div class="progress-bar ' + barCl + '" style="width:' + s.rate + '%"></div></div> <small>' + s.rate + '%</small></td>';
              }
              rows += '</tr>';
            });
          }
          $('#modal-students-tbody').html(rows);
        }
      });
    });
  })();
</script>
@endsection