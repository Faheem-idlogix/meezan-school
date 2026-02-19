@extends("admin.layout.master")
@section("content")
<main id="main" class="main">
  <div class="pagetitle">
    <h1><i class="bi bi-calendar-check text-primary"></i> Attendance Report</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li><li class="breadcrumb-item active">Attendance</li></ol></nav>
  </div>

  <style>
    .period-bar{background:#fff;border-radius:12px;padding:12px 18px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,.06);display:flex;align-items:center;gap:8px;flex-wrap:wrap}
    .period-bar .btn-period{border:1px solid #dee2e6;background:#f8f9fa;border-radius:20px;padding:5px 16px;font-size:13px;color:#555;font-weight:500;transition:all .15s;text-decoration:none}
    .period-bar .btn-period:hover,.period-bar .btn-period.active{background:#4154f1;color:#fff;border-color:#4154f1}
    .att-stat{border:none;border-radius:14px;text-align:center;padding:20px 14px}
    .att-stat .number{font-size:28px;font-weight:800}
    .att-stat .label{font-size:11px;text-transform:uppercase;letter-spacing:.5px;opacity:.8}
    .rpt-table{border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.05)}
    .rpt-table thead{background:#4154f1;color:#fff}
    .rpt-table th{font-weight:600;font-size:13px;padding:10px 14px;border:none}
    .rpt-table td{font-size:13px;padding:10px 14px;vertical-align:middle}
    .progress{height:8px;border-radius:4px}
  </style>

  {{-- Period + Class Filter --}}
  <div class="period-bar">
    <span class="fw-semibold text-muted me-2"><i class="bi bi-funnel"></i></span>
    @foreach(['this_month'=>'This Month','last_month'=>'Last Month','6_months'=>'6 Months','this_year'=>'This Year'] as $k=>$v)
      <a href="{{ route('reports.attendance', ['period'=>$k,'class_id'=>$classFilter]) }}" class="btn-period {{ $periodKey===$k?'active':'' }}">{{ $v }}</a>
    @endforeach
    <span class="ms-2">|</span>
    <select onchange="location.href='{{ route('reports.attendance') }}?period={{ $periodKey }}&class_id='+this.value" class="form-select form-select-sm" style="width:180px;border-radius:20px">
      <option value="">All Classes</option>
      @foreach($classrooms as $c)
        <option value="{{ $c->id }}" {{ $classFilter==$c->id?'selected':'' }}>{{ $c->class_name }}</option>
      @endforeach
    </select>
    <span class="ms-auto text-muted small fw-semibold">{{ $periodLabel }}</span>
  </div>

  {{-- Summary --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-2"><div class="att-stat" style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);color:#1565c0"><div class="number">{{ number_format($totalRecords) }}</div><div class="label">Total Records</div></div></div>
    <div class="col-6 col-md-4 col-lg-2"><div class="att-stat" style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);color:#2e7d32"><div class="number">{{ number_format($present) }}</div><div class="label">Present</div></div></div>
    <div class="col-6 col-md-4 col-lg-2"><div class="att-stat" style="background:linear-gradient(135deg,#ffebee,#ffcdd2);color:#c62828"><div class="number">{{ number_format($absent) }}</div><div class="label">Absent</div></div></div>
    <div class="col-6 col-md-4 col-lg-2"><div class="att-stat" style="background:linear-gradient(135deg,#fff3e0,#ffe0b2);color:#e65100"><div class="number">{{ number_format($leave) }}</div><div class="label">On Leave</div></div></div>
    <div class="col-6 col-md-4 col-lg-2"><div class="att-stat" style="background:linear-gradient(135deg,#f3e5f5,#e1bee7);color:#7b1fa2"><div class="number">{{ number_format($late) }}</div><div class="label">Late</div></div></div>
    <div class="col-6 col-md-4 col-lg-2"><div class="att-stat" style="background:linear-gradient(135deg,#e0f7fa,#b2ebf2);color:#00838f"><div class="number">{{ $rate }}%</div><div class="label">Rate</div></div></div>
  </div>

  <div class="row g-4">
    {{-- Class-wise --}}
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm" style="border-radius:14px">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-building text-primary"></i> Class-wise Attendance</h6>
          <div class="table-responsive">
            <table class="table rpt-table table-hover mb-0">
              <thead><tr><th>Class</th><th>Records</th><th>Present</th><th>Absent</th><th>Rate</th></tr></thead>
              <tbody>
                @forelse($classWise as $c)
                <tr>
                  <td class="fw-semibold">{{ $c['class'] }}</td>
                  <td>{{ $c['total'] }}</td>
                  <td class="text-success">{{ $c['present'] }}</td>
                  <td class="text-danger">{{ $c['absent'] }}</td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="progress flex-grow-1"><div class="progress-bar {{ $c['rate']>=80?'bg-success':($c['rate']>=60?'bg-warning':'bg-danger') }}" style="width:{{ $c['rate'] }}%"></div></div>
                      <small class="fw-bold">{{ $c['rate'] }}%</small>
                    </div>
                  </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">No data</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- Student-wise --}}
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm" style="border-radius:14px">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-person-lines-fill text-info"></i> Student-wise Attendance (Top {{ min($studentWise->count(), 30) }})</h6>
          <div class="table-responsive" style="max-height:450px;overflow-y:auto">
            <table class="table rpt-table table-hover mb-0">
              <thead style="position:sticky;top:0"><tr><th>Student</th><th>Class</th><th>P</th><th>A</th><th>Rate</th></tr></thead>
              <tbody>
                @forelse($studentWise->take(30) as $s)
                <tr>
                  <td><a href="{{ route('student.show', $s['student_id']) }}" class="fw-semibold text-decoration-none">{{ $s['student_name'] }}</a></td>
                  <td><small>{{ $s['class_name'] }}</small></td>
                  <td class="text-success fw-bold">{{ $s['present'] }}</td>
                  <td class="text-danger fw-bold">{{ $s['absent'] }}</td>
                  <td>
                    <span class="badge {{ $s['rate']>=80?'bg-success':($s['rate']>=60?'bg-warning':'bg-danger') }}">{{ $s['rate'] }}%</span>
                  </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">No data</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
@endsection
