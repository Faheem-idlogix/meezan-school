@extends("admin.layout.master")
@section("content")
<main id="main" class="main">
  <div class="pagetitle d-flex justify-content-between align-items-center flex-wrap">
    <div>
      <h1><i class="bi bi-people text-purple"></i> Student Report <span class="badge bg-primary fs-6">Advanced</span></h1>
      <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li><li class="breadcrumb-item active">Students (Advanced)</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-success btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i> Print</button>
    </div>
  </div>

  <style>
    .stu-stat{border:none;border-radius:14px;text-align:center;padding:20px 14px}
    .stu-stat .number{font-size:24px;font-weight:800}
    .stu-stat .label{font-size:11px;text-transform:uppercase;letter-spacing:.5px;opacity:.8}
    .adv-card{border:none;border-radius:14px;box-shadow:0 2px 10px rgba(0,0,0,.05)}
    .rpt-table{border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.05)}
    .rpt-table thead{background:#7b1fa2;color:#fff}
    .rpt-table th{font-weight:600;font-size:13px;padding:10px 14px;border:none}
    .rpt-table td{font-size:13px;padding:10px 14px;vertical-align:middle}
    @media print{.pagetitle .btn,.breadcrumb{display:none!important}.main{padding:0!important}}
  </style>

  {{-- Filter Bar --}}
  <div class="card adv-card mb-3">
    <div class="card-body p-3 d-flex align-items-center gap-3 flex-wrap">
      <select onchange="location.href='{{ route('reports.students') }}?class_id='+this.value+'&show_trashed={{ $showTrashed?'1':'0' }}'" class="form-select form-select-sm" style="width:auto">
        <option value="">All Classes</option>
        @foreach($classrooms as $c)
          <option value="{{ $c->id }}" {{ $classFilter==$c->id?'selected':'' }}>{{ $c->class_name }} {{ $c->section_name }}</option>
        @endforeach
      </select>
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="trashedToggle" {{ $showTrashed?'checked':'' }}
               onchange="location.href='{{ route('reports.students') }}?class_id={{ $classFilter }}&show_trashed='+(this.checked?'1':'0')">
        <label class="form-check-label small" for="trashedToggle">Include Archived</label>
      </div>
    </div>
  </div>

  {{-- Summary Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="stu-stat" style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);color:#2e7d32"><div class="number">{{ number_format($active) }}</div><div class="label">Active Students</div></div></div>
    <div class="col-6 col-lg-3"><div class="stu-stat" style="background:linear-gradient(135deg,#ffebee,#ffcdd2);color:#c62828"><div class="number">{{ number_format($inactive) }}</div><div class="label">Archived</div></div></div>
    <div class="col-6 col-lg-3"><div class="stu-stat" style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);color:#1565c0"><div class="number">{{ number_format($male) }}</div><div class="label">Male</div></div></div>
    <div class="col-6 col-lg-3"><div class="stu-stat" style="background:linear-gradient(135deg,#fce4ec,#f8bbd0);color:#c62828"><div class="number">{{ number_format($female) }}</div><div class="label">Female</div></div></div>
  </div>

  <div class="row g-4">
    {{-- Gender Pie --}}
    <div class="col-lg-4">
      <div class="card adv-card">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart text-info"></i> Gender Distribution</h6>
          <canvas id="genderPie" height="220"></canvas>
        </div>
      </div>
    </div>
    {{-- Class Distribution Bar --}}
    <div class="col-lg-8">
      <div class="card adv-card">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart text-primary"></i> Students per Class</h6>
          <canvas id="classChart" height="240"></canvas>
        </div>
      </div>
    </div>
  </div>

  {{-- Student Table --}}
  <div class="card adv-card mt-4">
    <div class="card-body p-4">
      <h6 class="fw-bold mb-3"><i class="bi bi-list-ul text-primary"></i> Student List ({{ $students->count() }})</h6>
      <div class="table-responsive" style="max-height:500px;overflow-y:auto">
        <table class="table rpt-table table-hover mb-0">
          <thead style="position:sticky;top:0"><tr><th>#</th><th>Name</th><th>Father</th><th>Class</th><th>Gender</th><th>Contact</th><th>Status</th></tr></thead>
          <tbody>
            @forelse($students as $i => $s)
            <tr class="{{ $s->trashed() ? 'table-danger' : '' }}">
              <td>{{ $i+1 }}</td>
              <td><a href="{{ route('student.show', $s->id) }}" class="fw-semibold text-decoration-none">{{ $s->student_name }}</a></td>
              <td>{{ $s->father_name ?? '—' }}</td>
              <td>{{ $s->classroom->class_name ?? '—' }}</td>
              <td>{{ ucfirst($s->gender ?? '—') }}</td>
              <td>{{ $s->contact_no ?? '—' }}</td>
              <td>
                @if($s->trashed()) <span class="badge bg-danger">Archived</span>
                @elseif($s->student_status === 'active') <span class="badge bg-success">Active</span>
                @else <span class="badge bg-secondary">{{ ucfirst($s->student_status ?? 'inactive') }}</span>
                @endif
              </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No students found.</td></tr>
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
new Chart(document.getElementById('genderPie'), {
  type:'doughnut',
  data:{labels:['Male','Female'],datasets:[{data:[{{ $male }},{{ $female }}],backgroundColor:['#1565c0','#e91e63']}]},
  options:{responsive:true,plugins:{legend:{position:'bottom'}}}
});

new Chart(document.getElementById('classChart'), {
  type:'bar',
  data:{
    labels:{!! json_encode($classDist->pluck('class')) !!},
    datasets:[{label:'Students',data:{!! json_encode($classDist->pluck('count')) !!},backgroundColor:'rgba(123,31,162,.6)',borderRadius:6}]
  },
  options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{stepSize:1}}}}
});
</script>
@endsection
