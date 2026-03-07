@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
      <h1>My Profile</h1>
      <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Profile</li>
      </ol></nav>
    </div>

    <section class="section profile">
      <div class="row">
        {{-- Profile Card --}}
        <div class="col-lg-4">
          <div class="card shadow-sm border-0">
            <div class="card-body text-center pt-4 pb-4">
              @php
                $initials = collect(explode(' ', auth()->user()->name))
                  ->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('');
                $colors = ['#4154f1','#2eca6a','#ff771d','#e74c3c','#9b59b6','#1abc9c','#3498db','#f39c12'];
                $bg = $colors[auth()->id() % count($colors)];
              @endphp
              <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold mx-auto mb-3"
                   style="width:80px;height:80px;font-size:28px;background:{{ $bg }};">
                {{ $initials }}
              </div>
              <h5 class="mb-1">{{ auth()->user()->name }}</h5>
              <p class="text-muted small mb-2">{{ auth()->user()->email }}</p>
              @if(auth()->user()->roles->count())
                @foreach(auth()->user()->roles as $role)
                  <span class="badge bg-primary">{{ $role->display_name }}</span>
                @endforeach
              @else
                <span class="badge bg-secondary">{{ ucfirst(auth()->user()->type ?? 'User') }}</span>
              @endif
            </div>
          </div>

          <div class="card shadow-sm border-0">
            <div class="card-body">
              <h5 class="card-title">Quick Links</h5>
              <a href="{{ route('profile.change-password') }}" class="btn btn-outline-primary btn-sm w-100 mb-2">
                <i class="bi bi-key me-1"></i>Change Password
              </a>
              <a href="{{ route('notifications.my') }}" class="btn btn-outline-info btn-sm w-100">
                <i class="bi bi-bell me-1"></i>My Notifications
              </a>
            </div>
          </div>
        </div>

        {{-- Edit Profile --}}
        <div class="col-lg-8">
          <div class="card shadow-sm border-0">
            <div class="card-body pt-3">
              <h5 class="card-title">Edit Profile</h5>

              @if(session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
              @endif

              <form action="{{ route('profile.update') }}" method="POST">
                @csrf @method('PUT')

                <div class="row mb-3">
                  <label class="col-md-3 col-form-label">Full Name</label>
                  <div class="col-md-9">
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', auth()->user()->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-md-3 col-form-label">Email</label>
                  <div class="col-md-9">
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', auth()->user()->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-md-3 col-form-label">Role</label>
                  <div class="col-md-9">
                    <input type="text" class="form-control" disabled
                           value="{{ auth()->user()->roles->pluck('display_name')->join(', ') ?: ucfirst(auth()->user()->type ?? 'User') }}">
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-md-3 col-form-label">Member Since</label>
                  <div class="col-md-9">
                    <input type="text" class="form-control" disabled
                           value="{{ auth()->user()->created_at->format('d M Y') }}">
                  </div>
                </div>

                <div class="text-end">
                  <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
</main>
@endsection
