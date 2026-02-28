@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>Classes</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Classes</li>
          </ol>
        </nav>
      </div>
      <a href="{{ route('class.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Add Class
      </a>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card shadow-sm border-0">
            <div class="card-body">

              <ul class="nav nav-tabs mb-3" id="classTabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#active-classes" type="button">
                    <i class="bi bi-journal-check me-1"></i> Active
                    <span class="badge rounded-pill bg-success ms-1">{{ $class->count() }}</span>
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#inactive-classes" type="button">
                    <i class="bi bi-journal-x me-1"></i> Inactive
                    <span class="badge rounded-pill bg-danger ms-1">{{ $trashed->count() }}</span>
                  </button>
                </li>
              </ul>

              <div class="tab-content">

                <!-- ACTIVE -->
                <div class="tab-pane fade show active" id="active-classes" role="tabpanel">
                  <div class="table-responsive">
                    <table class="table table-hover datatable align-middle">
                      <thead class="table-light">
                        <tr><th>#</th><th>Class Name</th><th>Section</th><th>Session</th><th>Status</th><th>Actions</th></tr>
                      </thead>
                      <tbody>
                        @forelse ($class as $i => $item)
                        <tr id="class-row-{{ $item->id }}">
                          <td>{{ $i + 1 }}</td>
                          <td><strong>{{ $item->class_name }}</strong></td>
                          <td>{{ $item->section_name ?? '—' }}</td>
                          <td>{{ $item->session->session_name ?? '—' }}</td>
                          <td>
                            @if ($item->status == 1)
                              <span class="badge bg-success">Active</span>
                            @else
                              <span class="badge bg-warning text-dark">Inactive</span>
                            @endif
                          </td>
                          <td>
                            <div class="d-flex gap-1">
                              <a href="{{ route('class_edit', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                              <button class="btn btn-sm btn-outline-danger" onclick="deactivateClass({{ $item->id }})" title="Deactivate"><i class="bi bi-journal-x"></i></button>
                              <form id="class-delete-{{ $item->id }}" action="{{ route('class_destroy', $item->id) }}" method="POST" class="d-none">@method('DELETE') @csrf</form>
                            </div>
                          </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No active classes.</td></tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- INACTIVE -->
                <div class="tab-pane fade" id="inactive-classes" role="tabpanel">
                  <div class="table-responsive">
                    <table class="table table-hover datatable align-middle">
                      <thead class="table-light">
                        <tr><th>#</th><th>Class Name</th><th>Section</th><th>Session</th><th>Deleted At</th><th>Actions</th></tr>
                      </thead>
                      <tbody>
                        @forelse ($trashed as $i => $item)
                        <tr id="trashed-class-{{ $item->id }}">
                          <td>{{ $i + 1 }}</td>
                          <td class="text-muted"><strong>{{ $item->class_name }}</strong></td>
                          <td>{{ $item->section_name ?? '—' }}</td>
                          <td>{{ $item->session->session_name ?? '—' }}</td>
                          <td><small class="text-muted">{{ $item->deleted_at->format('d M Y') }}</small></td>
                          <td>
                            <div class="d-flex gap-1">
                              <button class="btn btn-sm btn-outline-success" onclick="restoreClass({{ $item->id }})"><i class="bi bi-arrow-counterclockwise"></i> Restore</button>
                              <button class="btn btn-sm btn-outline-danger" onclick="forceDeleteClass({{ $item->id }})"><i class="bi bi-trash-fill"></i></button>
                            </div>
                          </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No inactive classes.</td></tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
</main>
@endsection

@section('script')
<script>
const CSRF = '{{ csrf_token() }}';
function deactivateClass(id) {
  if (!confirm('Move this class to inactive?')) return;
  document.getElementById('class-delete-' + id).submit();
}
function restoreClass(id) {
  $.ajax({
    url: '/class/' + id + '/restore', method: 'POST', data: { _token: CSRF },
    success: function(res) { if(res.success){ toastr.success(res.message); setTimeout(()=>location.reload(),1000); } },
    error: function() { toastr.error('Something went wrong'); }
  });
}
function forceDeleteClass(id) {
  if (!confirm('Permanently delete this class?')) return;
  $.ajax({
    url: '/class/' + id + '/force-delete', method: 'POST', data: { _token: CSRF, _method: 'DELETE' },
    success: function(res) { if(res.success){ toastr.success(res.message); $('#trashed-class-'+id).fadeOut(); } },
    error: function() { toastr.error('Something went wrong'); }
  });
}
</script>
@endsection
