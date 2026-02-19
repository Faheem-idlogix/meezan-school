@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
  <div class="pagetitle mb-3">
    <h1><i class="bi bi-plus-square me-2 text-primary"></i>Add New School</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('super_admin.schools') }}">Schools</a></li><li class="breadcrumb-item active">Add</li></ol></nav>
  </div>
  <div class="card mx-auto" style="max-width:800px">
    <div class="card-header"><h5 class="card-title mb-0">School Information</h5></div>
    <div class="card-body">
      <form action="{{ route('super_admin.schools.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label fw-semibold">School Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Subdomain</label><div class="input-group"><input type="text" name="subdomain" class="form-control" value="{{ old('subdomain') }}" placeholder="school1"><span class="input-group-text">.yourapp.com</span></div><small class="text-muted">Lowercase letters, numbers, hyphens only</small></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Plan</label><select name="plan_id" class="form-select"><option value="">No Plan (Free)</option>@foreach($plans as $p)<option value="{{ $p->id }}">{{ $p->name }} — Rs.{{ number_format($p->price,0) }}/mo</option>@endforeach</select></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Status</label><select name="status" class="form-select" required><option value="trial">Trial</option><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone') }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Principal Name</label><input type="text" name="principal_name" class="form-control" value="{{ old('principal_name') }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Registration No.</label><input type="text" name="registration_no" class="form-control" value="{{ old('registration_no') }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">WhatsApp Number</label><input type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number') }}" placeholder="923001234567"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">City</label><input type="text" name="city" class="form-control" value="{{ old('city') }}"></div>
          <div class="col-12"><label class="form-label fw-semibold">Address</label><textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Subscription Start</label><input type="date" name="subscription_start" class="form-control" value="{{ today()->toDateString() }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Subscription End</label><input type="date" name="subscription_end" class="form-control"></div>
          <div class="col-12"><label class="form-label fw-semibold">School Logo</label><input type="file" name="logo" class="form-control" accept="image/*"></div>
          <hr>
          <h6 class="fw-bold text-muted">Admin User (optional)</h6>
          <div class="col-md-4"><label class="form-label small">Admin Name</label><input type="text" name="admin_name" class="form-control form-control-sm" value="{{ old('admin_name') }}"></div>
          <div class="col-md-4"><label class="form-label small">Admin Email</label><input type="email" name="admin_email" class="form-control form-control-sm" value="{{ old('admin_email') }}"></div>
          <div class="col-md-4"><label class="form-label small">Admin Password</label><input type="password" name="admin_password" class="form-control form-control-sm" placeholder="Default: password123"></div>
        </div>
        <div class="d-flex gap-2 mt-4 justify-content-end">
          <a href="{{ route('super_admin.schools') }}" class="btn btn-outline-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i>Create School</button>
        </div>
      </form>
    </div>
  </div>
</main>
@endsection
