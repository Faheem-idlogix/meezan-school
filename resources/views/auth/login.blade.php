@extends('admin.layout.app')
@section('content')
<main>
  <div class="container-fluid" style="min-height:100vh;background:linear-gradient(135deg,#012970 0%,#4154f1 100%);display:flex;align-items:center;justify-content:center;">
    <div class="row w-100 justify-content-center px-3">
      <div class="col-xl-4 col-lg-5 col-md-7 col-sm-10">

        {{-- Logo --}}
        <div class="text-center mb-4">
          <div class="d-inline-flex align-items-center gap-2 mb-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center"
                 style="width:48px;height:48px;background:rgba(255,255,255,.15);">
              <i class="bi bi-mortarboard-fill text-white fs-4"></i>
            </div>
            <span class="fw-bold text-white fs-4">{{ setting('school_name', 'School') }}</span>
          </div>
          <p class="text-white-50 small mb-0">{{ setting('school_tagline', 'School Management System') }}</p>
        </div>

        {{-- Card --}}
        <div class="card border-0 shadow-lg" style="border-radius:12px;">
          <div class="card-body p-4 p-md-5">

            <div class="text-center mb-4">
              <h4 class="fw-bold mb-1" style="color:#012970;">Welcome Back!</h4>
              <p class="text-muted small">Sign in to your account to continue</p>
            </div>

            <form method="POST" action="{{ route('login') }}" novalidate>
              @csrf

              <div class="mb-3">
                <label class="form-label fw-semibold" style="color:#012970;">Email Address</label>
                <div class="input-group">
                  <span class="input-group-text" style="background:#f6f9ff;border-color:#dee2e6;"><i class="bi bi-envelope" style="color:#4154f1;"></i></span>
                  <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}" placeholder="admin@school.com" autofocus>
                  @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label fw-semibold" style="color:#012970;">Password</label>
                <div class="input-group">
                  <span class="input-group-text" style="background:#f6f9ff;border-color:#dee2e6;"><i class="bi bi-lock" style="color:#4154f1;"></i></span>
                  <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    id="yourPassword" placeholder="Enter your password">
                  <span class="input-group-text" id="togglePassword" style="cursor:pointer;background:#f6f9ff;border-color:#dee2e6;">
                    <i class="bi bi-eye-slash" id="toggleIcon" style="color:#4154f1;"></i>
                  </span>
                  @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>

              <button type="submit" class="btn w-100 fw-semibold py-2"
                style="background:linear-gradient(135deg,#4154f1,#717ff5);color:#fff;border:none;border-radius:8px;font-size:.95rem;">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
              </button>

            </form>

          </div>
        </div>

      </div>
    </div>
  </div>
</main>

@endsection

@section('script')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('togglePassword');
    if (!toggle) return;
    toggle.addEventListener('click', function () {
      const passwordInput = document.getElementById('yourPassword');
      const toggleIcon = document.getElementById('toggleIcon');
      if (!passwordInput || !toggleIcon) return;
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
      } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
      }
    });
  });
</script>
@endsection