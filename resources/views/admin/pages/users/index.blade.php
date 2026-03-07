@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>User Management</h1>
        <nav><ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item active">Users</li>
        </ol></nav>
      </div>
      <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Add User
      </a>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card shadow-sm border-0">
            <div class="card-body">

              <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#active-users" type="button">
                    <i class="bi bi-person-check me-1"></i> Active
                    <span class="badge rounded-pill bg-success ms-1">{{ $users->count() }}</span>
                  </button>
                </li>
                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#inactive-users" type="button">
                    <i class="bi bi-person-dash me-1"></i> Inactive
                    <span class="badge rounded-pill bg-danger ms-1">{{ $trashed->count() }}</span>
                  </button>
                </li>
              </ul>

              <div class="tab-content">

                <!-- ACTIVE -->
                <div class="tab-pane fade show active" id="active-users" role="tabpanel">
                  <table class="table table-hover datatable align-middle">
                    <thead class="table-light">
                      <tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                      @forelse ($users as $i => $user)
                      <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                          <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                              style="width:36px;height:36px;font-size:14px;background:{{ ['admin'=>'#6f42c1','teacher'=>'#0d6efd','student'=>'#198754','accountant'=>'#fd7e14'][$user->role ?? 'admin'] ?? '#6c757d' }}">
                              {{ strtoupper(substr($user->name,0,1)) }}
                            </div>
                            <span>{{ $user->name }}</span>
                          </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                          @php $roleColors = ['admin'=>'bg-purple','teacher'=>'bg-primary','student'=>'bg-success','accountant'=>'bg-warning text-dark','super_admin'=>'bg-danger','receptionist'=>'bg-info']; @endphp
                          @foreach($user->roles as $r)
                            <span class="badge {{ $roleColors[$r->name] ?? 'bg-secondary' }}">
                              {{ $r->display_name }}
                            </span>
                          @endforeach
                          @if($user->roles->isEmpty())
                            <span class="badge bg-secondary">{{ ucfirst($user->role ?? 'none') }}</span>
                          @endif
                        </td>
                        <td>
                          <div class="d-flex gap-1">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-fill"></i></a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Deactivate this user?')">
                              @method('DELETE') @csrf
                              <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-person-dash"></i></button>
                            </form>
                            @endif
                          </div>
                        </td>
                      </tr>
                      @empty
                      <tr><td colspan="5" class="text-center text-muted py-4">No users found.</td></tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>

                <!-- INACTIVE -->
                <div class="tab-pane fade" id="inactive-users" role="tabpanel">
                  <table class="table table-hover datatable align-middle">
                    <thead class="table-light">
                      <tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Deleted At</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                      @forelse ($trashed as $i => $user)
                      <tr id="trashed-user-{{ $user->id }}">
                        <td>{{ $i + 1 }}</td>
                        <td class="text-muted">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($user->role ?? 'admin') }}</span></td>
                        <td><small>{{ $user->deleted_at->format('d M Y') }}</small></td>
                        <td>
                          <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-success" onclick="restoreUser({{ $user->id }})"><i class="bi bi-arrow-counterclockwise"></i> Restore</button>
                            <button class="btn btn-sm btn-outline-danger" onclick="forceDeleteUser({{ $user->id }})"><i class="bi bi-trash-fill"></i></button>
                          </div>
                        </td>
                      </tr>
                      @empty
                      <tr><td colspan="6" class="text-center text-muted py-4">No inactive users.</td></tr>
                      @endforelse
                    </tbody>
                  </table>
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
function restoreUser(id) {
  $.ajax({
    url: '/users/' + id + '/restore', method: 'POST', data: { _token: CSRF },
    success: function(res) { if(res.success){ toastr.success(res.message); setTimeout(()=>location.reload(),1000); } },
    error: function() { toastr.error('Something went wrong'); }
  });
}
function forceDeleteUser(id) {
  if (!confirm('Permanently delete this user?')) return;
  $.ajax({
    url: '/users/' + id + '/force-delete', method: 'POST', data: { _token: CSRF, _method: 'DELETE' },
    success: function(res) { if(res.success){ toastr.success(res.message); $('#trashed-user-'+id).fadeOut(); } },
    error: function() { toastr.error('Something went wrong'); }
  });
}
</script>
@endsection
