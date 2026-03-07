@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
      <h1>Change Password</h1>
      <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('profile.index') }}">Profile</a></li>
        <li class="breadcrumb-item active">Change Password</li>
      </ol></nav>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card shadow-sm border-0">
            <div class="card-body pt-4">
              <div class="text-center mb-4">
                <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3"
                     style="width:64px;height:64px;">
                  <i class="bi bi-key fs-2 text-primary"></i>
                </div>
                <h5>Update Your Password</h5>
                <p class="text-muted small">Please enter your current password and choose a new one.</p>
              </div>

              @if(session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
              @endif

              @if(session('error'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
              @endif

              <form action="{{ route('profile.update-password') }}" method="POST">
                @csrf

                <div class="mb-3">
                  <label class="form-label">Current Password <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="current_password" id="current_password"
                           class="form-control @error('current_password') is-invalid @enderror"
                           placeholder="Enter current password" required>
                    <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="current_password">
                      <i class="bi bi-eye"></i>
                    </button>
                    @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">New Password <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                    <input type="password" name="password" id="new_password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Enter new password" required minlength="8">
                    <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="new_password">
                      <i class="bi bi-eye"></i>
                    </button>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                  <div class="form-text">Minimum 8 characters.</div>
                </div>

                <div class="mb-4">
                  <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                    <input type="password" name="password_confirmation" id="confirm_password"
                           class="form-control" placeholder="Confirm new password" required>
                    <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="confirm_password">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                </div>

                <div class="d-flex justify-content-between">
                  <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Profile
                  </a>
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Update Password
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
</main>
@endsection

@section('scripts')
<script>
  document.querySelectorAll('.toggle-pw').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.target);
      const icon = btn.querySelector('i');
      if(input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
      }
    });
  });
</script>
@endsection
