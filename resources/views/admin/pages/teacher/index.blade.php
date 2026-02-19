@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>Teachers</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Teachers</li>
          </ol>
        </nav>
      </div>
      <a href="{{ route('teacher.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Add Teacher
      </a>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">

              <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#active-teachers" type="button">
                    <i class="bi bi-person-check me-1"></i> Active
                    <span class="badge rounded-pill bg-success ms-1">{{ $teachers->count() }}</span>
                  </button>
                </li>
                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#inactive-teachers" type="button">
                    <i class="bi bi-person-dash me-1"></i> Inactive
                    <span class="badge rounded-pill bg-danger ms-1">{{ $trashed->count() }}</span>
                  </button>
                </li>
              </ul>

              <div class="tab-content">

                <!-- ACTIVE -->
                <div class="tab-pane fade show active" id="active-teachers" role="tabpanel">
                  <div class="table-responsive">
                    <table class="table table-hover datatable align-middle">
                      <thead class="table-light">
                        <tr><th>#</th><th>Name</th><th>Contact</th><th>WhatsApp</th><th>Gender</th><th>Status</th><th>Actions</th></tr>
                      </thead>
                      <tbody>
                        @forelse ($teachers as $i => $item)
                        <tr id="teacher-row-{{ $item->id }}">
                          <td>{{ $i + 1 }}</td>
                          <td>
                            <div class="d-flex align-items-center gap-2">
                              @if(!empty($item->teacher_image))
                                <img src="{{ asset('img/teachers/'.$item->teacher_image) }}" class="rounded-circle" width="36" height="36" style="object-fit:cover">
                              @else
                                <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center" style="width:36px;height:36px;font-size:14px">{{ strtoupper(substr($item->teacher_name,0,1)) }}</div>
                              @endif
                              <div>
                                <div>{{ $item->teacher_name }}</div>
                                <small class="text-muted">{{ $item->teacher_email }}</small>
                              </div>
                            </div>
                          </td>
                          <td>{{ $item->contact_no ?? '—' }}</td>
                          <td>{{ $item->whatsapp_number ?? '—' }}</td>
                          <td>{{ ucfirst($item->gender) }}</td>
                          <td>
                            <span class="badge {{ $item->teacher_status === 'active' ? 'bg-success' : 'bg-warning text-dark' }}">
                              {{ ucfirst($item->teacher_status) }}
                            </span>
                          </td>
                          <td>
                            <div class="d-flex gap-1">
                              <a href="{{ route('teacher.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                              <button class="btn btn-sm btn-outline-danger" onclick="deactivateTeacher({{ $item->id }})"><i class="bi bi-person-dash"></i></button>
                              <form id="teacher-delete-{{ $item->id }}" action="{{ route('teacher.destroy', $item) }}" method="POST" class="d-none">@method('DELETE') @csrf</form>
                            </div>
                          </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No active teachers found.</td></tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- INACTIVE -->
                <div class="tab-pane fade" id="inactive-teachers" role="tabpanel">
                  <div class="table-responsive">
                    <table class="table table-hover datatable align-middle">
                      <thead class="table-light">
                        <tr><th>#</th><th>Name</th><th>Contact</th><th>Gender</th><th>Deleted At</th><th>Actions</th></tr>
                      </thead>
                      <tbody>
                        @forelse ($trashed as $i => $item)
                        <tr id="trashed-teacher-{{ $item->id }}">
                          <td>{{ $i + 1 }}</td>
                          <td class="text-muted">{{ $item->teacher_name }}</td>
                          <td>{{ $item->contact_no ?? '—' }}</td>
                          <td>{{ ucfirst($item->gender) }}</td>
                          <td><small class="text-muted">{{ $item->deleted_at->format('d M Y') }}</small></td>
                          <td>
                            <div class="d-flex gap-1">
                              <button class="btn btn-sm btn-outline-success" onclick="restoreTeacher({{ $item->id }})"><i class="bi bi-arrow-counterclockwise"></i> Restore</button>
                              <button class="btn btn-sm btn-outline-danger" onclick="forceDeleteTeacher({{ $item->id }})"><i class="bi bi-trash3"></i></button>
                            </div>
                          </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No inactive teachers.</td></tr>
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
function deactivateTeacher(id) {
  if (!confirm('Move this teacher to inactive?')) return;
  document.getElementById('teacher-delete-' + id).submit();
}
function restoreTeacher(id) {
  $.ajax({
    url: '/teacher/' + id + '/restore', method: 'POST', data: { _token: CSRF },
    success: function(res) { if(res.success){ toastr.success(res.message); setTimeout(()=>location.reload(),1000); } },
    error: function() { toastr.error('Something went wrong'); }
  });
}
function forceDeleteTeacher(id) {
  if (!confirm('Permanently delete this teacher?')) return;
  $.ajax({
    url: '/teacher/' + id + '/force-delete', method: 'POST', data: { _token: CSRF, _method: 'DELETE' },
    success: function(res) { if(res.success){ toastr.success(res.message); $('#trashed-teacher-'+id).fadeOut(); } },
    error: function() { toastr.error('Something went wrong'); }
  });
}
</script>
@endsection
