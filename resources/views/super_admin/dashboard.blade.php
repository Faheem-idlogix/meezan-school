@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
  <div class="pagetitle mb-4">
    <h1><i class="bi bi-building me-2 text-primary"></i>Super Admin Panel</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item active">Super Admin</li></ol></nav>
  </div>

  {{-- Stats --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="ea-stat blue"><div class="ea-stat-icon"><i class="bi bi-buildings"></i></div><div class="ea-stat-label">Total Schools</div><div class="ea-stat-value">{{ $stats['total_schools'] }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="ea-stat green"><div class="ea-stat-icon"><i class="bi bi-check-circle"></i></div><div class="ea-stat-label">Active</div><div class="ea-stat-value">{{ $stats['active_schools'] }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="ea-stat orange"><div class="ea-stat-icon"><i class="bi bi-hourglass"></i></div><div class="ea-stat-label">Trial</div><div class="ea-stat-value">{{ $stats['trial_schools'] }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="ea-stat teal"><div class="ea-stat-icon"><i class="bi bi-people"></i></div><div class="ea-stat-label">Total Users</div><div class="ea-stat-value">{{ $stats['total_users'] }}</div></div></div>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0 fw-bold">Recent Schools</h5>
    <a href="{{ route('super_admin.schools') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Manage All Schools</a>
  </div>
  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead><tr><th>#</th><th>School Name</th><th>Plan</th><th>Status</th><th>Subscription Ends</th><th>Actions</th></tr></thead>
          <tbody>
            @forelse($schools as $i => $school)
            <tr>
              <td>{{ $i+1 }}</td>
              <td><strong>{{ $school->name }}</strong><br><small class="text-muted">{{ $school->subdomain ? $school->subdomain.'.yourapp.com' : 'No subdomain' }}</small></td>
              <td>{{ $school->plan?->name ?? '—' }}</td>
              <td>
                @if($school->status === 'active') <span class="badge bg-success">Active</span>
                @elseif($school->status === 'trial') <span class="badge bg-warning text-dark">Trial</span>
                @elseif($school->status === 'suspended') <span class="badge bg-danger">Suspended</span>
                @else <span class="badge bg-secondary">Inactive</span>
                @endif
              </td>
              <td>{{ $school->subscription_end?->format('d M Y') ?? 'No expiry' }}</td>
              <td>
                <a href="{{ route('super_admin.schools.edit', $school) }}" class="fee-btn fee-btn-partial" title="Edit"><i class="bi bi-pencil"></i></a>
              </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-4 text-muted">No schools yet. <a href="{{ route('super_admin.schools.create') }}">Add first school</a>.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>
@endsection
