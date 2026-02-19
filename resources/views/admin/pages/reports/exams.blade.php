@extends("admin.layout.master")
@section("content")
<main id="main" class="main">
  <div class="pagetitle">
    <h1><i class="bi bi-journal-bookmark-fill text-info"></i> Exam Report</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li><li class="breadcrumb-item active">Exams</li></ol></nav>
  </div>

  <style>
    .filter-bar{background:#fff;border-radius:12px;padding:12px 18px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,.06);display:flex;align-items:center;gap:10px;flex-wrap:wrap}
    .exam-stat{border:none;border-radius:14px;text-align:center;padding:20px 14px}
    .exam-stat .number{font-size:28px;font-weight:800}
    .exam-stat .label{font-size:11px;text-transform:uppercase;letter-spacing:.5px;opacity:.8}
    .rpt-table{border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.05)}
    .rpt-table thead{background:#00838f;color:#fff}
    .rpt-table th{font-weight:600;font-size:13px;padding:10px 14px;border:none}
    .rpt-table td{font-size:13px;padding:10px 14px;vertical-align:middle}
    .rank-badge{width:28px;height:28px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:12px;color:#fff}
    .rank-1{background:linear-gradient(135deg,#ffd700,#ffb300)}.rank-2{background:linear-gradient(135deg,#c0c0c0,#9e9e9e)}.rank-3{background:linear-gradient(135deg,#cd7f32,#a0522d)}.rank-other{background:#dee2e6;color:#555}
  </style>

  {{-- Filter --}}
  <div class="filter-bar">
    <i class="bi bi-funnel text-muted"></i>
    <select onchange="location.href='{{ route('reports.exams') }}?exam_id='+this.value" class="form-select form-select-sm" style="width:250px;border-radius:20px">
      <option value="">All Exams</option>
      @foreach($exams as $e)
        <option value="{{ $e->id }}" {{ $examFilter==$e->id?'selected':'' }}>{{ $e->exam_name }}</option>
      @endforeach
    </select>
    <span class="ms-auto text-muted small">{{ $results->count() }} results found</span>
  </div>

  {{-- Summary --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="exam-stat" style="background:linear-gradient(135deg,#e0f7fa,#b2ebf2);color:#00838f"><div class="number">{{ $exams->count() }}</div><div class="label">Total Exams</div></div></div>
    <div class="col-6 col-md-3"><div class="exam-stat" style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);color:#2e7d32"><div class="number">{{ $results->pluck('student_id')->unique()->count() }}</div><div class="label">Students</div></div></div>
    <div class="col-6 col-md-3"><div class="exam-stat" style="background:linear-gradient(135deg,#f3e5f5,#e1bee7);color:#7b1fa2"><div class="number">{{ $results->pluck('subject_id')->unique()->count() }}</div><div class="label">Subjects</div></div></div>
    <div class="col-6 col-md-3">
      @php $tm = $results->sum('total_marks'); $om = $results->sum('obtained_marks'); @endphp
      <div class="exam-stat" style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);color:#1565c0"><div class="number">{{ $tm > 0 ? round(($om/$tm)*100,1) : 0 }}%</div><div class="label">Overall Average</div></div>
    </div>
  </div>

  <div class="row g-4">
    {{-- Exam Summary --}}
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm" style="border-radius:14px">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-clipboard-data text-primary"></i> Exam Summary</h6>
          <div class="table-responsive">
            <table class="table rpt-table table-hover mb-0">
              <thead><tr><th>Exam</th><th>Students</th><th>Subjects</th><th>Total</th><th>Obtained</th><th>Avg %</th></tr></thead>
              <tbody>
                @forelse($examSummary as $e)
                <tr>
                  <td class="fw-semibold">{{ $e['exam'] }}</td>
                  <td>{{ $e['students'] }}</td>
                  <td>{{ $e['subjects'] }}</td>
                  <td>{{ number_format($e['total']) }}</td>
                  <td>{{ number_format($e['obtained']) }}</td>
                  <td><span class="badge {{ $e['avg_pct']>=70?'bg-success':($e['avg_pct']>=50?'bg-warning':'bg-danger') }}">{{ $e['avg_pct'] }}%</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">No exam data found.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- Top Performers --}}
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm" style="border-radius:14px">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-trophy-fill text-warning"></i> Top 10 Performers</h6>
          @forelse($topPerformers as $i => $tp)
          <div class="d-flex align-items-center py-2 {{ !$loop->last?'border-bottom':'' }}">
            <span class="rank-badge {{ $i<3?'rank-'.($i+1):'rank-other' }} me-3">{{ $i+1 }}</span>
            <div class="flex-grow-1">
              <a href="{{ route('student.show', $tp['student_id']) }}" class="fw-semibold text-decoration-none d-block">{{ $tp['student_name'] }}</a>
              <small class="text-muted">{{ $tp['obtained'] }} / {{ $tp['total'] }}</small>
            </div>
            <span class="badge {{ $tp['pct']>=70?'bg-success':($tp['pct']>=50?'bg-warning':'bg-danger') }} fs-6">{{ $tp['pct'] }}%</span>
          </div>
          @empty
          <p class="text-muted small">No results available.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</main>
@endsection
