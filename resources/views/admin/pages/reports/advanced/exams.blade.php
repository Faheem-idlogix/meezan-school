@extends("admin.layout.master")
@section("content")
<main id="main" class="main">
  <div class="pagetitle d-flex justify-content-between align-items-center flex-wrap">
    <div>
      <h1><i class="bi bi-journal-bookmark text-info"></i> Exam Report <span class="badge bg-primary fs-6">Advanced</span></h1>
      <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li><li class="breadcrumb-item active">Exams (Advanced)</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-success btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i> Print</button>
    </div>
  </div>

  <style>
    .ex-stat{border:none;border-radius:14px;text-align:center;padding:20px 14px}
    .ex-stat .number{font-size:24px;font-weight:800}
    .ex-stat .label{font-size:11px;text-transform:uppercase;letter-spacing:.5px;opacity:.8}
    .adv-card{border:none;border-radius:14px;box-shadow:0 2px 10px rgba(0,0,0,.05)}
    .rpt-table{border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.05)}
    .rpt-table thead{background:#00838f;color:#fff}
    .rpt-table th{font-weight:600;font-size:13px;padding:10px 14px;border:none}
    .rpt-table td{font-size:13px;padding:10px 14px;vertical-align:middle}
    .rank-badge{width:28px;height:28px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff}
    .rank-1{background:linear-gradient(135deg,#ffd700,#ffb300)}.rank-2{background:linear-gradient(135deg,#bdbdbd,#9e9e9e)}.rank-3{background:linear-gradient(135deg,#cd7f32,#a0522d)}.rank-other{background:#546e7a}
    @media print{.pagetitle .btn,.breadcrumb{display:none!important}.main{padding:0!important}}
  </style>

  {{-- Exam Filter --}}
  <div class="card adv-card mb-3">
    <div class="card-body p-3 d-flex align-items-center gap-3 flex-wrap">
      <select onchange="location.href='{{ route('reports.exams') }}?exam_id='+this.value" class="form-select form-select-sm" style="width:auto">
        <option value="">All Exams</option>
        @foreach($exams as $e)
          <option value="{{ $e->id }}" {{ $examFilter==$e->id?'selected':'' }}>{{ $e->exam_name }}</option>
        @endforeach
      </select>
    </div>
  </div>

  {{-- Summary Cards --}}
  @php
    $totalExams = $exams->count();
    $totalStudents = $results->pluck('student_id')->unique()->count();
    $totalSubjects = $results->pluck('subject_id')->unique()->count();
    $avgPct = $results->sum('total_marks') > 0 ? round(($results->sum('obtained_marks')/$results->sum('total_marks'))*100,1) : 0;
  @endphp
  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="ex-stat" style="background:linear-gradient(135deg,#e0f7fa,#b2ebf2);color:#00838f"><div class="number">{{ $totalExams }}</div><div class="label">Total Exams</div></div></div>
    <div class="col-6 col-lg-3"><div class="ex-stat" style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);color:#1565c0"><div class="number">{{ $totalStudents }}</div><div class="label">Students</div></div></div>
    <div class="col-6 col-lg-3"><div class="ex-stat" style="background:linear-gradient(135deg,#f3e5f5,#e1bee7);color:#7b1fa2"><div class="number">{{ $totalSubjects }}</div><div class="label">Subjects</div></div></div>
    <div class="col-6 col-lg-3"><div class="ex-stat" style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);color:#2e7d32"><div class="number">{{ $avgPct }}%</div><div class="label">Average Score</div></div></div>
  </div>

  <div class="row g-4">
    {{-- Exam Summary Table --}}
    <div class="col-lg-7">
      <div class="card adv-card">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-table text-primary"></i> Exam Summary</h6>
          <div class="table-responsive">
            <table class="table rpt-table table-hover mb-0">
              <thead><tr><th>Exam</th><th>Students</th><th>Subjects</th><th>Total</th><th>Obtained</th><th>Avg %</th></tr></thead>
              <tbody>
                @forelse($examSummary as $es)
                <tr>
                  <td class="fw-semibold">{{ $es['exam'] }}</td>
                  <td>{{ $es['students'] }}</td>
                  <td>{{ $es['subjects'] }}</td>
                  <td>{{ number_format($es['total']) }}</td>
                  <td class="text-success fw-bold">{{ number_format($es['obtained']) }}</td>
                  <td>
                    <div class="progress" style="height:18px;border-radius:9px;min-width:70px">
                      <div class="progress-bar {{ $es['avg_pct'] >= 70 ? 'bg-success' : ($es['avg_pct'] >= 50 ? 'bg-warning' : 'bg-danger') }}" style="width:{{ $es['avg_pct'] }}%">{{ $es['avg_pct'] }}%</div>
                    </div>
                  </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">No exam data</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- Performance Pie --}}
    <div class="col-lg-5">
      <div class="card adv-card">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart text-info"></i> Score Distribution</h6>
          <canvas id="scorePie" height="220"></canvas>
        </div>
      </div>
    </div>
  </div>

  {{-- Top 10 Performers --}}
  <div class="card adv-card mt-4">
    <div class="card-body p-4">
      <h6 class="fw-bold mb-3"><i class="bi bi-trophy text-warning"></i> Top 10 Performers</h6>
      <div class="table-responsive">
        <table class="table rpt-table table-hover mb-0">
          <thead><tr><th>Rank</th><th>Student</th><th>Total Marks</th><th>Obtained</th><th>Percentage</th></tr></thead>
          <tbody>
            @forelse($topPerformers as $i => $tp)
            <tr>
              <td>
                @if($i < 3) <span class="rank-badge rank-{{ $i+1 }}">{{ $i+1 }}</span>
                @else <span class="rank-badge rank-other">{{ $i+1 }}</span>
                @endif
              </td>
              <td class="fw-semibold">{{ $tp['student_name'] }}</td>
              <td>{{ number_format($tp['total']) }}</td>
              <td class="text-success fw-bold">{{ number_format($tp['obtained']) }}</td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="progress flex-grow-1" style="height:18px;border-radius:9px">
                    <div class="progress-bar {{ $tp['pct'] >= 80 ? 'bg-success' : ($tp['pct'] >= 60 ? 'bg-warning' : 'bg-danger') }}" style="width:{{ $tp['pct'] }}%">{{ $tp['pct'] }}%</div>
                  </div>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No results</td></tr>
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
@php
  $a = $results->filter(fn($r)=>$r->total_marks>0?($r->obtained_marks/$r->total_marks*100)>=80:false)->count();
  $b = $results->filter(fn($r)=>$r->total_marks>0&&($r->obtained_marks/$r->total_marks*100)>=60&&($r->obtained_marks/$r->total_marks*100)<80)->count();
  $c = $results->filter(fn($r)=>$r->total_marks>0&&($r->obtained_marks/$r->total_marks*100)>=40&&($r->obtained_marks/$r->total_marks*100)<60)->count();
  $d = $results->filter(fn($r)=>$r->total_marks>0&&($r->obtained_marks/$r->total_marks*100)<40)->count();
@endphp
new Chart(document.getElementById('scorePie'), {
  type:'doughnut',
  data:{
    labels:['A (80%+)','B (60-79%)','C (40-59%)','F (<40%)'],
    datasets:[{data:[{{ $a }},{{ $b }},{{ $c }},{{ $d }}],backgroundColor:['#2e7d32','#f57f17','#e65100','#c62828']}]
  },
  options:{responsive:true,plugins:{legend:{position:'bottom',labels:{font:{size:12}}}}}
});
</script>
@endsection
