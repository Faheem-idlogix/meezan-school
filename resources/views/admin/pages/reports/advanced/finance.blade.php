@extends("admin.layout.master")
@section("content")
<main id="main" class="main">
  <div class="pagetitle d-flex justify-content-between align-items-center flex-wrap">
    <div>
      <h1><i class="bi bi-cash-coin text-success"></i> Finance Report <span class="badge bg-primary fs-6">Advanced</span></h1>
      <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li><li class="breadcrumb-item active">Finance (Advanced)</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-success btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i> Print</button>
      <a href="{{ route('reports.finance', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-download me-1"></i> Export CSV</a>
    </div>
  </div>

  <style>
    .period-bar{background:#fff;border-radius:12px;padding:12px 18px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,.06);display:flex;align-items:center;gap:8px;flex-wrap:wrap}
    .period-bar .btn-period{border:1px solid #dee2e6;background:#f8f9fa;border-radius:20px;padding:5px 16px;font-size:13px;color:#555;font-weight:500;transition:all .15s;text-decoration:none}
    .period-bar .btn-period:hover,.period-bar .btn-period.active{background:#4154f1;color:#fff;border-color:#4154f1}
    .fin-card{border:none;border-radius:14px;text-align:center;padding:22px 16px}
    .fin-card h6{font-size:12px;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px}
    .fin-card .amount{font-size:24px;font-weight:800}
    .fc-income{background:linear-gradient(135deg,#e8f5e9,#c8e6c9);color:#2e7d32}
    .fc-expense{background:linear-gradient(135deg,#ffebee,#ffcdd2);color:#c62828}
    .fc-profit{background:linear-gradient(135deg,#e3f2fd,#bbdefb);color:#1565c0}
    .fc-count{background:linear-gradient(135deg,#f3e5f5,#e1bee7);color:#7b1fa2}
    .fc-ratio{background:linear-gradient(135deg,#fff8e1,#ffecb3);color:#f57f17}
    .fc-avg{background:linear-gradient(135deg,#e0f7fa,#b2ebf2);color:#00838f}
    .adv-card{border:none;border-radius:14px;box-shadow:0 2px 10px rgba(0,0,0,.05)}
    .rpt-table{border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.05)}
    .rpt-table thead{background:#4154f1;color:#fff}
    .rpt-table th{font-weight:600;font-size:13px;padding:10px 14px;border:none}
    .rpt-table td{font-size:13px;padding:10px 14px;vertical-align:middle}
    @media print{.period-bar,.pagetitle .btn,.breadcrumb{display:none!important}.main{padding:0!important}}
  </style>

  {{-- Period Filter --}}
  <div class="period-bar">
    <span class="fw-semibold text-muted me-2"><i class="bi bi-funnel"></i></span>
    @foreach(['this_month'=>'This Month','last_month'=>'Last Month','6_months'=>'6 Months','this_year'=>'This Year','last_year'=>'Last Year'] as $k=>$v)
      <a href="{{ route('reports.finance', ['period'=>$k]) }}" class="btn-period {{ $periodKey===$k?'active':'' }}">{{ $v }}</a>
    @endforeach
    <form class="d-flex gap-2 ms-2" method="GET" action="{{ route('reports.finance') }}">
      <input type="hidden" name="period" value="custom">
      <input type="date" name="from" class="form-control form-control-sm" value="{{ $from->format('Y-m-d') }}" style="width:140px">
      <input type="date" name="to" class="form-control form-control-sm" value="{{ $to->format('Y-m-d') }}" style="width:140px">
      <button class="btn btn-sm btn-primary">Go</button>
    </form>
    <span class="ms-auto text-muted small fw-semibold">{{ $periodLabel }}</span>
  </div>

  {{-- Summary Cards (6 cards) --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-2"><div class="fin-card fc-income"><h6>Total Income</h6><div class="amount">₨ {{ number_format($totalIncome) }}</div></div></div>
    <div class="col-6 col-lg-2"><div class="fin-card fc-expense"><h6>Total Expense</h6><div class="amount">₨ {{ number_format($totalExpense) }}</div></div></div>
    <div class="col-6 col-lg-2"><div class="fin-card fc-profit"><h6>Net Profit/Loss</h6><div class="amount">₨ {{ number_format($profitLoss) }}</div></div></div>
    <div class="col-6 col-lg-2"><div class="fin-card fc-count"><h6>Vouchers</h6><div class="amount">{{ $vouchers->count() }}</div></div></div>
    <div class="col-6 col-lg-2"><div class="fin-card fc-ratio"><h6>Expense Ratio</h6><div class="amount">{{ $totalIncome > 0 ? round(($totalExpense/$totalIncome)*100,1) : 0 }}%</div></div></div>
    <div class="col-6 col-lg-2"><div class="fin-card fc-avg"><h6>Avg Voucher</h6><div class="amount">₨ {{ $vouchers->count() > 0 ? number_format($vouchers->sum('amount')/$vouchers->count()) : 0 }}</div></div></div>
  </div>

  <div class="row g-4">
    {{-- Monthly Trend Chart --}}
    <div class="col-lg-8">
      <div class="card adv-card">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-graph-up text-primary"></i> Monthly Income vs Expense Trend</h6>
          <canvas id="finChart" height="280"></canvas>
        </div>
      </div>
    </div>

    {{-- Category Breakdown --}}
    <div class="col-lg-4">
      <div class="card adv-card">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart text-info"></i> Expense by Category</h6>
          <canvas id="catPieChart" height="220"></canvas>
          <div class="mt-3">
            @forelse($byCategory as $c)
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
              <span class="fw-semibold small">{{ $c['category'] }}</span>
              <div>
                <span class="badge bg-success bg-opacity-10 text-success me-1">+₨{{ number_format($c['income']) }}</span>
                <span class="badge bg-danger bg-opacity-10 text-danger">-₨{{ number_format($c['expense']) }}</span>
              </div>
            </div>
            @empty
            <p class="text-muted small text-center">No categories found</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Monthly Breakdown Table --}}
  <div class="card adv-card mt-4">
    <div class="card-body p-4">
      <h6 class="fw-bold mb-3"><i class="bi bi-table text-primary"></i> Monthly Breakdown</h6>
      <div class="table-responsive">
        <table class="table rpt-table table-hover mb-0">
          <thead><tr><th>Month</th><th>Income</th><th>Expense</th><th>Net</th><th>Margin</th></tr></thead>
          <tbody>
            @foreach($monthly as $m)
            @php $net = $m['income'] - $m['expense']; @endphp
            <tr>
              <td class="fw-semibold">{{ $m['label'] }}</td>
              <td class="text-success">₨ {{ number_format($m['income']) }}</td>
              <td class="text-danger">₨ {{ number_format($m['expense']) }}</td>
              <td class="{{ $net >= 0 ? 'text-success' : 'text-danger' }} fw-bold">₨ {{ number_format($net) }}</td>
              <td>
                @if($m['income'] > 0)
                  <div class="progress" style="height:18px;border-radius:10px">
                    <div class="progress-bar {{ $net >= 0 ? 'bg-success' : 'bg-danger' }}" style="width:{{ min(abs($net)/$m['income']*100, 100) }}%">
                      {{ round($net/$m['income']*100,1) }}%
                    </div>
                  </div>
                @else — @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Voucher Detail Table --}}
  <div class="card adv-card mt-4">
    <div class="card-body p-4">
      <h6 class="fw-bold mb-3"><i class="bi bi-list-ul text-primary"></i> All Vouchers ({{ $vouchers->count() }})</h6>
      <div class="table-responsive" style="max-height:500px;overflow-y:auto">
        <table class="table rpt-table table-hover mb-0">
          <thead style="position:sticky;top:0"><tr><th>#</th><th>Date</th><th>Type</th><th>Category</th><th>Description</th><th>Amount</th></tr></thead>
          <tbody>
            @forelse($vouchers as $i => $v)
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $v->voucher_date ?? ($v->created_at ? $v->created_at->format('d M Y') : '—') }}</td>
              <td><span class="badge {{ $v->type === 'income' ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($v->type) }}</span></td>
              <td>{{ $v->category ?: '—' }}</td>
              <td style="max-width:250px">{{ \Illuminate\Support\Str::limit($v->description ?? '—', 60) }}</td>
              <td class="fw-bold">₨ {{ number_format($v->amount) }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No vouchers found for this period.</td></tr>
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
// Monthly Trend Chart
new Chart(document.getElementById('finChart'), {
  type:'bar',
  data:{
    labels:{!! json_encode($monthly->pluck('label')) !!},
    datasets:[
      {label:'Income',data:{!! json_encode($monthly->pluck('income')) !!},backgroundColor:'rgba(46,125,50,.7)',borderRadius:6},
      {label:'Expense',data:{!! json_encode($monthly->pluck('expense')) !!},backgroundColor:'rgba(198,40,40,.6)',borderRadius:6},
      {type:'line',label:'Net',data:{!! json_encode($monthly->map(fn($m)=>$m['income']-$m['expense'])) !!},borderColor:'#1565c0',borderWidth:2,pointRadius:4,fill:false}
    ]
  },
  options:{responsive:true,plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true,ticks:{callback:v=>'₨'+v.toLocaleString()}}}}
});

// Category Pie Chart
@php $expCats = $byCategory->filter(fn($c)=>$c['expense']>0); @endphp
new Chart(document.getElementById('catPieChart'), {
  type:'doughnut',
  data:{
    labels:{!! json_encode($expCats->pluck('category')) !!},
    datasets:[{data:{!! json_encode($expCats->pluck('expense')) !!},backgroundColor:['#e74c3c','#e67e22','#f1c40f','#2ecc71','#3498db','#9b59b6','#1abc9c','#34495e']}]
  },
  options:{responsive:true,plugins:{legend:{position:'bottom',labels:{font:{size:11}}}}}
});
</script>
@endsection
