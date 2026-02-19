@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle d-flex align-items-center justify-content-between">
      <div>
        <h1>Add Teacher</h1>
        <nav><ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('teacher.index') }}">Teachers</a></li>
          <li class="breadcrumb-item active">Add New</li>
        </ol></nav>
      </div>
      <a href="{{ route('teacher.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title"><i class="bi bi-person-workspace me-2 text-primary"></i>Teacher Information</h5>
            </div>
            <div class="card-body p-4">
              <form action="{{ route('teacher.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" placeholder="First name">
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" placeholder="Last name">
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Father Name</label>
                    <input type="text" name="father_name" class="form-control" value="{{ old('father_name') }}" placeholder="Father name">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                      <option value="">Select Gender</option>
                      <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                      <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Contact No</label>
                    <input type="text" name="contact_no" class="form-control" value="{{ old('contact_no') }}" placeholder="03XX-XXXXXXX">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">WhatsApp No</label>
                    <input type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number') }}" placeholder="92XXXXXXXXXX">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">CNIC</label>
                    <input type="text" name="teacher_cnic" class="form-control" value="{{ old('teacher_cnic') }}" placeholder="XXXXX-XXXXXXX-X">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="teacher_status" class="form-select @error('teacher_status') is-invalid @enderror">
                      <option value="">Select Status</option>
                      <option value="active" {{ old('teacher_status') === 'active' ? 'selected' : '' }}>Active</option>
                      <option value="inactive" {{ old('teacher_status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('teacher_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                  <div class="col-md-12">
                    <label class="form-label">Profile Photo</label>
                    <input type="file" name="teacher_image" class="form-control" accept="image/*">
                  </div>
                  <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i> Save Teacher</button>
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
