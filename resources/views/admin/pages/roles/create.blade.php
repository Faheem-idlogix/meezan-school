@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
      <h1>Create Role</h1>
      <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
        <li class="breadcrumb-item active">Create</li>
      </ol></nav>
    </div>

    <section class="section">
      <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="row">
          {{-- Role Info --}}
          <div class="col-lg-4">
            <div class="card shadow-sm border-0">
              <div class="card-body p-4">
                <h5 class="card-title">Role Information</h5>
                <div class="mb-3">
                  <label class="form-label">Role Slug <span class="text-danger">*</span></label>
                  <input type="text" name="name" class="form-control font-monospace @error('name') is-invalid @enderror"
                         value="{{ old('name') }}" placeholder="e.g. class_teacher"
                         pattern="[a-z_]+" title="Lowercase letters and underscores only">
                  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  <small class="text-muted">Lowercase letters & underscores only</small>
                </div>
                <div class="mb-3">
                  <label class="form-label">Display Name <span class="text-danger">*</span></label>
                  <input type="text" name="display_name" class="form-control @error('display_name') is-invalid @enderror"
                         value="{{ old('display_name') }}" placeholder="e.g. Class Teacher">
                  @error('display_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                  <label class="form-label">Description</label>
                  <textarea name="description" class="form-control" rows="3" placeholder="What can this role do?">{{ old('description') }}</textarea>
                </div>
                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary"><i class="bi bi-shield-plus me-1"></i> Create Role</button>
                  <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
              </div>
            </div>
          </div>

          {{-- Permissions --}}
          <div class="col-lg-8">
            <div class="card shadow-sm border-0">
              <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="card-title mb-0">Assign Permissions</h5>
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="selectAll">
                    <label class="form-check-label fw-bold" for="selectAll">Select All</label>
                  </div>
                </div>

                <div class="row">
                  @foreach($permissionGroups as $group => $permissions)
                  <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border h-100">
                      <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                        <div class="form-check mb-0">
                          <input type="checkbox" class="form-check-input group-toggle" id="group-{{ Str::slug($group) }}"
                                 data-group="{{ Str::slug($group) }}">
                          <label class="form-check-label fw-semibold small" for="group-{{ Str::slug($group) }}">
                            {{ $group }}
                          </label>
                        </div>
                        <span class="badge bg-secondary">{{ $permissions->count() }}</span>
                      </div>
                      <div class="card-body py-2">
                        @foreach($permissions as $perm)
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input perm-checkbox perm-{{ Str::slug($group) }}"
                                 name="permissions[]" value="{{ $perm->id }}" id="perm-{{ $perm->id }}"
                                 {{ in_array($perm->id, old('permissions', [])) ? 'checked' : '' }}>
                          <label class="form-check-label small" for="perm-{{ $perm->id }}">
                            {{ $perm->display_name }}
                          </label>
                        </div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                  @endforeach
                </div>

              </div>
            </div>
          </div>
        </div>
      </form>
    </section>
</main>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select All
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = this.checked);
        document.querySelectorAll('.group-toggle').forEach(cb => cb.checked = this.checked);
    });
    // Group toggle
    document.querySelectorAll('.group-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const group = this.dataset.group;
            document.querySelectorAll('.perm-' + group).forEach(cb => cb.checked = this.checked);
        });
    });
    // Update group toggle when individual checkboxes change
    document.querySelectorAll('.perm-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            document.querySelectorAll('.group-toggle').forEach(toggle => {
                const group = toggle.dataset.group;
                const all = document.querySelectorAll('.perm-' + group);
                const checked = document.querySelectorAll('.perm-' + group + ':checked');
                toggle.checked = all.length === checked.length;
                toggle.indeterminate = checked.length > 0 && checked.length < all.length;
            });
        });
    });
});
</script>
@endsection
