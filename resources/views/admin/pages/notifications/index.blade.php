@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>Notifications</h1>
        <nav><ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item active">Notifications</li>
        </ol></nav>
      </div>
      <a href="{{ route('notifications.create') }}" class="btn btn-primary">
        <i class="bi bi-megaphone me-1"></i> Send Notification
      </a>
    </div>

    <section class="section">
      <div class="row">
        {{-- Stats --}}
        <div class="col-md-3 col-6">
          <div class="card shadow-sm border-0 text-center p-3">
            <div class="text-primary mb-1"><i class="bi bi-bell fs-2"></i></div>
            <h3 class="mb-0">{{ $notifications->total() }}</h3>
            <small class="text-muted">Total Sent</small>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card shadow-sm border-0 text-center p-3">
            <div class="text-info mb-1"><i class="bi bi-info-circle fs-2"></i></div>
            <h3 class="mb-0">{{ $notifications->where('type', 'info')->count() }}</h3>
            <small class="text-muted">Info</small>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card shadow-sm border-0 text-center p-3">
            <div class="text-warning mb-1"><i class="bi bi-exclamation-triangle fs-2"></i></div>
            <h3 class="mb-0">{{ $notifications->where('type', 'warning')->count() }}</h3>
            <small class="text-muted">Warning</small>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="card shadow-sm border-0 text-center p-3">
            <div class="text-danger mb-1"><i class="bi bi-x-circle fs-2"></i></div>
            <h3 class="mb-0">{{ $notifications->where('type', 'danger')->count() }}</h3>
            <small class="text-muted">Critical</small>
          </div>
        </div>

        {{-- Table --}}
        <div class="col-12">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <h5 class="card-title">All Notifications</h5>
              <table class="table table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Notification</th>
                    <th>Type</th>
                    <th>Target</th>
                    <th>Recipients</th>
                    <th>Sent By</th>
                    <th>Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($notifications as $n)
                  <tr>
                    <td>{{ $loop->iteration + ($notifications->currentPage() - 1) * $notifications->perPage() }}</td>
                    <td>
                      <strong>{{ $n->title }}</strong>
                      <br><small class="text-muted">{{ Str::limit($n->message, 50) }}</small>
                    </td>
                    <td>{!! $n->type_badge !!}</td>
                    <td>
                      @if($n->target_type === 'all')
                        <span class="badge bg-primary">All Users</span>
                      @elseif($n->target_type === 'role')
                        <span class="badge bg-info">{{ $n->targetRole->display_name ?? 'Role' }}</span>
                      @elseif($n->target_type === 'class')
                        <span class="badge bg-success">Class #{{ $n->target_class_id }}</span>
                      @else
                        <span class="badge bg-secondary">Specific Users</span>
                      @endif
                    </td>
                    <td><span class="badge bg-dark">{{ $n->recipients_count }}</span></td>
                    <td>{{ $n->sender->name ?? 'System' }}</td>
                    <td><small>{{ $n->created_at->format('d M Y H:i') }}</small></td>
                    <td>
                      <div class="d-flex gap-1">
                        <a href="{{ route('notifications.show', $n) }}" class="btn btn-sm btn-outline-info">
                          <i class="bi bi-eye"></i>
                        </a>
                        <form action="{{ route('notifications.destroy', $n) }}" method="POST"
                              onsubmit="return confirm('Delete this notification?')">
                          @csrf @method('DELETE')
                          <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr><td colspan="8" class="text-center text-muted py-4">No notifications sent yet.</td></tr>
                  @endforelse
                </tbody>
              </table>

              {{ $notifications->links() }}
            </div>
          </div>
        </div>
      </div>
    </section>
</main>
@endsection
