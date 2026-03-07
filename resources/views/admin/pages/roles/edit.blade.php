@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
      <h1>Edit Role</h1>
      <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
        <li class="breadcrumb-item active">Edit — {{ $role->display_name }}</li>
      </ol></nav>
    </div>

    <section class="section">
      <form action="{{ route('roles.update', $role) }}" method="POST">
        @csrf @method('PUT')
        <div class="row">
          {{-- Role Info --}}
          <div class="col-lg-4">
            <div class="card shadow-sm border-0">
              <div class="card-body p-4">
                <h5 class="card-title">Role Information</h5>
                <div class="mb-3">
                  <label class="form-label">Role Slug <span class="text-danger">*</span></label>
                  @if($role->is_system)
                    <input type="text" class="form-control font-monospace" value="{{ $role->name }}" disabled>
                    <small class="text-warning"><i class="bi bi-lock-fill me-1"></i>System role name cannot be changed</small>
                  @else
                    <input type="text" name="name" class="form-control font-monospace @error('name') is-invalid @enderror"
                           value="{{ old('name', $role->name) }}" pattern="[a-z_]+" title="Lowercase letters and underscores only">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  @endif
                </div>
                <div class="mb-3">
                  <label class="form-label">Display Name <span class="text-danger">*</span></label>
                  <input type="text" name="display_name" class="form-control @error('display_name') is-invalid @enderror"
                         value="{{ old('display_name', $role->display_name) }}">
                  @error('display_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                  <label class="form-label">Description</label>
                  <textarea name="description" class="form-control" rows="3">{{ old('description', $role->description) }}</textarea>
                </div>

                @if($role->is_system)
                <div class="alert alert-warning small py-2">
                  <i class="bi bi-shield-exclamation me-1"></i>
                  This is a system role. The slug cannot be changed or deleted.
                </div>
                @endif

                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Update Role</button>
                  <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
              </div>
            </div>

            {{-- Role Users Summary --}}
            <div class="card shadow-sm border-0 mt-3">
              <div class="card-body">
                <h5 class="card-title">Assigned Users</h5>
                @php $roleUsers = $role->users()->take(5)->get(); $totalUsers = $role->users()->count(); @endphp
                @if($roleUsers->count())
                  @foreach($roleUsers as $u)
                  <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold bg-primary"
                         style="width:32px;height:32px;font-size:12px;">
                      {{ strtoupper(substr($u->name,0,1)) }}
                    </div>
                    <div>
                      <small class="fw-semibold">{{ $u->name }}</small><br>
                      <small class="text-muted">{{ $u->email }}</small>
                    </div>
                  </div>
                  @endforeach
                  @if($totalUsers > 5)
                    <small class="text-muted">...and {{ $totalUsers - 5 }} more</small>
                  @endif
                @else
                  <p class="text-muted small mb-0">No users assigned to this role.</p>
                @endif
              </div>
            </div>
          </div>

          {{-- Permissions --}}
          <div class="col-lg-8">
            <div class="card shadow-sm border-0">
              <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="card-title mb-0">
                    Permissions
                    <span class="badge bg-primary ms-1">{{ count($rolePermissionIds) }} assigned</span>
                  </h5>
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="selectAll">
                    <label class="form-check-label fw-bold" for="selectAll">Select All</label>
                  </div>
                </div>

                @if($role->name === 'super_admin')
                <div class="alert alert-info small">
                  <i class="bi bi-info-circle me-1"></i>
                  Super Admin automatically has all permissions regardless of what is checked here.
                </div>
                @endif

                <div class="row">
                  @foreach($allPermissions as $group => $permissions)
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
                        <span class="badge bg-secondary">
                          <span class="group-count-{{ Str::slug($group) }}">{{ $permissions->whereIn('id', $rolePermissionIds)->count() }}</span>/{{ $permissions->count() }}
                        </span>
                      </div>
                      <div class="card-body py-2">
                        @foreach($permissions as $perm)
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input perm-checkbox perm-{{ Str::slug($group) }}"
                                 name="permissions[]" value="{{ $perm->id }}" id="perm-{{ $perm->id }}"
                                 {{ in_array($perm->id, old('permissions', $rolePermissionIds)) ? 'checked' : '' }}>
                          <label class="form-check-label small" for="perm-{{ $perm->id }}" title="{{ $perm->name }}">
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
    function updateGroupStates() {
        document.querySelectorAll('.group-toggle').forEach(toggle => {
            const group = toggle.dataset.group;
            const all = document.querySelectorAll('.perm-' + group);
            const checked = document.querySelectorAll('.perm-' + group + ':checked');
            toggle.checked = all.length === checked.length;
            toggle.indeterminate = checked.length > 0 && checked.length < all.length;
        });
    }
    // Init group states
    updateGroupStates();
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
    // Individual checkbox
    document.querySelectorAll('.perm-checkbox').forEach(cb => {
        cb.addEventListener('change', updateGroupStates);
    });
});
</script>
@endsection
