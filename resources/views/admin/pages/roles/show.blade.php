@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>{{ $role->display_name }}</h1>
        <nav><ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
          <li class="breadcrumb-item active">{{ $role->display_name }}</li>
        </ol></nav>
      </div>
      <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary">
        <i class="bi bi-pencil me-1"></i> Edit Role
      </a>
    </div>

    <section class="section">
      <div class="row">
        {{-- Role Info --}}
        <div class="col-lg-4">
          <div class="card shadow-sm border-0">
            <div class="card-body p-4">
              <h5 class="card-title">Role Details</h5>
              <table class="table table-borderless mb-0">
                <tr>
                  <th class="text-muted small ps-0" style="width:40%">Slug</th>
                  <td><code>{{ $role->name }}</code></td>
                </tr>
                <tr>
                  <th class="text-muted small ps-0">Display Name</th>
                  <td>{{ $role->display_name }}</td>
                </tr>
                <tr>
                  <th class="text-muted small ps-0">Description</th>
                  <td>{{ $role->description ?: '—' }}</td>
                </tr>
                <tr>
                  <th class="text-muted small ps-0">Type</th>
                  <td>
                    @if($role->is_system)
                      <span class="badge bg-warning text-dark"><i class="bi bi-lock-fill me-1"></i>System</span>
                    @else
                      <span class="badge bg-secondary">Custom</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th class="text-muted small ps-0">Users</th>
                  <td><span class="badge bg-info">{{ $role->users->count() }}</span></td>
                </tr>
                <tr>
                  <th class="text-muted small ps-0">Permissions</th>
                  <td>
                    @if($role->name === 'super_admin')
                      <span class="badge bg-success">All (bypasses checks)</span>
                    @else
                      <span class="badge bg-primary">{{ $role->permissions->count() }}</span>
                    @endif
                  </td>
                </tr>
              </table>
            </div>
          </div>

          {{-- Assigned Users --}}
          <div class="card shadow-sm border-0 mt-3">
            <div class="card-body">
              <h5 class="card-title">Assigned Users ({{ $role->users->count() }})</h5>
              @forelse($role->users as $u)
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
              @empty
              <p class="text-muted small mb-0">No users assigned.</p>
              @endforelse
            </div>
          </div>
        </div>

        {{-- Permissions Grid --}}
        <div class="col-lg-8">
          <div class="card shadow-sm border-0">
            <div class="card-body p-4">
              <h5 class="card-title">Permissions by Module</h5>

              @if($role->name === 'super_admin')
              <div class="alert alert-info small">
                <i class="bi bi-info-circle me-1"></i>
                Super Admin has access to everything. No explicit permissions needed.
              </div>
              @endif

              @if($permissionGroups->count())
              <div class="row">
                @foreach($permissionGroups as $group => $permissions)
                <div class="col-md-6 col-lg-4 mb-3">
                  <div class="card border h-100">
                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                      <span class="fw-semibold small">{{ $group }}</span>
                      <span class="badge bg-primary">{{ $permissions->count() }}</span>
                    </div>
                    <div class="card-body py-2">
                      @foreach($permissions as $perm)
                      <div class="d-flex align-items-center gap-2 py-1">
                        <i class="bi bi-check-circle-fill text-success small"></i>
                        <small>{{ $perm->display_name }}</small>
                      </div>
                      @endforeach
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
              @else
              <p class="text-muted">No permissions assigned to this role.</p>
              @endif

            </div>
          </div>
        </div>
      </div>
    </section>
</main>
@endsection
