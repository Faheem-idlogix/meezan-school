@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle d-flex justify-content-between align-items-center">
        <div>
            <h1>Log Detail</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('activity_logs.index') }}">Activity Logs</a></li>
                    <li class="breadcrumb-item active">#{{ $activityLog->id }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('activity_logs.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">Log Information</h5>
                        <table class="table table-borderless">
                            <tr><th class="text-muted" style="width:140px">ID</th><td>{{ $activityLog->id }}</td></tr>
                            <tr><th class="text-muted">User</th><td>{{ $activityLog->user_name ?? 'System' }}</td></tr>
                            <tr>
                                <th class="text-muted">Action</th>
                                <td>
                                    @php
                                        $badge = match($activityLog->action) {
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
                                    <span class="badge {{ $badge }}">{{ ucfirst(str_replace('_', ' ', $activityLog->action)) }}</span>
                                </td>
                            </tr>
                            <tr><th class="text-muted">Description</th><td>{{ $activityLog->description }}</td></tr>
                            <tr><th class="text-muted">Model</th><td>{{ $activityLog->model_type ? class_basename($activityLog->model_type) : '—' }} {{ $activityLog->model_id ? '#'.$activityLog->model_id : '' }}</td></tr>
                            <tr><th class="text-muted">IP Address</th><td>{{ $activityLog->ip_address ?? '—' }}</td></tr>
                            <tr><th class="text-muted">Time</th><td>{{ $activityLog->created_at->format('d M Y H:i:s') }}</td></tr>
                            <tr><th class="text-muted">User Agent</th><td><small>{{ $activityLog->user_agent ?? '—' }}</small></td></tr>
                        </table>
                    </div>
                </div>
            </div>

            @if($activityLog->old_values || $activityLog->new_values)
            <div class="col-lg-6">
                @if($activityLog->action === 'updated' && $activityLog->old_values && $activityLog->new_values)
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title">Changes</h5>
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr><th>Field</th><th>Old Value</th><th>New Value</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($activityLog->new_values as $key => $newVal)
                                        @if(!in_array($key, ['updated_at', 'created_at']))
                                        <tr>
                                            <td><strong>{{ str_replace('_', ' ', ucfirst($key)) }}</strong></td>
                                            <td class="text-danger">{{ $activityLog->old_values[$key] ?? '—' }}</td>
                                            <td class="text-success">{{ $newVal }}</td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    @if($activityLog->old_values)
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Previous Values</h5>
                            <pre class="bg-light p-3 rounded" style="max-height:400px;overflow:auto;font-size:.8rem">{{ json_encode($activityLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                    @endif
                    @if($activityLog->new_values)
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title">New Values</h5>
                            <pre class="bg-light p-3 rounded" style="max-height:400px;overflow:auto;font-size:.8rem">{{ json_encode($activityLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                    @endif
                @endif
            </div>
            @endif
        </div>
    </section>
</main>
@endsection
