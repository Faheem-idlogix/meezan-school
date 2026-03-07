@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>Notification Detail</h1>
        <nav><ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('notifications.index') }}">Notifications</a></li>
          <li class="breadcrumb-item active">{{ Str::limit($notification->title, 30) }}</li>
        </ol></nav>
      </div>
      <form action="{{ route('notifications.destroy', $notification) }}" method="POST"
            onsubmit="return confirm('Delete this notification?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Delete</button>
      </form>
    </div>

    <section class="section">
      <div class="row">
        {{-- Detail Card --}}
        <div class="col-lg-7">
          <div class="card shadow-sm border-0">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                  <h4 class="mb-1">{{ $notification->title }}</h4>
                  <small class="text-muted">Sent {{ $notification->created_at->format('d M Y H:i') }}
                    ({{ $notification->created_at->diffForHumans() }})</small>
                </div>
                {!! $notification->type_badge !!}
              </div>

              <div class="bg-light rounded p-3 mb-3">
                {!! nl2br(e($notification->message)) !!}
              </div>

              @if($notification->link)
              <p><strong>Link:</strong> <a href="{{ $notification->link }}">{{ $notification->link }}</a></p>
              @endif

              <div class="row g-3 mt-2">
                <div class="col-sm-6">
                  <strong class="small text-muted">Target Type</strong>
                  <div>
                    @if($notification->target_type === 'all')
                      <span class="badge bg-primary">All Users</span>
                    @elseif($notification->target_type === 'role')
                      <span class="badge bg-info">Role: {{ $notification->targetRole->display_name ?? '—' }}</span>
                    @elseif($notification->target_type === 'class')
                      <span class="badge bg-success">Class #{{ $notification->target_class_id }}</span>
                    @else
                      <span class="badge bg-secondary">Specific Users</span>
                    @endif
                  </div>
                </div>
                <div class="col-sm-6">
                  <strong class="small text-muted">Sent By</strong>
                  <div>{{ $notification->sender->name ?? 'System' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Recipients --}}
        <div class="col-lg-5">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <h5 class="card-title">
                Recipients
                <span class="badge bg-primary">{{ $notification->recipients->count() }}</span>
              </h5>
              <div style="max-height:400px;overflow-y:auto;">
                @forelse($notification->recipients as $user)
                <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                  <div class="d-flex align-items-center gap-2">
                    @php
                      $init = collect(explode(' ', $user->name))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('');
                      $colors = ['#4154f1','#2eca6a','#ff771d','#e74c3c','#9b59b6','#1abc9c'];
                    @endphp
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                         style="width:30px;height:30px;font-size:11px;background:{{ $colors[$user->id % count($colors)] }};">
                      {{ $init }}
                    </div>
                    <div>
                      <small class="fw-semibold">{{ $user->name }}</small><br>
                      <small class="text-muted">{{ $user->email }}</small>
                    </div>
                  </div>
                  <div>
                    @if($user->pivot->read_at)
                      <span class="badge bg-success"><i class="bi bi-check2"></i> Read</span>
                    @else
                      <span class="badge bg-secondary">Unread</span>
                    @endif
                  </div>
                </div>
                @empty
                <p class="text-muted small">No recipients.</p>
                @endforelse
              </div>

              @php
                $readCount = $notification->recipients->where('pivot.read_at', '!=', null)->count();
                $total = $notification->recipients->count();
                $pct = $total > 0 ? round(($readCount / $total) * 100) : 0;
              @endphp
              <div class="mt-3">
                <div class="d-flex justify-content-between small mb-1">
                  <span>Read rate</span>
                  <span class="fw-bold">{{ $readCount }}/{{ $total }} ({{ $pct }}%)</span>
                </div>
                <div class="progress" style="height:6px;">
                  <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
</main>
@endsection
