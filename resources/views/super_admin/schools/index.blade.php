@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
  <div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1><i class="bi bi-buildings me-2 text-primary"></i>All Schools</h1>
      <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">Super Admin</a></li><li class="breadcrumb-item active">Schools</li></ol></nav>
    </div>
    <a href="{{ route('super_admin.schools.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add School</a>
  </div>

  @if(session('success'))<div class="alert alert-success alert-dismissible border-0 mb-3"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>{{ session('success') }}</div>@endif

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover datatable mb-0">
          <thead><tr><th>#</th><th>School</th><th>Plan</th><th>Students</th><th>Status</th><th>Expires</th><th>Actions</th></tr></thead>
          <tbody>
            @forelse($schools as $i => $school)
            <tr>
              <td>{{ $i+1 }}</td>
              <td>
                <strong>{{ $school->name }}</strong><br>
                <small class="text-muted"><i class="bi bi-link-45deg"></i>{{ $school->subdomain ?? '—' }}</small>
              </td>
              <td>{{ $school->plan?->name ?? 'Free' }}</td>
              <td>{{ $school->current_students }}</td>
              <td>
                @if($school->status === 'active') <span class="badge bg-success">Active</span>
                @elseif($school->status === 'trial') <span class="badge bg-warning text-dark">Trial</span>
                @elseif($school->status === 'suspended') <span class="badge bg-danger">Suspended</span>
                @else <span class="badge bg-secondary">Inactive</span>
                @endif
              </td>
              <td>{{ $school->subscription_end?->format('d M Y') ?? '—' }}</td>
              <td>
                <div class="d-flex gap-1">
                  <a href="{{ route('super_admin.schools.edit', $school) }}" class="fee-btn fee-btn-partial" title="Edit"><i class="bi bi-pencil"></i></a>
                  <form action="{{ route('super_admin.schools.destroy', $school) }}" method="POST" onsubmit="return confirm('Remove this school?')">@csrf @method('DELETE')
                    <button class="fee-btn fee-btn-cancel" title="Remove"><i class="bi bi-trash"></i></button>
                  </form>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-4 text-muted">No schools yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="mt-3">{{ $schools->links() }}</div>
</main>
@endsection
