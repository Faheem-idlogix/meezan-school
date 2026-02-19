@extends("admin.layout.master")
@section("content")
<main id="main" class="main">
  <div class="pagetitle">
    <h1><i class="bi bi-receipt-cutoff text-warning"></i> Fee Collection Report</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li><li class="breadcrumb-item active">Fee Collection</li></ol></nav>
  </div>

  <style>
    .period-bar{background:#fff;border-radius:12px;padding:12px 18px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,.06);display:flex;align-items:center;gap:8px;flex-wrap:wrap}
    .period-bar .btn-period{border:1px solid #dee2e6;background:#f8f9fa;border-radius:20px;padding:5px 16px;font-size:13px;color:#555;font-weight:500;transition:all .15s;text-decoration:none}
    .period-bar .btn-period:hover,.period-bar .btn-period.active{background:#e65100;color:#fff;border-color:#e65100}
    .fee-stat{border:none;border-radius:14px;text-align:center;padding:20px 14px}
    .fee-stat .number{font-size:24px;font-weight:800}
    .fee-stat .label{font-size:11px;text-transform:uppercase;letter-spacing:.5px;opacity:.8}
    .rpt-table{border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.05)}
    .rpt-table thead{background:#e65100;color:#fff}
    .rpt-table th{font-weight:600;font-size:13px;padding:10px 14px;border:none}
    .rpt-table td{font-size:13px;padding:10px 14px;vertical-align:middle}
  </style>

  {{-- Period Filter --}}
  <div class="period-bar">
    <span class="fw-semibold text-muted me-2"><i class="bi bi-funnel"></i></span>
    @foreach(['this_month'=>'This Month','last_month'=>'Last Month','6_months'=>'6 Months','this_year'=>'This Year','last_year'=>'Last Year'] as $k=>$v)
      <a href="{{ route('reports.fees', ['period'=>$k]) }}" class="btn-period {{ $periodKey===$k?'active':'' }}">{{ $v }}</a>
    @endforeach
    <span class="ms-auto text-muted small fw-semibold">{{ $periodLabel }}</span>
  </div>

  {{-- Summary --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-2"><div class="fee-stat" style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);color:#1565c0"><div class="number">₨ {{ number_format($totalBilled) }}</div><div class="label">Total Billed</div></div></div>
    <div class="col-6 col-lg-2"><div class="fee-stat" style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);color:#2e7d32"><div class="number">₨ {{ number_format($totalReceived) }}</div><div class="label">Received</div></div></div>
    <div class="col-6 col-lg-2"><div class="fee-stat" style="background:linear-gradient(135deg,#fff3e0,#ffe0b2);color:#e65100"><div class="number">₨ {{ number_format($outstanding) }}</div><div class="label">Outstanding</div></div></div>
    <div class="col-6 col-lg-2"><div class="fee-stat" style="background:linear-gradient(135deg,#e8f5e9,#a5d6a7);color:#1b5e20"><div class="number">{{ $paidCount }}</div><div class="label">Paid</div></div></div>
    <div class="col-6 col-lg-2"><div class="fee-stat" style="background:linear-gradient(135deg,#ffebee,#ffcdd2);color:#c62828"><div class="number">{{ $unpaidCount }}</div><div class="label">Unpaid</div></div></div>
    <div class="col-6 col-lg-2"><div class="fee-stat" style="background:linear-gradient(135deg,#f3e5f5,#e1bee7);color:#7b1fa2"><div class="number">{{ $pendingCount }}</div><div class="label">Pending</div></div></div>
  </div>

  <div class="row g-4">
    {{-- Monthly Breakdown --}}
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm" style="border-radius:14px">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-calendar3 text-primary"></i> Monthly Breakdown</h6>
          <div class="table-responsive">
            <table class="table rpt-table table-hover mb-0">
              <thead><tr><th>Month</th><th>Billed</th><th>Received</th><th>Paid</th><th>Unpaid</th></tr></thead>
              <tbody>
                @forelse($monthlyFee as $m)
                <tr>
                  <td class="fw-semibold">{{ $m['month'] }}</td>
                  <td>₨ {{ number_format($m['billed']) }}</td>
                  <td class="text-success fw-bold">₨ {{ number_format($m['received']) }}</td>
                  <td><span class="badge bg-success">{{ $m['paid'] }}</span></td>
                  <td><span class="badge bg-danger">{{ $m['unpaid'] }}</span></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">No data for this period.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- Chart --}}
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm" style="border-radius:14px">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-graph-up text-success"></i> Collection Chart</h6>
          <canvas id="feeChart" height="260"></canvas>
        </div>
      </div>
    </div>
  </div>

  {{-- Detailed Records --}}
  <div class="card border-0 shadow-sm mt-4" style="border-radius:14px">
    <div class="card-body p-4">
      <h6 class="fw-bold mb-3"><i class="bi bi-list-ul text-primary"></i> Fee Records ({{ $feeRecords->count() }})</h6>
      <div class="table-responsive" style="max-height:500px;overflow-y:auto">
        <table class="table rpt-table table-hover mb-0">
          <thead style="position:sticky;top:0"><tr><th>#</th><th>Student</th><th>Class</th><th>Month</th><th>Total</th><th>Received</th><th>Balance</th><th>Status</th></tr></thead>
          <tbody>
            @forelse($feeRecords as $i => $f)
            <tr>
              <td>{{ $i+1 }}</td>
              <td><a href="{{ route('student.show', $f->student->id ?? 0) }}" class="fw-semibold text-decoration-none">{{ $f->student->student_name ?? '—' }}</a></td>
              <td>{{ $f->student->classroom->class_name ?? '—' }}</td>
              <td>{{ $f->fee_month }}</td>
              <td>₨ {{ number_format($f->total_fee) }}</td>
              <td class="text-success fw-bold">₨ {{ number_format($f->received_payment_fee) }}</td>
              <td class="text-danger">₨ {{ number_format($f->total_fee - $f->received_payment_fee) }}</td>
              <td>
                @if($f->status === 'paid')
                  <span class="badge bg-success">Paid</span>
                @elseif($f->status === 'unpaid')
                  <span class="badge bg-danger">Unpaid</span>
                @else
                  <span class="badge bg-warning text-dark">{{ ucfirst($f->status ?? 'pending') }}</span>
                @endif
              </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No fee records found.</td></tr>
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
new Chart(document.getElementById('feeChart'), {
  type:'bar',
  data:{
    labels:{!! json_encode($monthlyFee->pluck('month')) !!},
    datasets:[
      {label:'Billed',data:{!! json_encode($monthlyFee->pluck('billed')) !!},backgroundColor:'rgba(21,101,192,.6)',borderRadius:6},
      {label:'Received',data:{!! json_encode($monthlyFee->pluck('received')) !!},backgroundColor:'rgba(46,125,50,.7)',borderRadius:6}
    ]
  },
  options:{responsive:true,plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true,ticks:{callback:v=>'₨'+v.toLocaleString()}}}}
});
</script>
@endsection
