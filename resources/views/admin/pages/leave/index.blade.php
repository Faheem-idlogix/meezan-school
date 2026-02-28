@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
  <div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1><i class="bi bi-calendar-check me-2 text-primary"></i>Leave Requests</h1>
      <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item active">Leaves</li></ol></nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      @if($pending > 0)<span class="badge bg-danger px-3 py-2 fs-6">{{ $pending }} Pending</span>@endif
      <a href="{{ route('leave.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Leave</a>
    </div>
  </div>

  {{-- Filter tabs --}}
  <div class="card mb-3">
    <div class="card-body py-2 d-flex gap-3 align-items-center flex-wrap">
      <div>
        <a href="{{ route('leave.index', ['type'=>'all', 'status'=>$status]) }}" class="btn btn-sm {{ $type=='all' ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
        <a href="{{ route('leave.index', ['type'=>'teacher', 'status'=>$status]) }}" class="btn btn-sm {{ $type=='teacher' ? 'btn-primary' : 'btn-outline-secondary' }}">Teachers</a>
        <a href="{{ route('leave.index', ['type'=>'student', 'status'=>$status]) }}" class="btn btn-sm {{ $type=='student' ? 'btn-primary' : 'btn-outline-secondary' }}">Students</a>
      </div>
      <div>
        <a href="{{ route('leave.index', ['type'=>$type, 'status'=>'all']) }}" class="btn btn-sm {{ $status=='all' ? 'btn-secondary' : 'btn-outline-secondary' }}">All Status</a>
        <a href="{{ route('leave.index', ['type'=>$type, 'status'=>'pending']) }}" class="btn btn-sm {{ $status=='pending' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">Pending</a>
        <a href="{{ route('leave.index', ['type'=>$type, 'status'=>'approved']) }}" class="btn btn-sm {{ $status=='approved' ? 'btn-success' : 'btn-outline-success' }}">Approved</a>
        <a href="{{ route('leave.index', ['type'=>$type, 'status'=>'rejected']) }}" class="btn btn-sm {{ $status=='rejected' ? 'btn-danger' : 'btn-outline-danger' }}">Rejected</a>
      </div>
    </div>
  </div>

  @if(session('success'))<div class="alert alert-success alert-dismissible border-0 mb-3"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>{{ session('success') }}</div>@endif

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead><tr><th>#</th><th>Person</th><th>Type</th><th>Leave Type</th><th>From</th><th>To</th><th>Days</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            @forelse($leaves as $i => $l)
            <tr>
              <td>{{ $i+1 }}</td>
              <td>
                <strong>{{ $l->leavable?->teacher_name ?? $l->leavable?->student_name ?? '—' }}</strong>
                <span class="badge bg-light text-dark ms-1" style="font-size:.65rem">{{ class_basename($l->leavable_type) }}</span>
              </td>
              <td><span class="badge bg-light text-secondary">{{ class_basename($l->leavable_type) }}</span></td>
              <td><span class="badge bg-info text-dark">{{ ucfirst($l->leave_type) }}</span></td>
              <td>{{ $l->from_date->format('d M Y') }}</td>
              <td>{{ $l->to_date->format('d M Y') }}</td>
              <td><strong>{{ $l->total_days }}</strong></td>
              <td>{!! $l->status_badge !!}</td>
              <td>
                <div class="d-flex gap-1">
                  @if($l->status === 'pending')
                  <form action="{{ route('leave.approve', $l) }}" method="POST">@csrf @method('PUT')
                    <button class="btn btn-sm btn-outline-success" title="Approve"><i class="bi bi-check-lg"></i></button>
                  </form>
                  <button class="btn btn-sm btn-outline-warning" title="Reject" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $l->id }}"><i class="bi bi-x-lg"></i></button>
                  @endif
                  <form action="{{ route('leave.destroy', $l) }}" method="POST" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash-fill"></i></button>
                  </form>
                </div>
              </td>
            </tr>
            {{-- Reject Modal --}}
            <div class="modal fade" id="rejectModal{{ $l->id }}" tabindex="-1">
              <div class="modal-dialog modal-sm">
                <div class="modal-content">
                  <form action="{{ route('leave.reject', $l) }}" method="POST">@csrf @method('PUT')
                    <div class="modal-header"><h6 class="modal-title">Reject Leave</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body"><textarea name="rejection_reason" class="form-control" rows="3" placeholder="Reason for rejection..." required></textarea></div>
                    <div class="modal-footer"><button type="submit" class="btn btn-danger btn-sm">Reject</button></div>
                  </form>
                </div>
              </div>
            </div>
            @empty
            <tr><td colspan="9" class="text-center py-4 text-muted">No leave requests found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="mt-3">{{ $leaves->links() }}</div>
</main>
@endsection
