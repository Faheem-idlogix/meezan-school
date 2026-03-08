@extends('admin.layout.master')
@section('title', 'System Error Logs')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1><i class="bi bi-bug me-2"></i>System Error Logs</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Error Logs</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="ea-stat" style="background:linear-gradient(135deg,#dc3545,#e74c3c);">
                    <div class="ea-stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
                    <div class="ea-stat-label">Errors</div>
                    <div class="ea-stat-value">{{ number_format($totalErrors) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="ea-stat orange">
                    <div class="ea-stat-icon"><i class="bi bi-exclamation-circle"></i></div>
                    <div class="ea-stat-label">Warnings</div>
                    <div class="ea-stat-value">{{ number_format($totalWarnings) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="ea-stat purple">
                    <div class="ea-stat-icon"><i class="bi bi-x-octagon"></i></div>
                    <div class="ea-stat-label">Critical</div>
                    <div class="ea-stat-value">{{ number_format($totalCritical) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="ea-stat blue">
                    <div class="ea-stat-icon"><i class="bi bi-calendar-event"></i></div>
                    <div class="ea-stat-label">Today</div>
                    <div class="ea-stat-value">{{ number_format($todayCount) }}</div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('error-logs.index') }}">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select form-select-sm">
                                <option value="">All Types</option>
                                <option value="exception" {{ request('type') == 'exception' ? 'selected' : '' }}>Exception</option>
                                <option value="database" {{ request('type') == 'database' ? 'selected' : '' }}>Database</option>
                                <option value="validation" {{ request('type') == 'validation' ? 'selected' : '' }}>Validation</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Severity</label>
                            <select name="severity" class="form-select form-select-sm">
                                <option value="">All</option>
                                <option value="error" {{ request('severity') == 'error' ? 'selected' : '' }}>Error</option>
                                <option value="warning" {{ request('severity') == 'warning' ? 'selected' : '' }}>Warning</option>
                                <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">From</label>
                            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To</label>
                            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Message or file...">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Clear Logs --}}
        <div class="d-flex justify-content-between mb-3">
            <span class="text-muted small">Showing {{ $errorLogs->total() }} log entries</span>
            <form method="POST" action="{{ route('error-logs.destroy') }}" onsubmit="return confirm('Are you sure you want to clear all error logs?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i> Clear All Logs</button>
            </form>
        </div>

        {{-- Error Table --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Severity</th>
                                <th>Message</th>
                                <th>File</th>
                                <th>URL</th>
                                <th>User</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($errorLogs as $idx => $err)
                                @php
                                    $sevBadge = match($err->severity) {
                                        'critical' => 'bg-danger',
                                        'warning'  => 'bg-warning text-dark',
                                        default    => 'bg-danger',
                                    };
                                    $typeBadge = match($err->type) {
                                        'database'   => 'bg-info',
                                        'validation' => 'bg-secondary',
                                        default      => 'bg-dark',
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $errorLogs->firstItem() + $idx }}</td>
                                    <td><span class="badge {{ $typeBadge }}">{{ ucfirst($err->type) }}</span></td>
                                    <td><span class="badge {{ $sevBadge }}">{{ ucfirst($err->severity) }}</span></td>
                                    <td style="max-width:300px;">
                                        <span title="{{ e($err->message) }}">{{ \Illuminate\Support\Str::limit($err->message, 80) }}</span>
                                    </td>
                                    <td style="max-width:200px;font-size:.75rem;" class="text-muted">
                                        {{ $err->file ? basename($err->file) . ':' . $err->line : '-' }}
                                    </td>
                                    <td style="max-width:150px;font-size:.75rem;" class="text-muted">
                                        {{ $err->method }} {{ \Illuminate\Support\Str::limit($err->url, 30) }}
                                    </td>
                                    <td>{{ $err->user_id ?? '-' }}</td>
                                    <td style="font-size:.78rem;">{{ $err->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary view-error-btn" data-id="{{ $err->id }}" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="bi bi-check-circle fs-1 d-block mb-2 text-success"></i>
                                        No error logs found. System is running smoothly!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($errorLogs->hasPages())
                <div class="card-footer">{{ $errorLogs->links() }}</div>
            @endif
        </div>
    </section>
</main>

{{-- Error Detail Modal --}}
<div class="modal fade" id="errorDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-bug me-2"></i>Error Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="errorDetailBody" style="max-height:70vh; overflow-y:auto; word-break:break-word;">
                <div class="text-center py-3"><div class="spinner-border text-primary"></div></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).on('click', '.view-error-btn', function() {
    var id = $(this).data('id');
    var modal = new bootstrap.Modal(document.getElementById('errorDetailModal'));
    $('#errorDetailBody').html('<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>');
    modal.show();

    $.get('/error-logs/' + id, function(data) {
        var html = '<table class="table table-sm table-bordered" style="table-layout:fixed; width:100%;">';
        html += '<tr><th style="width:120px;">Type</th><td>' + data.type + '</td></tr>';
        html += '<tr><th>Severity</th><td>' + data.severity + '</td></tr>';
        html += '<tr><th>Message</th><td style="word-break:break-word;"><code style="white-space:pre-wrap;">' + $('<div>').text(data.message).html() + '</code></td></tr>';
        html += '<tr><th>File</th><td style="word-break:break-all;">' + (data.file || '-') + ':' + (data.line || '') + '</td></tr>';
        html += '<tr><th>URL</th><td style="word-break:break-all;">' + (data.method || '') + ' ' + (data.url || '-') + '</td></tr>';
        html += '<tr><th>IP</th><td>' + (data.ip || '-') + '</td></tr>';
        html += '<tr><th>User ID</th><td>' + (data.user_id || '-') + '</td></tr>';
        html += '<tr><th>Date</th><td>' + data.created_at + '</td></tr>';
        if (data.trace) {
            html += '<tr><th>Stack Trace</th><td><pre style="max-height:300px;overflow:auto;font-size:.75rem;">' + $('<div>').text(data.trace).html() + '</pre></td></tr>';
        }
        if (data.context) {
            html += '<tr><th>Context</th><td><pre style="max-height:200px;overflow:auto;font-size:.75rem;">' + JSON.stringify(data.context, null, 2) + '</pre></td></tr>';
        }
        html += '</table>';
        $('#errorDetailBody').html(html);
    });
});
</script>
@endsection
