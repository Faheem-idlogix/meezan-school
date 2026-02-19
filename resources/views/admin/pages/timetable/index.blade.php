@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
  <div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1><i class="bi bi-calendar3 me-2 text-primary"></i>Timetable</h1>
      <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item active">Timetable</li></ol></nav>
    </div>
    <a href="{{ route('timetable.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Period</a>
  </div>

  {{-- Filter --}}
  <div class="card mb-3">
    <div class="card-body py-2">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-auto">
          <label class="form-label small mb-1">Class</label>
          <select name="class_id" class="form-select form-select-sm select2">
            <option value="">Select Class...</option>
            @foreach($classes as $c)<option value="{{ $c->id }}" @selected($c->id == $classId)>{{ $c->class_name }}</option>@endforeach
          </select>
        </div>
        <div class="col-auto">
          <label class="form-label small mb-1">Session</label>
          <select name="session_id" class="form-select form-select-sm">
            <option value="">All Sessions</option>
            @foreach($sessions as $s)<option value="{{ $s->id }}" @selected($s->id == $sessionId)>{{ $s->session_name }}</option>@endforeach
          </select>
        </div>
        <div class="col-auto"><button class="btn btn-primary btn-sm">View</button></div>
      </form>
    </div>
  </div>

  @if(session('success'))<div class="alert alert-success alert-dismissible border-0 mb-3"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>{{ session('success') }}</div>@endif

  @if($classId)
  {{-- Timetable Grid --}}
  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered mb-0 text-center">
          <thead style="background:#012970;color:#fff">
            <tr>
              <th style="width:120px">Day</th>
              <th colspan="10">Periods</th>
            </tr>
          </thead>
          <tbody>
            @foreach($days as $day)
            <tr>
              <td class="fw-bold text-capitalize py-2" style="background:#f6f9ff;color:#012970">{{ $day }}</td>
              <td class="p-0" colspan="10">
                @if($timetable[$day]->count())
                <div class="d-flex flex-wrap gap-1 p-2">
                  @foreach($timetable[$day] as $slot)
                  <div class="timetable-slot">
                    <div class="slot-time">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</div>
                    <div class="slot-subject">{{ $slot->subject?->subject_name ?? '—' }}</div>
                    <div class="slot-teacher">{{ $slot->teacher?->teacher_name ?? '—' }}</div>
                    <form action="{{ route('timetable.destroy', $slot) }}" method="POST" class="mt-1">@csrf @method('DELETE')
                      <button class="btn btn-sm p-0 text-danger" style="font-size:.65rem" title="Remove period"><i class="bi bi-x-circle"></i></button>
                    </form>
                  </div>
                  @endforeach
                </div>
                @else
                <span class="text-muted small">— No periods —</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @else
  <div class="text-center py-5 text-muted"><i class="bi bi-calendar3 fs-1 d-block mb-2"></i>Select a class to view its timetable.</div>
  @endif
</main>
@endsection

@section('css')
<style>
.timetable-slot {
  background: #f0f4ff;
  border: 1px solid #e0e7ff;
  border-radius: 6px;
  padding: 6px 10px;
  min-width: 130px;
  text-align: left;
  border-left: 3px solid var(--ea-primary);
}
.slot-time    { font-size:.7rem; color:#999; }
.slot-subject { font-size:.82rem; font-weight:700; color:#012970; }
.slot-teacher { font-size:.75rem; color:#666; }
</style>
@endsection
