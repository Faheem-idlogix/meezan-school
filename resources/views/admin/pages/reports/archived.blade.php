@extends("admin.layout.master")
@section("content")
<main id="main" class="main">
  <div class="pagetitle">
    <h1><i class="bi bi-trash3-fill text-danger"></i> Archived Records</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li><li class="breadcrumb-item active">Archived</li></ol></nav>
  </div>

  <style>
    .arc-tab .nav-link{border-radius:10px 10px 0 0;font-weight:600;font-size:13px;padding:10px 20px;color:#555}
    .arc-tab .nav-link.active{background:#c62828;color:#fff;border-color:#c62828}
    .rpt-table{border-radius:0 0 12px 12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.05)}
    .rpt-table thead{background:#c62828;color:#fff}
    .rpt-table th{font-weight:600;font-size:13px;padding:10px 14px;border:none}
    .rpt-table td{font-size:13px;padding:10px 14px;vertical-align:middle}
    .trashed-row{background:#fff8f8}
    .empty-state{text-align:center;padding:40px 20px;color:#aaa}
    .empty-state i{font-size:48px;margin-bottom:10px;display:block}
    .count-badge{background:rgba(198,40,40,.1);color:#c62828;font-weight:700;border-radius:20px;padding:2px 10px;font-size:12px;margin-left:6px}
  </style>

  {{-- Summary --}}
  <div class="row g-3 mb-4">
    @php
      $counts = [
        'Students' => $students->count(),
        'Teachers' => $teachers->count(),
        'Vouchers' => $vouchers->count(),
        'Classes'  => $classrooms->count(),
        'Subjects' => $subjects->count(),
        'Results'  => $results->count(),
      ];
      $total = array_sum($counts);
    @endphp
    <div class="col-12">
      <div class="alert alert-danger border-0 shadow-sm" style="border-radius:12px;background:linear-gradient(135deg,#ffebee,#ffcdd2)">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>{{ $total }}</strong> total archived records found across all modules.
        These records have been soft-deleted and can be restored.
      </div>
    </div>
  </div>

  {{-- Tabs --}}
  <ul class="nav nav-tabs arc-tab mb-0" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-students">Students <span class="count-badge">{{ $students->count() }}</span></a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-teachers">Teachers <span class="count-badge">{{ $teachers->count() }}</span></a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-vouchers">Vouchers <span class="count-badge">{{ $vouchers->count() }}</span></a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-classes">Classes <span class="count-badge">{{ $classrooms->count() }}</span></a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-subjects">Subjects <span class="count-badge">{{ $subjects->count() }}</span></a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-results">Results <span class="count-badge">{{ $results->count() }}</span></a></li>
  </ul>

  <div class="tab-content">
    {{-- Students Tab --}}
    <div class="tab-pane fade show active" id="tab-students">
      @if($students->count())
      <div class="table-responsive">
        <table class="table rpt-table table-hover mb-0">
          <thead><tr><th>#</th><th>Name</th><th>Class</th><th>Phone</th><th>Deleted On</th><th>Action</th></tr></thead>
          <tbody>
            @foreach($students as $i => $s)
            <tr class="trashed-row">
              <td>{{ $i+1 }}</td>
              <td class="fw-semibold">{{ $s->student_name }}</td>
              <td>{{ $s->classroom->class_name ?? '—' }}</td>
              <td>{{ $s->phone_number ?? '—' }}</td>
              <td>{{ $s->deleted_at->format('d M Y h:i A') }}</td>
              <td>
                <form action="{{ route('student.restore', $s->id) }}" method="POST" class="d-inline">@csrf
                  <button class="btn btn-sm btn-outline-success"><i class="bi bi-arrow-counterclockwise"></i> Restore</button>
                </form>
                <form action="{{ route('student.forceDelete', $s->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Permanently delete this student?')">@csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash-fill"></i> Delete Forever</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else
      <div class="empty-state"><i class="bi bi-check-circle"></i><p>No deleted students.</p></div>
      @endif
    </div>

    {{-- Teachers Tab --}}
    <div class="tab-pane fade" id="tab-teachers">
      @if($teachers->count())
      <div class="table-responsive">
        <table class="table rpt-table table-hover mb-0">
          <thead><tr><th>#</th><th>Name</th><th>Phone</th><th>Deleted On</th><th>Action</th></tr></thead>
          <tbody>
            @foreach($teachers as $i => $t)
            <tr class="trashed-row">
              <td>{{ $i+1 }}</td>
              <td class="fw-semibold">{{ $t->teacher_name }}</td>
              <td>{{ $t->phone_number ?? '—' }}</td>
              <td>{{ $t->deleted_at->format('d M Y h:i A') }}</td>
              <td>
                <form action="{{ route('teacher.restore', $t->id) }}" method="POST" class="d-inline">@csrf
                  <button class="btn btn-sm btn-outline-success"><i class="bi bi-arrow-counterclockwise"></i> Restore</button>
                </form>
                <form action="{{ route('teacher.forceDelete', $t->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Permanently delete?')">@csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash-fill"></i> Delete Forever</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else
      <div class="empty-state"><i class="bi bi-check-circle"></i><p>No deleted teachers.</p></div>
      @endif
    </div>

    {{-- Vouchers Tab --}}
    <div class="tab-pane fade" id="tab-vouchers">
      @if($vouchers->count())
      <div class="table-responsive">
        <table class="table rpt-table table-hover mb-0">
          <thead><tr><th>#</th><th>Date</th><th>Type</th><th>Category</th><th>Amount</th><th>Deleted On</th></tr></thead>
          <tbody>
            @foreach($vouchers as $i => $v)
            <tr class="trashed-row">
              <td>{{ $i+1 }}</td>
              <td>{{ $v->voucher_date ? $v->voucher_date->format('d M Y') : '—' }}</td>
              <td><span class="badge {{ $v->type==='income'?'bg-success':'bg-danger' }}">{{ ucfirst($v->type) }}</span></td>
              <td>{{ $v->category ?? '—' }}</td>
              <td class="fw-bold">₨ {{ number_format($v->amount) }}</td>
              <td>{{ $v->deleted_at->format('d M Y h:i A') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else
      <div class="empty-state"><i class="bi bi-check-circle"></i><p>No deleted vouchers.</p></div>
      @endif
    </div>

    {{-- Classes Tab --}}
    <div class="tab-pane fade" id="tab-classes">
      @if($classrooms->count())
      <div class="table-responsive">
        <table class="table rpt-table table-hover mb-0">
          <thead><tr><th>#</th><th>Class Name</th><th>Deleted On</th><th>Action</th></tr></thead>
          <tbody>
            @foreach($classrooms as $i => $c)
            <tr class="trashed-row">
              <td>{{ $i+1 }}</td>
              <td class="fw-semibold">{{ $c->class_name }}</td>
              <td>{{ $c->deleted_at->format('d M Y h:i A') }}</td>
              <td>
                <form action="{{ route('class.restore', $c->id) }}" method="POST" class="d-inline">@csrf
                  <button class="btn btn-sm btn-outline-success"><i class="bi bi-arrow-counterclockwise"></i> Restore</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else
      <div class="empty-state"><i class="bi bi-check-circle"></i><p>No deleted classes.</p></div>
      @endif
    </div>

    {{-- Subjects Tab --}}
    <div class="tab-pane fade" id="tab-subjects">
      @if($subjects->count())
      <div class="table-responsive">
        <table class="table rpt-table table-hover mb-0">
          <thead><tr><th>#</th><th>Subject Name</th><th>Deleted On</th></tr></thead>
          <tbody>
            @foreach($subjects as $i => $s)
            <tr class="trashed-row">
              <td>{{ $i+1 }}</td>
              <td class="fw-semibold">{{ $s->subject_name ?? $s->name ?? '—' }}</td>
              <td>{{ $s->deleted_at->format('d M Y h:i A') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else
      <div class="empty-state"><i class="bi bi-check-circle"></i><p>No deleted subjects.</p></div>
      @endif
    </div>

    {{-- Results Tab --}}
    <div class="tab-pane fade" id="tab-results">
      @if($results->count())
      <div class="table-responsive">
        <table class="table rpt-table table-hover mb-0">
          <thead><tr><th>#</th><th>Student</th><th>Exam</th><th>Marks</th><th>Deleted On</th></tr></thead>
          <tbody>
            @foreach($results as $i => $r)
            <tr class="trashed-row">
              <td>{{ $i+1 }}</td>
              <td>{{ $r->student->student_name ?? '—' }}</td>
              <td>{{ $r->exam->exam_name ?? '—' }}</td>
              <td>{{ $r->obtained_marks }}/{{ $r->total_marks }}</td>
              <td>{{ $r->deleted_at->format('d M Y h:i A') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else
      <div class="empty-state"><i class="bi bi-check-circle"></i><p>No deleted results.</p></div>
      @endif
    </div>
  </div>
</main>
@endsection
