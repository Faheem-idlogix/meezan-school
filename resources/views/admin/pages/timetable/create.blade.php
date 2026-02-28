@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
  <div class="pagetitle mb-3">
    <h1><i class="bi bi-plus-circle me-2 text-primary"></i>Add Period</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('timetable.index') }}">Timetable</a></li><li class="breadcrumb-item active">Add Period</li></ol></nav>
  </div>
  <div class="card mx-auto" style="max-width:620px">
    <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-clock me-2 text-primary"></i>Period Details</h5></div>
    <div class="card-body">
      <form action="{{ route('timetable.store') }}" method="POST">
        @csrf
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Class <span class="text-danger">*</span></label>
            <select name="class_room_id" class="form-select select2" required>
              <option value="">Select Class...</option>
              @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->class_name }}</option>@endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Session</label>
            <select name="session_id" class="form-select select2">
              <option value="">Select Session...</option>
              @foreach($sessions as $s)<option value="{{ $s->id }}">{{ $s->session_name }}</option>@endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
            <select name="subject_id" class="form-select select2" required>
              <option value="">Select Subject...</option>
              @foreach($subjects as $s)<option value="{{ $s->id }}">{{ $s->subject_name }}</option>@endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Teacher <span class="text-danger">*</span></label>
            <select name="teacher_id" class="form-select select2" required>
              <option value="">Select Teacher...</option>
              @foreach($teachers as $t)<option value="{{ $t->id }}">{{ $t->teacher_name }}</option>@endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Day <span class="text-danger">*</span></label>
            <select name="day" class="form-select" required>
              @foreach(['monday','tuesday','wednesday','thursday','friday','saturday'] as $d)
                <option value="{{ $d }}" class="text-capitalize">{{ ucfirst($d) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Start Time <span class="text-danger">*</span></label>
            <input type="time" name="start_time" class="form-control" value="08:00" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">End Time <span class="text-danger">*</span></label>
            <input type="time" name="end_time" class="form-control" value="08:45" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Room No.</label>
            <input type="text" name="room_no" class="form-control" placeholder="e.g. A-101">
          </div>
        </div>
        <div class="d-flex gap-2 mt-4 justify-content-end">
          <a href="{{ route('timetable.index') }}" class="btn btn-outline-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Add Period</button>
        </div>
      </form>
    </div>
  </div>
</main>
@endsection
