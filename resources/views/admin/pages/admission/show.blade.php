@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>Enquiry Detail</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admission.index') }}">Admissions</a></li>
            <li class="breadcrumb-item active">{{ $admission->student_name }}</li>
          </ol>
        </nav>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('admission.edit', $admission) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
        @if($admission->status === 'enquiry')
        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#scheduleTestModal"><i class="bi bi-calendar-event me-1"></i>Schedule Test</button>
        @endif
        @if($admission->status === 'test_scheduled')
        <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#recordTestModal"><i class="bi bi-pencil-square me-1"></i>Record Test</button>
        @endif
        @if(in_array($admission->status, ['test_taken', 'enquiry']))
        <form action="{{ route('admission.approve', $admission) }}" method="POST" class="d-inline">
          @csrf
          <button class="btn btn-success btn-sm"><i class="bi bi-check-circle me-1"></i>Approve</button>
        </form>
        <form action="{{ route('admission.reject', $admission) }}" method="POST" class="d-inline" onsubmit="return confirm('Reject this admission?')">
          @csrf
          <button class="btn btn-danger btn-sm"><i class="bi bi-x-circle me-1"></i>Reject</button>
        </form>
        @endif
        @if($admission->status === 'approved')
        <form action="{{ route('admission.enroll', $admission) }}" method="POST" class="d-inline">
          @csrf
          <button class="btn btn-primary btn-sm"><i class="bi bi-person-plus me-1"></i>Enroll as Student</button>
        </form>
        @endif
      </div>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-md-8">
          <div class="card shadow-sm border-0">
            <div class="card-body pt-4">
              <h5 class="card-title">{{ $admission->student_name }} {!! $admission->status_badge !!}</h5>
              <div class="row g-3">
                <div class="col-md-6"><strong>Father:</strong> {{ $admission->father_name ?? '—' }}</div>
                <div class="col-md-6"><strong>Contact:</strong> {{ $admission->contact_no }}</div>
                <div class="col-md-6"><strong>Email:</strong> {{ $admission->email ?? '—' }}</div>
                <div class="col-md-6"><strong>Gender:</strong> {{ ucfirst($admission->gender ?? '—') }}</div>
                <div class="col-md-6"><strong>DOB:</strong> {{ $admission->date_of_birth ? $admission->date_of_birth->format('d M Y') : '—' }}</div>
                <div class="col-md-6"><strong>Class Applied:</strong> {{ $admission->classRoom->class_name ?? $admission->class_applied ?? '—' }}</div>
                <div class="col-md-6"><strong>Previous School:</strong> {{ $admission->previous_school ?? '—' }}</div>
                <div class="col-md-6"><strong>Enquiry Date:</strong> {{ $admission->enquiry_date ? $admission->enquiry_date->format('d M Y') : '—' }}</div>
                <div class="col-12"><strong>Address:</strong> {{ $admission->address ?? '—' }}</div>
                <div class="col-12"><strong>Remarks:</strong> {{ $admission->remarks ?? '—' }}</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm border-0">
            <div class="card-body pt-4">
              <h5 class="card-title">Test Information</h5>
              <p><strong>Test Date:</strong> {{ $admission->test_date ? $admission->test_date->format('d M Y') : 'Not scheduled' }}</p>
              <p><strong>Test Marks:</strong> {{ $admission->test_marks ?? '—' }}</p>
              <p><strong>Test Remarks:</strong> {{ $admission->test_remarks ?? '—' }}</p>
              <hr>
              <p><strong>Processed By:</strong> {{ $admission->processedBy->name ?? '—' }}</p>
              @if($admission->student)
              <p><strong>Enrolled As:</strong> <a href="{{ route('student.show', $admission->student) }}">{{ $admission->student->student_name }}</a></p>
              @endif
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- Schedule Test Modal --}}
    <div class="modal fade" id="scheduleTestModal" tabindex="-1">
      <div class="modal-dialog">
        <form action="{{ route('admission.scheduleTest', $admission) }}" method="POST">
          @csrf
          <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Schedule Admission Test</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
              <label class="form-label">Test Date</label>
              <input type="date" name="test_date" class="form-control" required>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Schedule</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    {{-- Record Test Modal --}}
    <div class="modal fade" id="recordTestModal" tabindex="-1">
      <div class="modal-dialog">
        <form action="{{ route('admission.recordTest', $admission) }}" method="POST">
          @csrf
          <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Record Test Result</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Test Marks</label>
                <input type="number" step="0.01" name="test_marks" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Remarks</label>
                <textarea name="test_remarks" class="form-control" rows="2"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Save Result</button>
            </div>
          </div>
        </form>
      </div>
    </div>
</main>
@endsection
