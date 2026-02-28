@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

  <div class="pagetitle d-flex align-items-center justify-content-between">
    <div>
      <h1>Add Student</h1>
      <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student.index') }}">Students</a></li>
        <li class="breadcrumb-item active">Add New</li>
      </ol></nav>
    </div>
    <a href="{{ route('student.index') }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i> Back
    </a>
  </div>

  <section class="section">
    <form method="post" action="{{ route('student.store') }}" enctype="multipart/form-data" novalidate>
      @csrf
      <div class="row g-3">

        <div class="col-lg-8">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Personal Information</h5>
            </div>
            <div class="card-body pt-3">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">First Name <span class="text-danger">*</span></label>
                  <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="First name"
                    class="form-control @error('first_name') is-invalid @enderror">
                  @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label">Last Name <span class="text-danger">*</span></label>
                  <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Last name"
                    class="form-control @error('last_name') is-invalid @enderror">
                  @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label">Father Name</label>
                  <input type="text" name="father_name" value="{{ old('father_name') }}" placeholder="Father full name" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Contact No</label>
                  <input type="text" name="contact_no" value="{{ old('contact_no') }}" placeholder="03001234567" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">WhatsApp Number</label>
                  <div class="input-group">
                    <span class="input-group-text" style="background:#f6f9ff;border-color:#dee2e6;"><i class="bi bi-whatsapp text-success"></i></span>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number') }}" placeholder="923001234567" class="form-control">
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Student CNIC / B-Form</label>
                  <input type="text" name="student_cnic" value="{{ old('student_cnic') }}" placeholder="CNIC or B-Form No" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Gender <span class="text-danger">*</span></label>
                  <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                    <option value="">Select Gender</option>
                    <option value="Boy" {{ old('gender')=='Boy' ? 'selected' : '' }}>Boy</option>
                    <option value="Girl" {{ old('gender')=='Girl' ? 'selected' : '' }}>Girl</option>
                  </select>
                  @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label">Date of Birth</label>
                  <input type="date" name="student_dob" value="{{ old('student_dob') }}" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Admission Date</label>
                  <input type="date" name="student_admission_date" value="{{ old('student_admission_date') }}" class="form-control">
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card mb-3">
            <div class="card-header">
              <h5 class="card-title"><i class="bi bi-image me-2 text-primary"></i>Profile Photo</h5>
            </div>
            <div class="card-body pt-3 text-center">
              <div id="preview-wrap" style="display:none;" class="mb-3">
                <img id="file-ip-1-preview" class="rounded" style="max-width:140px;max-height:140px;object-fit:cover;border:3px solid #f0f4ff;">
              </div>
              <div id="avatar-placeholder" class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                   style="width:100px;height:100px;background:linear-gradient(135deg,#4154f1,#717ff5);color:#fff;font-size:2.5rem;">
                <i class="bi bi-person"></i>
              </div>
              <input type="file" name="student_image" class="form-control form-control-sm" accept="image/*" onchange="showPreviewOne(event)">
              <small class="text-muted d-block mt-1">JPG, PNG max 2MB</small>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <h5 class="card-title"><i class="bi bi-mortarboard me-2 text-primary"></i>Academic Details</h5>
            </div>
            <div class="card-body pt-3">
              <div class="mb-3">
                <label class="form-label">Class <span class="text-danger">*</span></label>
                <select name="class_room_id" class="form-select @error('class_room_id') is-invalid @enderror">
                  <option value="">Select Class</option>
                  @foreach($class_room as $classroom)
                    <option value="{{ $classroom->id }}" {{ old('class_room_id') == $classroom->id ? 'selected' : '' }}>
                      {{ $classroom->class_name }} {{ $classroom->section_name }}
                    </option>
                  @endforeach
                </select>
                @error('class_room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="student_status" class="form-select @error('student_status') is-invalid @enderror">
                  <option value="active" {{ old('student_status')=='active' ? 'selected' : '' }}>Active</option>
                  <option value="inactive" {{ old('student_status')=='inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('student_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('student.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            <button type="submit" class="btn btn-primary px-5">
              <i class="bi bi-check-lg me-1"></i> Save Student
            </button>
          </div>
        </div>

      </div>
    </form>
  </section>
</main>
@endsection

@section('script')
<script>
function showPreviewOne(event) {
  if (event.target.files.length > 0) {
    document.getElementById('file-ip-1-preview').src = URL.createObjectURL(event.target.files[0]);
    document.getElementById('preview-wrap').style.display = 'block';
    document.getElementById('avatar-placeholder').style.display = 'none';
  }
}
</script>
@endsection
