@extends("admin.layout.master")
@section("content")
<main id="main" class="main">
  <div class="pagetitle d-flex justify-content-between align-items-center flex-wrap">
    <div>
      <h1><i class="bi bi-calendar-check text-primary"></i> Attendance Report <span class="badge bg-primary fs-6">Advanced</span></h1>
      <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li><li class="breadcrumb-item active">Attendance (Advanced)</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-success btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i> Print</button>
    </div>
  </div>

  <style>
    .period-bar{background:#fff;border-radius:12px;padding:12px 18px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,.06);display:flex;align-items:center;gap:8px;flex-wrap:wrap}
    .period-bar .btn-period{border:1px solid #dee2e6;background:#f8f9fa;border-radius:20px;padding:5px 16px;font-size:13px;color:#555;font-weight:500;transition:all .15s;text-decoration:none}
    .period-bar .btn-period:hover,.period-bar .btn-period.active{background:#1565c0;color:#fff;border-color:#1565c0}
    .att-stat{border:none;border-radius:14px;text-align:center;padding:20px 14px}
    .att-stat .number{font-size:24px;font-weight:800}
    .att-stat .label{font-size:11px;text-transform:uppercase;letter-spacing:.5px;opacity:.8}
    .adv-card{border:none;border-radius:14px;box-shadow:0 2px 10px rgba(0,0,0,.05)}
    .rpt-table{border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.05)}
    .rpt-table thead{background:#1565c0;color:#fff}
    .rpt-table th{font-weight:600;font-size:13px;padding:10px 14px;border:none}
    .rpt-table td{font-size:13px;padding:10px 14px;vertical-align:middle}
    @media print{.period-bar,.pagetitle .btn,.breadcrumb{display:none!important}.main{padding:0!important}}
  </style>

  {{-- Period + Class Filter --}}
  <div class="period-bar">
    <span class="fw-semibold text-muted me-2"><i class="bi bi-funnel"></i></span>
    @foreach(['this_month'=>'This Month','last_month'=>'Last Month','6_months'=>'6 Months','this_year'=>'This Year'] as $k=>$v)
      <a href="{{ route('reports.attendance', ['period'=>$k,'class_id'=>$classFilter]) }}" class="btn-period {{ $periodKey===$k?'active':'' }}">{{ $v }}</a>
    @endforeach
    <select onchange="location.href='{{ route('reports.attendance') }}?period={{ $periodKey }}&class_id='+this.value" class="form-select form-select-sm ms-2" style="width:auto">
      <option value="">All Classes</option>
      @foreach($classrooms as $c)
        <option value="{{ $c->id }}" {{ $classFilter==$c->id?'selected':'' }}>{{ $c->class_name }} {{ $c->section_name }}</option>
      @endforeach
    </select>
    <span class="ms-auto text-muted small fw-semibold">{{ $periodLabel }}</span>
  </div>

  {{-- Summary Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-2"><div class="att-stat" style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);color:#1565c0"><div class="number">{{ number_format($totalRecords) }}</div><div class="label">Total Records</div></div></div>
    <div class="col-6 col-lg-2"><div class="att-stat" style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);color:#2e7d32"><div class="number">{{ number_format($present) }}</div><div class="label">Present</div></div></div>
    <div class="col-6 col-lg-2"><div class="att-stat" style="background:linear-gradient(135deg,#ffebee,#ffcdd2);color:#c62828"><div class="number">{{ number_format($absent) }}</div><div class="label">Absent</div></div></div>
    <div class="col-6 col-lg-2"><div class="att-stat" style="background:linear-gradient(135deg,#fff3e0,#ffe0b2);color:#e65100"><div class="number">{{ number_format($leave) }}</div><div class="label">Leave</div></div></div>
    <div class="col-6 col-lg-2"><div class="att-stat" style="background:linear-gradient(135deg,#f3e5f5,#e1bee7);color:#7b1fa2"><div class="number">{{ number_format($late) }}</div><div class="label">Late</div></div></div>
    <div class="col-6 col-lg-2"><div class="att-stat" style="background:linear-gradient(135deg,#e0f7fa,#b2ebf2);color:#00838f"><div class="number">{{ $rate }}%</div><div class="label">Attendance Rate</div></div></div>
  </div>

  {{-- Attendance Rate Gauge --}}
  <div class="card adv-card mb-4">
    <div class="card-body p-4">
      <h6 class="fw-bold mb-3"><i class="bi bi-speedometer2 text-primary"></i> Overall Attendance Rate</h6>
      <div class="progress" style="height:28px;border-radius:14px">
        <div class="progress-bar {{ $rate >= 85 ? 'bg-success' : ($rate >= 70 ? 'bg-warning' : 'bg-danger') }}" style="width:{{ $rate }}%;font-size:14px;font-weight:700">{{ $rate }}%</div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    {{-- Status Pie --}}
    <div class="col-lg-4">
      <div class="card adv-card">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart text-info"></i> Status Distribution</h6>
          <canvas id="attPie" height="220"></canvas>
        </div>
      </div>
    </div>
    {{-- Class-wise Table --}}
    <div class="col-lg-8">
      <div class="card adv-card">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-building text-primary"></i> Class-wise Breakdown</h6>
          <div class="table-responsive">
            <table class="table rpt-table table-hover mb-0">
              <thead><tr><th>Class</th><th>Total</th><th>Present</th><th>Absent</th><th>Rate</th></tr></thead>
              <tbody>
                @forelse($classWise as $cw)
                <tr>
                  <td class="fw-semibold">{{ $cw['class'] }}</td>
                  <td>{{ $cw['total'] }}</td>
                  <td class="text-success fw-bold">{{ $cw['present'] }}</td>
                  <td class="text-danger">{{ $cw['absent'] }}</td>
                  <td>
                    <div class="progress" style="height:18px;border-radius:9px;min-width:80px">
                      <div class="progress-bar {{ $cw['rate'] >= 85 ? 'bg-success' : ($cw['rate'] >= 70 ? 'bg-warning' : 'bg-danger') }}" style="width:{{ $cw['rate'] }}%">{{ $cw['rate'] }}%</div>
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
  </div>

  {{-- Student-wise Table --}}
  <div class="card adv-card mt-4">
    <div class="card-body p-4">
      <h6 class="fw-bold mb-3"><i class="bi bi-people text-primary"></i> Student-wise Attendance ({{ $studentWise->count() }} students)</h6>
      <div class="table-responsive" style="max-height:500px;overflow-y:auto">
        <table class="table rpt-table table-hover mb-0">
          <thead style="position:sticky;top:0"><tr><th>#</th><th>Student</th><th>Class</th><th>Total</th><th>Present</th><th>Absent</th><th>Rate</th><th>Status</th></tr></thead>
          <tbody>
            @forelse($studentWise as $i => $sw)
            <tr>
              <td>{{ $i+1 }}</td>
              <td class="fw-semibold">{{ $sw['student_name'] }}</td>
              <td>{{ $sw['class_name'] }}</td>
              <td>{{ $sw['total'] }}</td>
              <td class="text-success">{{ $sw['present'] }}</td>
              <td class="text-danger">{{ $sw['absent'] }}</td>
              <td>
                <div class="progress" style="height:16px;border-radius:8px;min-width:60px">
                  <div class="progress-bar {{ $sw['rate'] >= 85 ? 'bg-success' : ($sw['rate'] >= 70 ? 'bg-warning' : 'bg-danger') }}" style="width:{{ $sw['rate'] }}%">{{ $sw['rate'] }}%</div>
                </div>
              </td>
              <td>
                @if($sw['rate'] >= 85) <span class="badge bg-success">Good</span>
                @elseif($sw['rate'] >= 70) <span class="badge bg-warning text-dark">Moderate</span>
                @else <span class="badge bg-danger">Critical</span>
                @endif
              </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No attendance records found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>
@endsection

@section("scripts")
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
new Chart(document.getElementById('attPie'), {
  type:'doughnut',
  data:{
    labels:['Present','Absent','Leave','Late'],
    datasets:[{data:[{{ $present }},{{ $absent }},{{ $leave }},{{ $late }}],backgroundColor:['#2e7d32','#c62828','#e65100','#7b1fa2']}]
  },
  options:{responsive:true,plugins:{legend:{position:'bottom',labels:{font:{size:12}}}}}
});
</script>
@endsection
