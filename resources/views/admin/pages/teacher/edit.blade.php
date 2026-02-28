@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
      <h1>Edit Teacher</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('teacher.index') }}">Teachers</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card shadow-sm border-0">
            <div class="card-body p-4">
              <h5 class="card-title mb-4">Edit Teacher: {{ $teacher->teacher_name }}</h5>
              <form action="{{ route('teacher.update', $teacher) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $teacher->first_name) }}">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $teacher->last_name) }}">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Father Name</label>
                    <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $teacher->father_name) }}">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select">
                      <option value="male"   {{ $teacher->gender === 'male'   ? 'selected' : '' }}>Male</option>
                      <option value="female" {{ $teacher->gender === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Contact No</label>
                    <input type="text" name="contact_no" class="form-control" value="{{ old('contact_no', $teacher->contact_no) }}">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">WhatsApp No</label>
                    <input type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $teacher->whatsapp_number) }}">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">CNIC</label>
                    <input type="text" name="teacher_cnic" class="form-control" value="{{ old('teacher_cnic', $teacher->teacher_cnic) }}">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="teacher_status" class="form-select">
                      <option value="active"   {{ $teacher->teacher_status === 'active'   ? 'selected' : '' }}>Active</option>
                      <option value="inactive" {{ $teacher->teacher_status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                  </div>
                  <div class="col-md-12">
                    <label class="form-label">Profile Photo</label>
                    @if($teacher->teacher_image)
                      <div class="mb-2">
                        <img src="{{ asset('img/teachers/'.$teacher->teacher_image) }}" width="80" class="rounded">
                      </div>
                    @endif
                    <input type="file" name="teacher_image" class="form-control" accept="image/*">
                  </div>
                  <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Update</button>
                    <a href="{{ route('teacher.index') }}" class="btn btn-outline-secondary">Cancel</a>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
</main>
@endsection
