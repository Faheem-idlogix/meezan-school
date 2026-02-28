@extends('admin.layout.master')
@section('title', 'Teacher Profile')

@section('content')
<div class="page-title d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2"></i>Teacher Profile</h4>
    <div>
        <a href="{{ route('teacher.edit', $teacher->id) }}" class="btn btn-outline-primary btn-sm me-2">
            <i class="bi bi-pencil-fill me-1"></i> Edit
        </a>
        <a href="{{ route('teacher.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- Profile Card --}}
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 text-center p-4">
            @if($teacher->teacher_image)
                <img src="{{ asset('img/teachers/' . $teacher->teacher_image) }}"
                     class="rounded-circle mx-auto mb-3 shadow"
                     style="width:120px;height:120px;object-fit:cover;" alt="Teacher Photo">
            @else
                <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow"
                     style="width:120px;height:120px;background:linear-gradient(135deg,#667eea,#764ba2);font-size:2.5rem;color:#fff;font-weight:700;">
                    {{ strtoupper(substr($teacher->teacher_name ?? 'T', 0, 1)) }}
                </div>
            @endif
            <h5 class="fw-bold mb-1">{{ $teacher->teacher_name }}</h5>
            <span class="badge bg-primary px-3 py-2 mb-2">{{ $teacher->subject ?? 'Teacher' }}</span>
            <p class="text-muted small mb-1">
                <i class="bi bi-envelope me-1"></i>{{ $teacher->teacher_email ?? 'N/A' }}
            </p>
            <p class="text-muted small mb-1">
                <i class="bi bi-phone me-1"></i>{{ $teacher->teacher_phone ?? 'N/A' }}
            </p>
            @if($teacher->whatsapp_number)
            <p class="text-muted small mb-1">
                <i class="bi bi-whatsapp text-success me-1"></i>{{ $teacher->whatsapp_number }}
            </p>
            @endif
            <hr>
            <div class="d-flex justify-content-center gap-3 text-center">
                <div>
                    <div class="fw-bold fs-5 text-primary">{{ $teacher->experience ?? '—' }}</div>
                    <small class="text-muted">Years Exp.</small>
                </div>
                <div>
                    <div class="fw-bold fs-5 text-success">
                        {{ $teacher->status == 1 ? 'Active' : 'Inactive' }}
                    </div>
                    <small class="text-muted">Status</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Details Card --}}
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2 text-primary"></i>Teacher Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold text-uppercase">Full Name</label>
                        <p class="fw-semibold mb-0">{{ $teacher->teacher_name ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold text-uppercase">Designation</label>
                        <p class="fw-semibold mb-0">{{ $teacher->designation ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold text-uppercase">Email Address</label>
                        <p class="fw-semibold mb-0">{{ $teacher->teacher_email ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold text-uppercase">Phone Number</label>
                        <p class="fw-semibold mb-0">{{ $teacher->teacher_phone ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold text-uppercase">WhatsApp</label>
                        <p class="fw-semibold mb-0">{{ $teacher->whatsapp_number ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold text-uppercase">Subject</label>
                        <p class="fw-semibold mb-0">{{ $teacher->subject ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold text-uppercase">Qualification</label>
                        <p class="fw-semibold mb-0">{{ $teacher->qualification ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold text-uppercase">Experience</label>
                        <p class="fw-semibold mb-0">{{ $teacher->experience ? $teacher->experience . ' Years' : '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold text-uppercase">Date of Joining</label>
                        <p class="fw-semibold mb-0">
                            {{ $teacher->joining_date ? \Carbon\Carbon::parse($teacher->joining_date)->format('d M, Y') : '—' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold text-uppercase">Salary</label>
                        <p class="fw-semibold mb-0">
                            {{ $teacher->salary ? 'PKR ' . number_format($teacher->salary) : '—' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold text-uppercase">CNIC</label>
                        <p class="fw-semibold mb-0">{{ $teacher->cnic ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-semibold text-uppercase">Address</label>
                        <p class="fw-semibold mb-0">{{ $teacher->address ?? '—' }}</p>
                    </div>
                    @if($teacher->bio)
                    <div class="col-12">
                        <label class="text-muted small fw-semibold text-uppercase">Bio / Notes</label>
                        <p class="fw-semibold mb-0">{{ $teacher->bio }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
