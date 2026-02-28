@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>Notices & Announcements</h1>
        <nav><ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item active">Notices</li>
        </ol></nav>
      </div>
      <a href="{{ route('notice.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>New Notice
      </a>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <table class="table table-hover datatable align-middle">
                <thead class="table-light">
                  <tr><th>#</th><th>Title</th><th>Audience</th><th>Priority</th><th>WhatsApp</th><th>Status</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                  @forelse ($notices as $i => $notice)
                  <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $notice->title }}</strong><br><small class="text-muted">{{ Str::limit($notice->content, 60) }}</small></td>
                    <td><span class="badge bg-info">{{ ucfirst($notice->audience) }}</span></td>
                    <td>
                      <span class="badge {{ $notice->priority === 'high' ? 'bg-danger' : ($notice->priority === 'medium' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                        {{ ucfirst($notice->priority) }}
                      </span>
                    </td>
                    <td>
                      @if($notice->send_whatsapp)
                        <span class="badge bg-success"><i class="bi bi-whatsapp"></i> Yes</span>
                      @else
                        <span class="badge bg-light text-dark">No</span>
                      @endif
                    </td>
                    <td>
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" {{ $notice->is_active ? 'checked' : '' }}
                          onchange="toggleNotice({{ $notice->id }}, this)">
                      </div>
                    </td>
                    <td><small>{{ $notice->created_at->format('d M Y') }}</small></td>
                    <td>
                      <div class="d-flex gap-1">
                        <a href="{{ route('notice.edit', $notice) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                        <form action="{{ route('notice.destroy', $notice) }}" method="POST" onsubmit="return confirm('Delete this notice?')">
                          @method('DELETE') @csrf
                          <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash-fill"></i></button>
                        </form>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr><td colspan="8" class="text-center text-muted py-4">No notices found. <a href="{{ route('notice.create') }}">Create one</a></td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
</main>
@endsection

@section('script')
<script>
function toggleNotice(id, el) {
  $.post('/notice/' + id + '/toggle-status', { _token: '{{ csrf_token() }}' }, function(res) {
    toastr.success('Notice status updated');
  }).fail(function() { toastr.error('Failed to update status'); el.checked = !el.checked; });
}
</script>
@endsection
