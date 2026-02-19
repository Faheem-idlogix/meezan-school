@extends("admin.layout.master")
@section("content")
<main id="main" class="main">
  <div class="pagetitle">
    <h1><i class="bi bi-cash-coin text-success"></i> Finance Report</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li><li class="breadcrumb-item active">Finance</li></ol></nav>
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
    .rpt-table{border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.05)}
    .rpt-table thead{background:#4154f1;color:#fff}
    .rpt-table th{font-weight:600;font-size:13px;padding:10px 14px;border:none}
    .rpt-table td{font-size:13px;padding:10px 14px;vertical-align:middle}
  </style>

  {{-- Period Filter --}}
  <div class="period-bar">
    <span class="fw-semibold text-muted me-2"><i class="bi bi-funnel"></i></span>
    @foreach(['this_month'=>'This Month','last_month'=>'Last Month','6_months'=>'6 Months','this_year'=>'This Year','last_year'=>'Last Year'] as $k=>$v)
      <a href="{{ route('reports.finance', ['period'=>$k]) }}" class="btn-period {{ $periodKey===$k?'active':'' }}">{{ $v }}</a>
    @endforeach
    <span class="ms-auto text-muted small fw-semibold">{{ $periodLabel }}</span>
  </div>

  {{-- Summary Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="fin-card fc-income"><h6>Total Income</h6><div class="amount">₨ {{ number_format($totalIncome) }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="fin-card fc-expense"><h6>Total Expense</h6><div class="amount">₨ {{ number_format($totalExpense) }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="fin-card fc-profit"><h6>Profit / Loss</h6><div class="amount">₨ {{ number_format($profitLoss) }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="fin-card fc-count"><h6>Vouchers</h6><div class="amount">{{ $vouchers->count() }}</div></div></div>
  </div>

  <div class="row g-4">
    {{-- Monthly Chart --}}
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm" style="border-radius:14px">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-graph-up text-primary"></i> Monthly Trend</h6>
          <canvas id="finChart" height="260"></canvas>
        </div>
      </div>
    </div>

    {{-- Category Breakdown --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm" style="border-radius:14px">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart text-info"></i> By Category</h6>
          @forelse($byCategory as $c)
          <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
            <span class="small fw-semibold">{{ $c['category'] }}</span>
            <div class="text-end">
              <span class="badge bg-success bg-opacity-10 text-success">+{{ number_format($c['income']) }}</span>
              <span class="badge bg-danger bg-opacity-10 text-danger">-{{ number_format($c['expense']) }}</span>
            </div>
          </div>
          @empty
          <p class="text-muted small">No category data found.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  {{-- Voucher Table --}}
  <div class="card border-0 shadow-sm mt-4" style="border-radius:14px">
    <div class="card-body p-4">
      <h6 class="fw-bold mb-3"><i class="bi bi-list-ul text-primary"></i> All Vouchers ({{ $vouchers->count() }})</h6>
      <div class="table-responsive">
        <table class="table rpt-table table-hover mb-0">
          <thead>
            <tr><th>#</th><th>Date</th><th>Type</th><th>Category</th><th>Description</th><th>Mode</th><th class="text-end">Amount</th></tr>
          </thead>
          <tbody>
            @forelse($vouchers as $i => $v)
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $v->voucher_date ? $v->voucher_date->format('d M Y') : $v->created_at->format('d M Y') }}</td>
              <td><span class="badge {{ $v->type==='income'?'bg-success':'bg-danger' }}">{{ ucfirst($v->type) }}</span></td>
              <td>{{ $v->category ?? '—' }}</td>
              <td>{{ Str::limit($v->description, 50) }}</td>
              <td><span class="badge bg-light text-dark">{{ ucfirst($v->payment_mode ?? 'cash') }}</span></td>
              <td class="text-end fw-bold {{ $v->type==='income'?'text-success':'text-danger' }}">₨ {{ number_format($v->amount) }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No vouchers in this period.</td></tr>
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
new Chart(document.getElementById('finChart'), {
  type:'bar',
  data:{
    labels: {!! json_encode($monthly->pluck('label')) !!},
    datasets:[
      {label:'Income',data:{!! json_encode($monthly->pluck('income')) !!},backgroundColor:'rgba(46,125,50,.7)',borderRadius:6},
      {label:'Expense',data:{!! json_encode($monthly->pluck('expense')) !!},backgroundColor:'rgba(198,40,40,.7)',borderRadius:6}
    ]
  },
  options:{responsive:true,plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true,ticks:{callback:v=>'₨'+v.toLocaleString()}}}}
});
</script>
@endsection
