@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle d-flex justify-content-between align-items-center">
        <div>
            <h1>Activity Logs</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Activity Logs</li>
                </ol>
            </nav>
        </div>
        <form action="{{ route('activity_logs.destroy') }}" method="POST"
              onsubmit="return confirm('Clear old logs? This cannot be undone.')">
            @csrf @method('DELETE')
            <div class="input-group input-group-sm">
                <span class="input-group-text">Clear older than</span>
                <select name="days" class="form-select form-select-sm" style="max-width:100px">
                    <option value="30">30 days</option>
                    <option value="60">60 days</option>
                    <option value="90" selected>90 days</option>
                    <option value="180">180 days</option>
                </select>
                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash-fill me-1"></i>Clear</button>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <section class="section">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                {{-- Filters --}}
                <form method="GET" class="row g-2 mb-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label form-label-sm">Action</label>
                        <select name="action" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($actions as $a)
                                <option value="{{ $a }}" {{ request('action') == $a ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $a)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label form-label-sm">User</label>
                        <input type="text" name="user" class="form-control form-control-sm" value="{{ request('user') }}" placeholder="Name...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label form-label-sm">Model</label>
                        <select name="model" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($models as $m)
                                <option value="{{ $m }}" {{ request('model') == $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label form-label-sm">From</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label form-label-sm">To</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button>
                        <a href="{{ route('activity_logs.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    </div>
                </form>

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:160px">Time</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>IP</th>
                                <th style="width:80px">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td><small class="text-muted">{{ $log->created_at->format('d M Y H:i:s') }}</small></td>
                                <td>{{ $log->user_name ?? 'System' }}</td>
                                <td>
                                    @php
                                        $badge = match($log->action) {
                                            'created'       => 'bg-success',
                                            'updated'       => 'bg-primary',
                                            'deleted'       => 'bg-warning text-dark',
                                            'force_deleted' => 'bg-danger',
                                            'restored'      => 'bg-info',
                                            'login'         => 'bg-secondary',
                                            'logout'        => 'bg-dark',
                                            default         => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span>
                                </td>
                                <td>
                                    {{ $log->description }}
                                    @if($log->model_type)
                                        <br><small class="text-muted">{{ class_basename($log->model_type) }} #{{ $log->model_id }}</small>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $log->ip_address }}</small></td>
                                <td>
                                    @if($log->old_values || $log->new_values)
                                        <a href="{{ route('activity_logs.show', $log) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No activity logs found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $logs->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
