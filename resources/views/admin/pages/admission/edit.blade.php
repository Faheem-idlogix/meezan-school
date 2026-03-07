@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
      <h1>Edit Admission Enquiry</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('admission.index') }}">Admissions</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="card shadow-sm border-0">
        <div class="card-body pt-4">
          <form action="{{ route('admission.update', $admission) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Student Name <span class="text-danger">*</span></label>
                <input type="text" name="student_name" class="form-control" value="{{ old('student_name', $admission->student_name) }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Father Name</label>
                <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $admission->father_name) }}">
              </div>
              <div class="col-md-4">
                <label class="form-label">Contact No <span class="text-danger">*</span></label>
                <input type="text" name="contact_no" class="form-control" value="{{ old('contact_no', $admission->contact_no) }}" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $admission->email) }}">
              </div>
              <div class="col-md-4">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                  <option value="">Select</option>
                  <option value="male" @selected(old('gender', $admission->gender) === 'male')>Male</option>
                  <option value="female" @selected(old('gender', $admission->gender) === 'female')>Female</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $admission->date_of_birth?->format('Y-m-d')) }}">
              </div>
              <div class="col-md-4">
                <label class="form-label">Class Applied For</label>
                <select name="class_room_id" class="form-select">
                  <option value="">Select Class</option>
                  @foreach($classes as $class)
                  <option value="{{ $class->id }}" @selected($admission->class_room_id == $class->id)>{{ $class->class_name }} {{ $class->section_name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                  @foreach(['enquiry','test_scheduled','test_taken','approved','rejected','enrolled'] as $s)
                  <option value="{{ $s }}" @selected($admission->status === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Test Date</label>
                <input type="date" name="test_date" class="form-control" value="{{ old('test_date', $admission->test_date?->format('Y-m-d')) }}">
              </div>
              <div class="col-md-4">
                <label class="form-label">Test Marks</label>
                <input type="number" step="0.01" name="test_marks" class="form-control" value="{{ old('test_marks', $admission->test_marks) }}">
              </div>
              <div class="col-md-4">
                <label class="form-label">Previous School</label>
                <input type="text" name="previous_school" class="form-control" value="{{ old('previous_school', $admission->previous_school) }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control" value="{{ old('address', $admission->address) }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Test Remarks</label>
                <input type="text" name="test_remarks" class="form-control" value="{{ old('test_remarks', $admission->test_remarks) }}">
              </div>
              <div class="col-12">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control" rows="2">{{ old('remarks', $admission->remarks) }}</textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-check-lg me-1"></i>Update Enquiry
                </button>
                <a href="{{ route('admission.index') }}" class="btn btn-secondary ms-2">Cancel</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </section>
</main>
@endsection
