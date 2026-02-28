@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
  <div class="pagetitle d-flex align-items-center justify-content-between mb-3">
    <div>
      <h1><i class="bi bi-cash-stack me-2 text-primary"></i>Payroll</h1>
      <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item active">Payroll</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('payroll.advances') }}" class="btn btn-outline-warning btn-sm"><i class="bi bi-cash me-1"></i>Advances</a>
      <a href="{{ route('payroll.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Generate Payroll</a>
    </div>
  </div>

  {{-- Filter Bar --}}
  <div class="card mb-3">
    <div class="card-body py-2">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-auto">
          <label class="form-label small mb-1">Month</label>
          <select name="month" class="form-select form-select-sm">
            @foreach($months as $m)
              <option value="{{ $m['value'] }}" @selected($m['value'] == $month)>{{ $m['label'] }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-auto">
          <label class="form-label small mb-1">Year</label>
          <select name="year" class="form-select form-select-sm">
            @foreach(range(date('Y'), date('Y')-3) as $y)
              <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-auto">
          <label class="form-label small mb-1">Teacher</label>
          <select name="teacher_id" class="form-select form-select-sm">
            <option value="">All Teachers</option>
            @foreach($teachers as $t)
              <option value="{{ $t->id }}" @selected($t->id == $teacherId)>{{ $t->teacher_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-auto"><button class="btn btn-primary btn-sm">Filter</button></div>
      </form>
    </div>
  </div>

  {{-- Stats --}}
  <div class="row g-3 mb-3">
    <div class="col-6 col-lg-3"><div class="ea-stat blue"><div class="ea-stat-icon"><i class="bi bi-cash-stack"></i></div><div class="ea-stat-label">Gross Payroll</div><div class="ea-stat-value">{{ number_format($stats['total_gross'],0) }}</div><div class="ea-stat-sub">Rs. for {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="ea-stat green"><div class="ea-stat-icon"><i class="bi bi-wallet2"></i></div><div class="ea-stat-label">Net Payable</div><div class="ea-stat-value">{{ number_format($stats['total_net'],0) }}</div><div class="ea-stat-sub">After all deductions</div></div></div>
    <div class="col-6 col-lg-3"><div class="ea-stat orange"><div class="ea-stat-icon"><i class="bi bi-check-circle"></i></div><div class="ea-stat-label">Paid</div><div class="ea-stat-value">{{ $stats['paid_count'] }}</div><div class="ea-stat-sub">Payrolls disbursed</div></div></div>
    <div class="col-6 col-lg-3"><div class="ea-stat teal"><div class="ea-stat-icon"><i class="bi bi-hourglass-split"></i></div><div class="ea-stat-label">Pending</div><div class="ea-stat-value">{{ $stats['pending_count'] }}</div><div class="ea-stat-sub">Awaiting payment</div></div></div>
  </div>

  {{-- Table --}}
  <div class="card">
    <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-table me-2 text-primary"></i>Payroll — {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}</h5></div>
    <div class="card-body p-0">
      @if(session('success'))<div class="alert alert-success alert-dismissible m-3 border-0"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>{{ session('success') }}</div>@endif
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead><tr>
            <th>#</th><th>Teacher</th><th>Basic</th><th>Allowances</th><th>Deductions</th>
            <th class="text-success fw-bold">Net Salary</th><th>Attendance</th><th>Status</th><th>Actions</th>
          </tr></thead>
          <tbody>
            @forelse($payrolls as $i => $p)
            <tr>
              <td>{{ $i+1 }}</td>
              <td><strong>{{ $p->teacher?->teacher_name ?? '—' }}</strong><br><small class="text-muted">{{ $p->teacher?->employee_id }}</small></td>
              <td>Rs. {{ number_format($p->basic_salary,0) }}</td>
              <td><span class="text-success">+{{ number_format($p->total_earnings - $p->basic_salary,0) }}</span></td>
              <td><span class="text-danger">-{{ number_format($p->total_deductions,0) }}</span></td>
              <td><strong class="text-success">Rs. {{ number_format($p->net_salary,0) }}</strong></td>
              <td><small>{{ $p->present_days }}/{{ $p->working_days }} days</small></td>
              <td>
                @if($p->status === 'paid') <span class="badge bg-success">Paid</span>
                @elseif($p->status === 'approved') <span class="badge bg-primary">Approved</span>
                @else <span class="badge bg-secondary">Draft</span>
                @endif
              </td>
              <td>
                <div class="d-flex gap-1">
                  <a href="{{ route('payroll.show', $p) }}" class="btn btn-sm btn-outline-info" title="View Payslip"><i class="bi bi-eye-fill"></i></a>
                  <a href="{{ route('payroll.payslip', $p) }}" class="btn btn-sm btn-outline-danger" title="Download PDF"><i class="bi bi-file-earmark-pdf-fill"></i></a>
                  @if($p->status === 'draft')
                  <form action="{{ route('payroll.approve', $p) }}" method="POST">@csrf
                    <button class="btn btn-sm btn-outline-success" title="Approve"><i class="bi bi-check-lg"></i></button>
                  </form>
                  @endif
                  @if($p->status !== 'paid')
                  <form action="{{ route('payroll.destroy', $p) }}" method="POST" onsubmit="return confirm('Delete this payroll?')">@csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash-fill"></i></button>
                  </form>
                  @endif
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center text-muted py-4"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No payroll records for this period.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>
@endsection
