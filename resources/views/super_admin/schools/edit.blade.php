@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
  <div class="pagetitle mb-3">
    <h1><i class="bi bi-pencil-square me-2 text-warning"></i>Edit School</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('super_admin.schools') }}">Schools</a></li><li class="breadcrumb-item active">Edit: {{ $school->name }}</li></ol></nav>
  </div>
  <div class="card mx-auto" style="max-width:800px">
    <div class="card-header d-flex align-items-center gap-2">
      @if($school->logo)
        <img src="{{ asset('storage/'.$school->logo) }}" alt="Logo" style="height:36px;width:36px;object-fit:contain;border-radius:6px;">
      @endif
      <h5 class="card-title mb-0">{{ $school->name }}</h5>
      <span class="ms-auto badge bg-{{ $school->status === 'active' ? 'success' : ($school->status === 'trial' ? 'warning' : 'danger') }}">{{ ucfirst($school->status) }}</span>
    </div>
    <div class="card-body">
      <form action="{{ route('super_admin.schools.update', $school) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label fw-semibold">School Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name', $school->name) }}" required></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Subdomain</label><div class="input-group"><input type="text" name="subdomain" class="form-control" value="{{ old('subdomain', $school->subdomain) }}" placeholder="school1"><span class="input-group-text">.yourapp.com</span></div><small class="text-muted">Leave blank to disable subdomain</small></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Plan</label><select name="plan_id" class="form-select"><option value="">No Plan (Free)</option>@foreach($plans as $p)<option value="{{ $p->id }}" {{ $school->plan_id == $p->id ? 'selected' : '' }}>{{ $p->name }} — Rs.{{ number_format($p->price,0) }}/mo</option>@endforeach</select></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Status</label><select name="status" class="form-select" required><option value="trial" {{ $school->status === 'trial' ? 'selected' : '' }}>Trial</option><option value="active" {{ $school->status === 'active' ? 'selected' : '' }}>Active</option><option value="inactive" {{ $school->status === 'inactive' ? 'selected' : '' }}>Inactive</option></select></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $school->email) }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $school->phone) }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Principal Name</label><input type="text" name="principal_name" class="form-control" value="{{ old('principal_name', $school->principal_name) }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Registration No.</label><input type="text" name="registration_no" class="form-control" value="{{ old('registration_no', $school->registration_no) }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">WhatsApp Number</label><input type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $school->whatsapp_number) }}" placeholder="923001234567"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">City</label><input type="text" name="city" class="form-control" value="{{ old('city', $school->city) }}"></div>
          <div class="col-12"><label class="form-label fw-semibold">Address</label><textarea name="address" class="form-control" rows="2">{{ old('address', $school->address) }}</textarea></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Subscription Start</label><input type="date" name="subscription_start" class="form-control" value="{{ old('subscription_start', optional($school->subscription_start)->toDateString()) }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Subscription End</label><input type="date" name="subscription_end" class="form-control" value="{{ old('subscription_end', optional($school->subscription_end)->toDateString()) }}"></div>
          <div class="col-12">
            <label class="form-label fw-semibold">School Logo</label>
            @if($school->logo)
              <div class="mb-2 d-flex align-items-center gap-2">
                <img src="{{ asset('storage/'.$school->logo) }}" alt="Current Logo" style="height:50px;border-radius:8px;border:1px solid #dee2e6;">
                <span class="text-muted small">Current logo</span>
              </div>
            @endif
            <input type="file" name="logo" class="form-control" accept="image/*">
            <small class="text-muted">Leave empty to keep current logo</small>
          </div>
          <div class="col-md-6"><label class="form-label fw-semibold">Max Students</label><input type="number" name="max_students" class="form-control" value="{{ old('max_students', $school->max_students ?? '') }}" placeholder="Unlimited"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Max Teachers</label><input type="number" name="max_teachers" class="form-control" value="{{ old('max_teachers', $school->max_teachers ?? '') }}" placeholder="Unlimited"></div>
        </div>
        <div class="d-flex gap-2 mt-4 justify-content-end">
          <a href="{{ route('super_admin.schools') }}" class="btn btn-outline-secondary">Cancel</a>
          <button type="submit" class="btn btn-warning px-4"><i class="bi bi-save me-1"></i>Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  {{-- School Stats Card --}}
  <div class="card mx-auto mt-3" style="max-width:800px">
    <div class="card-body">
      <h6 class="fw-bold text-muted mb-3">School Statistics</h6>
      <div class="row g-3 text-center">
        <div class="col-3"><div class="border rounded p-3"><div class="h4 fw-bold text-primary">{{ $school->students()->count() }}</div><small class="text-muted">Students</small></div></div>
        <div class="col-3"><div class="border rounded p-3"><div class="h4 fw-bold text-success">{{ $school->teachers()->count() }}</div><small class="text-muted">Teachers</small></div></div>
        <div class="col-3"><div class="border rounded p-3"><div class="h4 fw-bold text-info">{{ $school->users()->count() }}</div><small class="text-muted">Users</small></div></div>
        <div class="col-3"><div class="border rounded p-3"><div class="h4 fw-bold {{ $school->isSubscriptionValid() ? 'text-success' : 'text-danger' }}">{{ $school->isSubscriptionValid() ? '✓ Valid' : '✗ Expired' }}</div><small class="text-muted">Subscription</small></div></div>
      </div>
      <div class="mt-3 d-flex gap-2 justify-content-end">
        <small class="text-muted">Created: {{ $school->created_at->format('d M Y') }}</small>
      </div>
    </div>
  </div>
</main>
@endsection
