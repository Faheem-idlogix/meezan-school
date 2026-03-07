@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
      <h1>My Notifications</h1>
      <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">My Notifications</li>
      </ol></nav>
    </div>

    <section class="section">

      {{-- Quick stats --}}
      <div class="row mb-3">
        <div class="col-md-4">
          <div class="card border-start border-primary border-4 shadow-sm">
            <div class="card-body py-3 d-flex align-items-center gap-3">
              <i class="bi bi-bell fs-2 text-primary"></i>
              <div><h5 class="mb-0">{{ $notifications->total() }}</h5><small class="text-muted">Total</small></div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-start border-danger border-4 shadow-sm">
            <div class="card-body py-3 d-flex align-items-center gap-3">
              <i class="bi bi-envelope fs-2 text-danger"></i>
              <div><h5 class="mb-0">{{ $unreadCount }}</h5><small class="text-muted">Unread</small></div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-start border-success border-4 shadow-sm">
            <div class="card-body py-3 d-flex align-items-center gap-3">
              <i class="bi bi-check2-all fs-2 text-success"></i>
              <div><h5 class="mb-0">{{ $notifications->total() - $unreadCount }}</h5><small class="text-muted">Read</small></div>
            </div>
          </div>
        </div>
      </div>

      {{-- Mark all read --}}
      @if($unreadCount > 0)
      <div class="mb-2 text-end">
        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
          @csrf
          <button class="btn btn-sm btn-outline-success"><i class="bi bi-check2-all me-1"></i>Mark All as Read</button>
        </form>
      </div>
      @endif

      <div class="card shadow-sm border-0">
        <div class="card-body p-0">
          <div class="list-group list-group-flush">
            @forelse($notifications as $n)
            @php
              $isUnread = is_null($n->pivot->read_at);
              $typeColors = ['info'=>'primary','success'=>'success','warning'=>'warning','danger'=>'danger'];
              $typeBg = $typeColors[$n->type] ?? 'primary';
            @endphp
            <a href="{{ $n->link ?? '#' }}"
               class="list-group-item list-group-item-action py-3 {{ $isUnread ? 'bg-light' : '' }}"
               onclick="event.preventDefault(); markAndGo({{ $n->id }}, '{{ $n->link ?? '' }}');">
              <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0"
                     style="width:38px;height:38px;background:var(--bs-{{ $typeBg }});">
                  <i class="bi {{ $n->type_icon }}"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex justify-content-between align-items-center">
                    <strong class="{{ $isUnread ? '' : 'text-muted' }}">{{ $n->title }}</strong>
                    <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
                  </div>
                  <p class="mb-0 small text-muted">{{ Str::limit($n->message, 100) }}</p>
                </div>
                @if($isUnread)
                  <span class="badge rounded-pill bg-danger">NEW</span>
                @else
                  <i class="bi bi-check2 text-success"></i>
                @endif
              </div>
            </a>
            @empty
            <div class="text-center py-5 text-muted">
              <i class="bi bi-bell-slash fs-1"></i>
              <p class="mt-2">No notifications yet.</p>
            </div>
            @endforelse
          </div>
        </div>
      </div>

      <div class="mt-3">{{ $notifications->links() }}</div>
    </section>
</main>
@endsection

@section('scripts')
<script>
  function markAndGo(id, link) {
    fetch('/notifications/' + id + '/mark-read', {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}
    }).then(() => {
      if(link) window.location.href = link;
      else window.location.reload();
    });
  }
</script>
@endsection
