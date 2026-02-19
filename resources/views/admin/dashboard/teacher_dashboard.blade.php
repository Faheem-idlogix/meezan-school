@extends('admin.layout.master')
@section('title', 'Teacher Dashboard')

@section('content')
<div class="pagetitle mb-4">
    <h1 class="fw-bold">Welcome, {{ auth()->user()->name }} 👋</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Teacher Dashboard</li>
        </ol>
    </nav>
</div>

<section class="section">
    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-xxl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:60px;height:60px;background:rgba(13,110,253,.1);">
                        <i class="bi bi-people-fill text-primary fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Students</div>
                        <div class="fw-bold fs-4">{{ $totalStudents }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:60px;height:60px;background:rgba(25,135,84,.1);">
                        <i class="bi bi-journal-richtext text-success fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Exams</div>
                        <div class="fw-bold fs-4">{{ $totalExams }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:60px;height:60px;background:rgba(255,193,7,.1);">
                        <i class="bi bi-building text-warning fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Classes</div>
                        <div class="fw-bold fs-4">{{ $classrooms }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 p-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:60px;height:60px;background:rgba(13,202,240,.1);">
                        <i class="bi bi-megaphone text-info fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Notices</div>
                        <div class="fw-bold fs-4">{{ $recentNotices->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <a href="{{ route('attendance') }}" class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center gap-2">
                                <i class="bi bi-calendar-check fs-3"></i>
                                <span class="small fw-semibold">Mark Attendance</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('exam.index') }}" class="btn btn-outline-success w-100 py-3 d-flex flex-column align-items-center gap-2">
                                <i class="bi bi-file-earmark-text fs-3"></i>
                                <span class="small fw-semibold">View Exams</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('exam_result.index') }}" class="btn btn-outline-info w-100 py-3 d-flex flex-column align-items-center gap-2">
                                <i class="bi bi-bar-chart fs-3"></i>
                                <span class="small fw-semibold">Exam Results</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('student.index') }}" class="btn btn-outline-secondary w-100 py-3 d-flex flex-column align-items-center gap-2">
                                <i class="bi bi-people fs-3"></i>
                                <span class="small fw-semibold">View Students</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Notices --}}
    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-megaphone me-2 text-primary"></i>Recent Announcements</h6>
                    <a href="{{ route('notice.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentNotices as $notice)
                    <div class="d-flex align-items-start gap-3 p-3 border-bottom">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:40px;height:40px;background:rgba(13,110,253,.1);">
                            <i class="bi bi-megaphone-fill text-primary"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $notice->title }}</div>
                            <div class="text-muted small">{{ Str::limit($notice->content, 80) }}</div>
                            <div class="text-muted small mt-1">
                                <i class="bi bi-clock me-1"></i>{{ $notice->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-megaphone fs-1 mb-2 d-block"></i>
                        No recent announcements
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
