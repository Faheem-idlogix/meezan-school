@extends("admin.layout.master")
@section("content")
<main id="main" class="main">
  <div class="pagetitle">
    <h1><i class="bi bi-people-fill text-purple" style="color:#7b1fa2"></i> Student Report</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li><li class="breadcrumb-item active">Students</li></ol></nav>
  </div>

  <style>
    .stu-stat{border:none;border-radius:14px;text-align:center;padding:20px 14px}
    .stu-stat .number{font-size:28px;font-weight:800}
    .stu-stat .label{font-size:11px;text-transform:uppercase;letter-spacing:.5px;opacity:.8}
    .filter-bar{background:#fff;border-radius:12px;padding:12px 18px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,.06);display:flex;align-items:center;gap:10px;flex-wrap:wrap}
    .rpt-table{border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.05)}
    .rpt-table thead{background:#7b1fa2;color:#fff}
    .rpt-table th{font-weight:600;font-size:13px;padding:10px 14px;border:none}
    .rpt-table td{font-size:13px;padding:10px 14px;vertical-align:middle}
    .trashed-row{background:#fff5f5 !important}
  </style>

  {{-- Filters --}}
  <div class="filter-bar">
    <i class="bi bi-funnel text-muted"></i>
    <select onchange="applyFilter()" id="classFilter" class="form-select form-select-sm" style="width:180px;border-radius:20px">
      <option value="">All Classes</option>
      @foreach($classrooms as $c)
        <option value="{{ $c->id }}" {{ $classFilter==$c->id?'selected':'' }}>{{ $c->class_name }}</option>
      @endforeach
    </select>
    <label class="form-check-label ms-3">
      <input type="checkbox" id="trashedToggle" class="form-check-input" {{ $showTrashed?'checked':'' }} onchange="applyFilter()">
      Include Deleted Records
    </label>
    <span class="ms-auto text-muted small">Total: <strong>{{ $students->count() }}</strong> students</span>
  </div>

  {{-- Summary Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="stu-stat" style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);color:#2e7d32"><div class="number">{{ $active }}</div><div class="label">Active</div></div></div>
    <div class="col-6 col-md-3"><div class="stu-stat" style="background:linear-gradient(135deg,#ffebee,#ffcdd2);color:#c62828"><div class="number">{{ $inactive }}</div><div class="label">Deleted</div></div></div>
    <div class="col-6 col-md-3"><div class="stu-stat" style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);color:#1565c0"><div class="number">{{ $male }}</div><div class="label">Male</div></div></div>
    <div class="col-6 col-md-3"><div class="stu-stat" style="background:linear-gradient(135deg,#fce4ec,#f8bbd0);color:#ad1457"><div class="number">{{ $female }}</div><div class="label">Female</div></div></div>
  </div>

  <div class="row g-4">
    {{-- Class Distribution --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm" style="border-radius:14px">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart text-primary"></i> Class Distribution</h6>
          @foreach($classDist as $d)
          <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
            <span class="small fw-semibold">{{ $d['class'] }}</span>
            <span class="badge bg-primary bg-opacity-10 text-primary">{{ $d['count'] }} students</span>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Student List --}}
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm" style="border-radius:14px">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-table text-success"></i> Student List</h6>
          <div class="table-responsive" style="max-height:500px;overflow-y:auto">
            <table class="table rpt-table table-hover mb-0">
              <thead style="position:sticky;top:0"><tr><th>#</th><th>Name</th><th>Class</th><th>Gender</th><th>Phone</th><th>Status</th></tr></thead>
              <tbody>
                @forelse($students as $i => $s)
                <tr class="{{ $s->trashed() ? 'trashed-row' : '' }}">
                  <td>{{ $i+1 }}</td>
                  <td>
                    <a href="{{ route('student.show', $s->id) }}" class="fw-semibold text-decoration-none">{{ $s->student_name }}</a>
                    @if($s->trashed()) <span class="badge bg-danger ms-1">Deleted</span> @endif
                  </td>
                  <td>{{ $s->classroom->class_name ?? '—' }}</td>
                  <td>{{ ucfirst($s->gender ?? '—') }}</td>
                  <td>{{ $s->phone_number ?? '—' }}</td>
                  <td>
                    @if($s->trashed())
                      <span class="badge bg-danger">Archived</span>
                    @else
                      <span class="badge bg-success">Active</span>
                    @endif
                  </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No students found.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Deleted Students --}}
  @if($trashedStudents->count() > 0)
  <div class="card border-0 shadow-sm mt-4" style="border-radius:14px;border-left:4px solid #c62828 !important">
    <div class="card-body p-4">
      <h6 class="fw-bold mb-3 text-danger"><i class="bi bi-trash3"></i> Deleted Students ({{ $trashedStudents->count() }})</h6>
      <div class="table-responsive">
        <table class="table rpt-table table-hover mb-0">
          <thead style="background:#c62828"><tr><th>#</th><th>Name</th><th>Class</th><th>Deleted On</th><th>Action</th></tr></thead>
          <tbody>
            @foreach($trashedStudents as $i => $ts)
            <tr class="trashed-row">
              <td>{{ $i+1 }}</td>
              <td class="fw-semibold">{{ $ts->student_name }}</td>
              <td>{{ $ts->classroom->class_name ?? '—' }}</td>
              <td>{{ $ts->deleted_at->format('d M Y h:i A') }}</td>
              <td>
                <form action="{{ route('student.restore', $ts->id) }}" method="POST" class="d-inline">
                  @csrf
                  <button class="btn btn-sm btn-outline-success" title="Restore"><i class="bi bi-arrow-counterclockwise"></i> Restore</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif
</main>
@endsection

@section("scripts")
<script>
function applyFilter() {
  const cls = document.getElementById('classFilter').value;
  const trashed = document.getElementById('trashedToggle').checked;
  let url = '{{ route("reports.students") }}?';
  if (cls) url += 'class_id=' + cls + '&';
  if (trashed) url += 'show_trashed=1&';
  location.href = url;
}
</script>
@endsection
