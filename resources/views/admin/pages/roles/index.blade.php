@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>Roles & Permissions</h1>
        <nav><ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item active">Roles</li>
        </ol></nav>
      </div>
      <a href="{{ route('roles.create') }}" class="btn btn-primary">
        <i class="bi bi-shield-plus me-1"></i> Add Role
      </a>
    </div>

    <section class="section">
      <div class="row">
        {{-- Stats Cards --}}
        <div class="col-md-3 col-6">
          <div class="card shadow-sm border-0 text-center p-3">
            <div class="text-primary mb-1"><i class="bi bi-shield-lock fs-2"></i></div>
            <h3 class="mb-0">{{ $roles->count() }}</h3>
            <small class="text-muted">Total Roles</small>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card shadow-sm border-0 text-center p-3">
            <div class="text-success mb-1"><i class="bi bi-key fs-2"></i></div>
            <h3 class="mb-0">{{ \App\Models\Permission::count() }}</h3>
            <small class="text-muted">Total Permissions</small>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card shadow-sm border-0 text-center p-3">
            <div class="text-warning mb-1"><i class="bi bi-lock-fill fs-2"></i></div>
            <h3 class="mb-0">{{ $roles->where('is_system', true)->count() }}</h3>
            <small class="text-muted">System Roles</small>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card shadow-sm border-0 text-center p-3">
            <div class="text-info mb-1"><i class="bi bi-people-fill fs-2"></i></div>
            <h3 class="mb-0">{{ $roles->sum('users_count') }}</h3>
            <small class="text-muted">Assigned Users</small>
          </div>
        </div>

        {{-- Roles Table --}}
        <div class="col-12">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <h5 class="card-title">All Roles</h5>
              <table class="table table-hover datatable align-middle">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Role</th>
                    <th>Description</th>
                    <th class="text-center">Permissions</th>
                    <th class="text-center">Users</th>
                    <th class="text-center">Type</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($roles as $i => $role)
                  <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                      <div>
                        <strong>{{ $role->display_name }}</strong>
                        <br><small class="text-muted font-monospace">{{ $role->name }}</small>
                      </div>
                    </td>
                    <td><small class="text-muted">{{ Str::limit($role->description, 60) }}</small></td>
                    <td class="text-center">
                      @if($role->name === 'super_admin')
                        <span class="badge bg-success">All</span>
                      @else
                        <span class="badge bg-primary">{{ $role->permissions_count }}</span>
                      @endif
                    </td>
                    <td class="text-center">
                      <span class="badge bg-info">{{ $role->users_count }}</span>
                    </td>
                    <td class="text-center">
                      @if($role->is_system)
                        <span class="badge bg-warning text-dark"><i class="bi bi-lock-fill me-1"></i>System</span>
                      @else
                        <span class="badge bg-secondary">Custom</span>
                      @endif
                    </td>
                    <td>
                      <div class="d-flex gap-1">
                        <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-outline-info" title="View">
                          <i class="bi bi-eye-fill"></i>
                        </a>
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                          <i class="bi bi-pencil-fill"></i>
                        </a>
                        @if(!$role->is_system)
                        <form action="{{ route('roles.destroy', $role) }}" method="POST"
                              onsubmit="return confirm('Delete role \'{{ $role->display_name }}\'?')">
                          @method('DELETE') @csrf
                          <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                            <i class="bi bi-trash-fill"></i>
                          </button>
                        </form>
                        @endif
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr><td colspan="7" class="text-center text-muted py-4">No roles found. Run the seeder first.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
</main>
@endsection
