@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
  <div class="pagetitle mb-3">
    <h1><i class="bi bi-calendar-plus me-2 text-primary"></i>Submit Leave Request</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('leave.index') }}">Leaves</a></li><li class="breadcrumb-item active">New</li></ol></nav>
  </div>
  <div class="card mx-auto" style="max-width:600px">
    <div class="card-header"><h5 class="card-title mb-0">Leave Details</h5></div>
    <div class="card-body">
      <form action="{{ route('leave.store') }}" method="POST">
        @csrf
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Leave For</label>
            <select name="leavable_type" id="leaveType" class="form-select" required>
              <option value="teacher">Teacher</option>
              <option value="student">Student</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Select Person</label>
            <select name="leavable_id" id="leavePersonTeacher" class="form-select select2" required>
              <option value="">Select Teacher...</option>
              @foreach($teachers as $t)<option value="{{ $t->id }}">{{ $t->teacher_name }}</option>@endforeach
            </select>
            <select name="leavable_id" id="leavePersonStudent" class="form-select select2 d-none" disabled>
              <option value="">Select Student...</option>
              @foreach($students as $s)<option value="{{ $s->id }}">{{ $s->student_name }}</option>@endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Leave Type</label>
            <select name="leave_type" class="form-select" required>
              <option value="sick">Sick</option>
              <option value="casual">Casual</option>
              <option value="annual">Annual</option>
              <option value="emergency">Emergency</option>
              <option value="maternity">Maternity</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">From Date</label>
            <input type="date" name="from_date" class="form-control" value="{{ today()->toDateString() }}" required>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">To Date</label>
            <input type="date" name="to_date" class="form-control" value="{{ today()->toDateString() }}" required>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Reason</label>
            <textarea name="reason" class="form-control" rows="3" placeholder="Reason for leave..." required></textarea>
          </div>
        </div>
        <div class="d-flex gap-2 mt-4 justify-content-end">
          <a href="{{ route('leave.index') }}" class="btn btn-outline-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary px-4"><i class="bi bi-send me-1"></i>Submit</button>
        </div>
      </form>
    </div>
  </div>
</main>
@endsection

@section('scripts')
<script>
$('#leaveType').on('change', function() {
  if ($(this).val() === 'teacher') {
    $('#leavePersonTeacher').removeClass('d-none').prop('disabled', false);
    $('#leavePersonStudent').addClass('d-none').prop('disabled', true);
  } else {
    $('#leavePersonStudent').removeClass('d-none').prop('disabled', false);
    $('#leavePersonTeacher').addClass('d-none').prop('disabled', true);
  }
});
</script>
@endsection
