@extends("admin.layout.master")
@section("content")
<main id="main" class="main">
  <div class="pagetitle d-flex justify-content-between align-items-center flex-wrap">
    <div>
      <h1><i class="bi bi-receipt-cutoff text-warning"></i> Fee Collection Report <span class="badge bg-primary fs-6">Advanced</span></h1>
      <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li><li class="breadcrumb-item active">Fee Collection (Advanced)</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-success btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i> Print</button>
      <a href="{{ route('reports.fees', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-download me-1"></i> Export CSV</a>
    </div>
  </div>

  <style>
    .period-bar{background:#fff;border-radius:12px;padding:12px 18px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,.06);display:flex;align-items:center;gap:8px;flex-wrap:wrap}
    .period-bar .btn-period{border:1px solid #dee2e6;background:#f8f9fa;border-radius:20px;padding:5px 16px;font-size:13px;color:#555;font-weight:500;transition:all .15s;text-decoration:none}
    .period-bar .btn-period:hover,.period-bar .btn-period.active{background:#e65100;color:#fff;border-color:#e65100}
    .fee-stat{border:none;border-radius:14px;text-align:center;padding:20px 14px}
    .fee-stat .number{font-size:24px;font-weight:800}
    .fee-stat .label{font-size:11px;text-transform:uppercase;letter-spacing:.5px;opacity:.8}
    .adv-card{border:none;border-radius:14px;box-shadow:0 2px 10px rgba(0,0,0,.05)}
    .rpt-table{border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.05)}
    .rpt-table thead{background:#e65100;color:#fff}
    .rpt-table th{font-weight:600;font-size:13px;padding:10px 14px;border:none}
    .rpt-table td{font-size:13px;padding:10px 14px;vertical-align:middle}
    @media print{.period-bar,.pagetitle .btn,.breadcrumb{display:none!important}.main{padding:0!important}}
  </style>

  {{-- Period Filter --}}
  <div class="period-bar">
    <span class="fw-semibold text-muted me-2"><i class="bi bi-funnel"></i></span>
    @foreach(['this_month'=>'This Month','last_month'=>'Last Month','6_months'=>'6 Months','this_year'=>'This Year','last_year'=>'Last Year'] as $k=>$v)
      <a href="{{ route('reports.fees', ['period'=>$k]) }}" class="btn-period {{ $periodKey===$k?'active':'' }}">{{ $v }}</a>
    @endforeach
    <form class="d-flex gap-2 ms-2" method="GET" action="{{ route('reports.fees') }}">
      <input type="hidden" name="period" value="custom">
      <input type="date" name="from" class="form-control form-control-sm" value="{{ $from->format('Y-m-d') }}" style="width:140px">
      <input type="date" name="to" class="form-control form-control-sm" value="{{ $to->format('Y-m-d') }}" style="width:140px">
      <button class="btn btn-sm btn-primary">Go</button>
    </form>
    <span class="ms-auto text-muted small fw-semibold">{{ $periodLabel }}</span>
  </div>

  {{-- Summary Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-2"><div class="fee-stat" style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);color:#1565c0"><div class="number">₨ {{ number_format($totalBilled) }}</div><div class="label">Total Billed</div></div></div>
    <div class="col-6 col-lg-2"><div class="fee-stat" style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);color:#2e7d32"><div class="number">₨ {{ number_format($totalReceived) }}</div><div class="label">Received</div></div></div>
    <div class="col-6 col-lg-2"><div class="fee-stat" style="background:linear-gradient(135deg,#fff3e0,#ffe0b2);color:#e65100"><div class="number">₨ {{ number_format($outstanding) }}</div><div class="label">Outstanding</div></div></div>
    <div class="col-6 col-lg-2"><div class="fee-stat" style="background:linear-gradient(135deg,#e8f5e9,#a5d6a7);color:#1b5e20"><div class="number">{{ $paidCount }}</div><div class="label">Paid</div></div></div>
    <div class="col-6 col-lg-2"><div class="fee-stat" style="background:linear-gradient(135deg,#ffebee,#ffcdd2);color:#c62828"><div class="number">{{ $unpaidCount }}</div><div class="label">Unpaid</div></div></div>
    <div class="col-6 col-lg-2"><div class="fee-stat" style="background:linear-gradient(135deg,#f3e5f5,#e1bee7);color:#7b1fa2"><div class="number">{{ $pendingCount }}</div><div class="label">Pending</div></div></div>
  </div>

  {{-- Collection Rate Progress --}}
  <div class="card adv-card mb-4">
    <div class="card-body p-4">
      <h6 class="fw-bold mb-3"><i class="bi bi-speedometer2 text-primary"></i> Collection Rate</h6>
      @php $colRate = $totalBilled > 0 ? round(($totalReceived/$totalBilled)*100,1) : 0; @endphp
      <div class="d-flex align-items-center gap-3">
        <div class="progress flex-grow-1" style="height:28px;border-radius:14px">
          <div class="progress-bar bg-success" style="width:{{ $colRate }}%;font-size:14px;font-weight:700">{{ $colRate }}% Collected</div>
        </div>
        <span class="fw-bold text-muted" style="min-width:100px">₨ {{ number_format($totalReceived) }} / {{ number_format($totalBilled) }}</span>
      </div>
    </div>
  </div>

  <div class="row g-4">
    {{-- Monthly Breakdown Table --}}
    <div class="col-lg-5">
      <div class="card adv-card">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-calendar3 text-primary"></i> Monthly Breakdown</h6>
          <div class="table-responsive">
            <table class="table rpt-table table-hover mb-0">
              <thead><tr><th>Month</th><th>Billed</th><th>Received</th><th>Rate</th><th>Paid</th><th>Unpaid</th></tr></thead>
              <tbody>
                @forelse($monthlyFee as $m)
                @php $mRate = $m['billed'] > 0 ? round(($m['received']/$m['billed'])*100,1) : 0; @endphp
                <tr>
                  <td class="fw-semibold">{{ $m['month'] }}</td>
                  <td>₨ {{ number_format($m['billed']) }}</td>
                  <td class="text-success fw-bold">₨ {{ number_format($m['received']) }}</td>
                  <td>
                    <div class="progress" style="height:16px;border-radius:8px;min-width:60px">
                      <div class="progress-bar {{ $mRate >= 80 ? 'bg-success' : ($mRate >= 50 ? 'bg-warning' : 'bg-danger') }}" style="width:{{ $mRate }}%">{{ $mRate }}%</div>
                    </div>
                  </td>
                  <td><span class="badge bg-success">{{ $m['paid'] }}</span></td>
                  <td><span class="badge bg-danger">{{ $m['unpaid'] }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">No data for this period.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- Collection Chart --}}
    <div class="col-lg-7">
      <div class="card adv-card">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-graph-up text-success"></i> Billed vs Received Trend</h6>
          <canvas id="feeChart" height="260"></canvas>
        </div>
      </div>
    </div>
  </div>

  {{-- Status Pie + Class-wise --}}
  <div class="row g-4 mt-2">
    <div class="col-lg-4">
      <div class="card adv-card">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart text-info"></i> Status Distribution</h6>
          <canvas id="statusPie" height="220"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="card adv-card">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-building text-primary"></i> Class-wise Fee Summary</h6>
          <div class="table-responsive" style="max-height:360px;overflow-y:auto">
            <table class="table rpt-table table-hover mb-0">
              <thead style="position:sticky;top:0"><tr><th>Class</th><th>Students</th><th>Billed</th><th>Received</th><th>Outstanding</th><th>Rate</th></tr></thead>
              <tbody>
                @php
                  $classSummary = $feeRecords->groupBy(fn($f)=>$f->student->classroom->class_name ?? 'Unknown')->map(function($g, $cn){
                    $b=$g->sum('total_fee'); $r=$g->sum('received_payment_fee');
                    return['class'=>$cn,'students'=>$g->pluck('student_id')->unique()->count(),'billed'=>$b,'received'=>$r,'outstanding'=>$b-$r,'rate'=>$b>0?round(($r/$b)*100,1):0];
                  })->sortBy('class')->values();
                @endphp
                @forelse($classSummary as $cs)
                <tr>
                  <td class="fw-semibold">{{ $cs['class'] }}</td>
                  <td>{{ $cs['students'] }}</td>
                  <td>₨ {{ number_format($cs['billed']) }}</td>
                  <td class="text-success fw-bold">₨ {{ number_format($cs['received']) }}</td>
                  <td class="text-danger">₨ {{ number_format($cs['outstanding']) }}</td>
                  <td>
                    <div class="progress" style="height:16px;border-radius:8px;min-width:60px">
                      <div class="progress-bar {{ $cs['rate'] >= 80 ? 'bg-success' : ($cs['rate'] >= 50 ? 'bg-warning' : 'bg-danger') }}" style="width:{{ $cs['rate'] }}%">{{ $cs['rate'] }}%</div>
                    </div>
                  </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">No data</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Detailed Fee Records --}}
  <div class="card adv-card mt-4">
    <div class="card-body p-4">
      <h6 class="fw-bold mb-3"><i class="bi bi-list-ul text-primary"></i> All Fee Records ({{ $feeRecords->count() }})</h6>
      <div class="table-responsive" style="max-height:500px;overflow-y:auto">
        <table class="table rpt-table table-hover mb-0">
          <thead style="position:sticky;top:0"><tr><th>#</th><th>Student</th><th>Father</th><th>Class</th><th>Month</th><th>Total</th><th>Received</th><th>Balance</th><th>Status</th><th>Voucher#</th></tr></thead>
          <tbody>
            @forelse($feeRecords as $i => $f)
            <tr>
              <td>{{ $i+1 }}</td>
              <td><a href="{{ route('student.show', $f->student->id ?? 0) }}" class="fw-semibold text-decoration-none">{{ $f->student->student_name ?? '—' }}</a></td>
              <td>{{ $f->student->father_name ?? '—' }}</td>
              <td>{{ $f->student->classroom->class_name ?? '—' }}</td>
              <td>{{ $f->fee_month }}</td>
              <td>₨ {{ number_format($f->total_fee) }}</td>
              <td class="text-success fw-bold">₨ {{ number_format($f->received_payment_fee) }}</td>
              <td class="text-danger">₨ {{ number_format($f->total_fee - $f->received_payment_fee) }}</td>
              <td>
                @if($f->status === 'paid') <span class="badge bg-success">Paid</span>
                @elseif($f->status === 'unpaid') <span class="badge bg-danger">Unpaid</span>
                @else <span class="badge bg-warning text-dark">{{ ucfirst($f->status ?? 'pending') }}</span>
                @endif
              </td>
              <td><code>{{ $f->voucher_no ?? '—' }}</code></td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center text-muted py-4">No fee records found.</td></tr>
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
// Billed vs Received Bar Chart
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

// Status Doughnut
new Chart(document.getElementById('statusPie'), {
  type:'doughnut',
  data:{
    labels:['Paid','Unpaid','Pending'],
    datasets:[{data:[{{ $paidCount }},{{ $unpaidCount }},{{ $pendingCount }}],backgroundColor:['#2e7d32','#c62828','#f57f17']}]
  },
  options:{responsive:true,plugins:{legend:{position:'bottom',labels:{font:{size:12}}}}}
});
</script>
@endsection
