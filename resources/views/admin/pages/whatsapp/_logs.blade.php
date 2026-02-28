{{-- ══════════ MESSAGE LOGS TAB ══════════ --}}
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <span class="badge bg-primary me-1">Total: {{ $logStats['total'] }}</span>
    <span class="badge bg-success me-1">Sent: {{ $logStats['sent'] }}</span>
    <span class="badge bg-danger">Failed: {{ $logStats['failed'] }}</span>
  </div>
  @if($logStats['total'] > 0)
  <form action="{{ route('whatsapp.clearLogs') }}" method="POST" onsubmit="return confirm('Clear all WhatsApp logs?')">
    @csrf @method('DELETE')
    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash-fill me-1"></i>Clear All Logs</button>
  </form>
  @endif
</div>

<div class="table-responsive">
  <table class="table table-hover table-bordered">
    <thead>
      <tr>
        <th>#</th>
        <th>Date</th>
        <th>To</th>
        <th>Recipient</th>
        <th>Type</th>
        <th>Message</th>
        <th>Status</th>
        <th>Provider</th>
      </tr>
    </thead>
    <tbody>
      @forelse($logs as $log)
      <tr>
        <td>{{ $loop->iteration }}</td>
        <td style="font-size:.8rem;white-space:nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
        <td style="font-size:.82rem">{{ $log->to }}</td>
        <td>
          @if($log->recipient_name)
            <span class="fw-semibold" style="font-size:.82rem">{{ $log->recipient_name }}</span>
            <span class="badge bg-light text-dark" style="font-size:.65rem">{{ ucfirst($log->recipient_type) }}</span>
          @else
            <span class="text-muted">—</span>
          @endif
        </td>
        <td><span class="badge bg-info text-dark">{{ ucfirst($log->message_type ?? 'manual') }}</span></td>
        <td style="max-width:250px;font-size:.8rem" class="text-truncate" title="{{ $log->message }}">{{ Str::limit($log->message, 60) }}</td>
        <td>
          @if($log->status == 'sent')
            <span class="badge bg-success">Sent</span>
          @elseif($log->status == 'failed')
            <span class="badge bg-danger" data-bs-toggle="tooltip" title="{{ $log->api_response }}">Failed</span>
          @else
            <span class="badge bg-warning text-dark">Pending</span>
          @endif
        </td>
        <td style="font-size:.8rem">{{ ucfirst($log->provider ?? '—') }}</td>
      </tr>
      @empty
      <tr>
        <td colspan="8" class="text-center py-4 text-muted">
          <i class="bi bi-chat-left-text fs-1 d-block mb-2" style="color:#c5cde8"></i>
          No message logs yet. Send a test message to get started.
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
@if($logs->hasPages())
<div class="mt-2">{{ $logs->links() }}</div>
@endif
