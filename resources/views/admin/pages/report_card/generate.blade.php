@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Generate Report Cards</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li><li class="breadcrumb-item active">Generate Report Card</li></ol></nav>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if(session('error'))<div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    <section class="section">
        <div class="row">
            {{-- Generate Single Report Card --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body pt-4">
                        <h5 class="card-title"><i class="bi bi-person me-1"></i>Single Student Report Card</h5>
                        <form action="{{ route('report-cards.pdf') }}" method="GET" target="_blank">
                            <div class="mb-3">
                                <label class="form-label">Select Exam <span class="text-danger">*</span></label>
                                <select name="exam_id" class="form-select" required>
                                    <option value="">-- Select Exam --</option>
                                    @foreach($exams as $exam)
                                    <option value="{{ $exam->id }}">{{ $exam->exam_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Select Student <span class="text-danger">*</span></label>
                                <select name="student_id" class="form-select" required>
                                    <option value="">-- Select Student --</option>
                                    @foreach($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->student_name }} - {{ $student->father_name }} ({{ $student->classroom->class_name ?? '' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-file-pdf me-1"></i>Generate PDF</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Bulk Actions --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body pt-4">
                        <h5 class="card-title"><i class="bi bi-calculator me-1"></i>Calculate Grades & Positions</h5>
                        <form action="{{ route('report-cards.calculate-grades') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Select Exam <span class="text-danger">*</span></label>
                                <select name="exam_id" class="form-select" required>
                                    <option value="">-- Select Exam --</option>
                                    @foreach($exams as $exam)
                                    <option value="{{ $exam->id }}">{{ $exam->exam_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-1"></i>This will calculate grades, percentages, and class/subject positions for all results in the selected exam.
                            </div>
                            <button type="submit" class="btn btn-success"><i class="bi bi-calculator me-1"></i>Calculate Now</button>
                        </form>

                        <hr>

                        <h5 class="card-title"><i class="bi bi-check2-all me-1"></i>Approve Results</h5>
                        <form action="{{ route('report-cards.approve-results') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Select Exam <span class="text-danger">*</span></label>
                                <select name="exam_id" class="form-select" required>
                                    <option value="">-- Select Exam --</option>
                                    @foreach($exams as $exam)
                                    <option value="{{ $exam->id }}">{{ $exam->exam_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-warning"><i class="bi bi-check2-all me-1"></i>Approve All Results</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection