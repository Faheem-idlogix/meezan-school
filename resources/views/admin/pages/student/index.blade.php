@extends('admin.layout.master')
@section('css')
<style>
.status-pill {
  display: inline-block;
  padding: 2px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  color: #fff;
}
</style>
@endsection
@section('content')
<main id="main" class="main">

    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>Students</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Students</li>
          </ol>
        </nav>
      </div>
      <a href="{{ route('student.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Add Student
      </a>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card shadow-sm border-0">
            <div class="card-body">

              <!-- Tabs -->
              <ul class="nav nav-tabs mb-3" id="studentTabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#active-students" type="button" role="tab">
                    <i class="bi bi-person-check me-1"></i> Active
                    <span class="badge rounded-pill bg-success ms-1">{{ $student->count() }}</span>
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#inactive-students" type="button" role="tab">
                    <i class="bi bi-person-dash me-1"></i> Inactive
                    <span class="badge rounded-pill bg-danger ms-1">{{ $trashed->count() }}</span>
                  </button>
                </li>
              </ul>

              <div class="tab-content">

                <!-- ACTIVE -->
                <div class="tab-pane fade show active" id="active-students" role="tabpanel">
                  <div class="table-responsive">
                    <table class="table table-hover datatable align-middle">
                      <thead class="table-light">
                        <tr>
                          <th>#</th><th>Name</th><th>Father</th><th>Contact</th><th>WhatsApp</th><th>Class</th><th>Status</th><th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse ($student as $i => $item)
                        <tr id="student-row-{{ $item->id }}">
                          <td>{{ $i + 1 }}</td>
                          <td>
                            <div class="d-flex align-items-center gap-2">
                              @if($item->student_image)
                                <img src="{{ asset('img/students/'.$item->student_image) }}" class="rounded-circle" width="36" height="36" style="object-fit:cover">
                              @else
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:36px;height:36px;font-size:14px">{{ strtoupper(substr($item->student_name,0,1)) }}</div>
                              @endif
                              <a href="{{ route('student.show', $item) }}" class="text-decoration-none text-primary fw-semibold" title="View Profile">{{ $item->student_name }}</a>
                            </div>
                          </td>
                          <td>{{ $item->father_name ?? '—' }}</td>
                          <td>{{ $item->contact_no ?? '—' }}</td>
                          <td>{{ $item->whatsapp_number ?? '—' }}</td>
                          <td><span class="badge bg-secondary">{{ $item->classroom->class_name ?? '—' }}</span></td>
                          <td><span class="status-pill" style="background:{{ $item->student_status === 'active' ? '#198754' : '#ffc107' }}">{{ ucfirst($item->student_status) }}</span></td>
                          <td>
                            <div class="d-flex gap-1 flex-wrap">
                              <a href="{{ route('student.show', $item) }}" class="btn btn-sm btn-outline-info" title="View Profile"><i class="bi bi-eye-fill"></i></a>
                              <a href="{{ route('student.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                              <button class="btn btn-sm btn-outline-danger" onclick="softDeleteStudent({{ $item->id }})" title="Deactivate"><i class="bi bi-person-dash"></i></button>
                              <form id="delete-form-{{ $item->id }}" action="{{ route('student.destroy', $item) }}" method="POST" class="d-none">@method('DELETE') @csrf</form>
                            </div>
                          </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No active students found.</td></tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- INACTIVE -->
                <div class="tab-pane fade" id="inactive-students" role="tabpanel">
                  <div class="table-responsive">
                    <table class="table table-hover datatable align-middle">
                      <thead class="table-light">
                        <tr><th>#</th><th>Name</th><th>Father</th><th>Contact</th><th>Class</th><th>Deleted At</th><th>Actions</th></tr>
                      </thead>
                      <tbody>
                        @forelse ($trashed as $i => $item)
                        <tr id="trashed-row-{{ $item->id }}">
                          <td>{{ $i + 1 }}</td>
                          <td>
                            <div class="d-flex align-items-center gap-2">
                              <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:36px;height:36px;font-size:14px">{{ strtoupper(substr($item->student_name,0,1)) }}</div>
                              <span class="text-muted">{{ $item->student_name }}</span>
                            </div>
                          </td>
                          <td>{{ $item->father_name ?? '—' }}</td>
                          <td>{{ $item->contact_no ?? '—' }}</td>
                          <td><span class="badge bg-secondary">{{ $item->classroom->class_name ?? '—' }}</span></td>
                          <td><small class="text-muted">{{ $item->deleted_at->format('d M Y') }}</small></td>
                          <td>
                            <div class="d-flex gap-1 flex-wrap">
                              <button class="btn btn-sm btn-outline-success" onclick="restoreStudent({{ $item->id }})"><i class="bi bi-arrow-counterclockwise"></i> Restore</button>
                              <button class="btn btn-sm btn-outline-danger" onclick="forceDeleteStudent({{ $item->id }})"><i class="bi bi-trash-fill"></i></button>
                            </div>
                          </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No inactive students.</td></tr>
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
function softDeleteStudent(id) {
  if (!confirm('Move this student to inactive?')) return;
  document.getElementById('delete-form-' + id).submit();
}
function restoreStudent(id) {
  $.ajax({
    url: '/student/' + id + '/restore', method: 'POST', data: { _token: CSRF },
    success: function(res) { if(res.success){ toastr.success(res.message); setTimeout(()=>location.reload(),1000); } },
    error: function() { toastr.error('Something went wrong'); }
  });
}
function forceDeleteStudent(id) {
  if (!confirm('Permanently delete? This cannot be undone.')) return;
  $.ajax({
    url: '/student/' + id + '/force-delete', method: 'POST', data: { _token: CSRF, _method: 'DELETE' },
    success: function(res) { if(res.success){ toastr.success(res.message); $('#trashed-row-'+id).fadeOut(); } },
    error: function() { toastr.error('Something went wrong'); }
  });
}
</script>
@endsection