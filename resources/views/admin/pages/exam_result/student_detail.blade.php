@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="row col-lg-12 pagetitle">
        <div class="col-lg-8">
            <h1>Student Exam Detail</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('exam_result.index') }}">Exam Results</a></li>
                    <li class="breadcrumb-item active">Student Detail</li>
                </ol>
            </nav>
        </div>
        <div class="col-lg-4 text-end">
            <a href="{{ route('exam_result.index') }}" class="btn btn-outline-secondary me-1"><i class="bi bi-arrow-left me-1"></i>Back</a>
            @if($results->first())
            <a href="{{ route('exam_result.show', $results->first()->id) }}" class="btn btn-outline-info" target="_blank"><i class="bi bi-printer me-1"></i>Print Report</a>
            @endif
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                @if (session('success'))
                <div class="alert alert-success alert-dismissible border-0 fade show" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    {{ session('success') }}
                </div>
                @endif

                {{-- Student Info Card --}}
                <div class="card mb-3">
                    <div class="card-body pt-3 pb-2">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="text-muted small text-uppercase fw-semibold">Student</div>
                                <div class="fw-bold fs-5">{{ $student->student_name ?? '' }}</div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-muted small text-uppercase fw-semibold">Class</div>
                                <div class="fw-semibold">{{ $classRoom->class_name ?? '' }}</div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-muted small text-uppercase fw-semibold">Exam</div>
                                <div class="fw-semibold">{{ $exam->name ?? '' }}</div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-muted small text-uppercase fw-semibold">Overall</div>
                                <div class="fw-bold">
                                    {{ $totalObt }} / {{ $totalMax }}
                                    <span class="ms-1 badge {{ $percentage >= 80 ? 'bg-success' : ($percentage >= 60 ? 'bg-primary' : ($percentage >= 50 ? 'bg-warning text-dark' : 'bg-danger')) }}">
                                        {{ $percentage }}%
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3 text-end">
                                <span class="badge fs-6 {{ $percentage >= 80 ? 'bg-success' : ($percentage >= 60 ? 'bg-primary' : ($percentage >= 50 ? 'bg-warning text-dark' : 'bg-danger')) }}">
                                    Grade: {{ $overallGrade }}
                                </span>
                                <div class="text-muted small mt-1">{{ $overallRemark }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Subjects Table --}}
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">All Subjects</h5>

                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Subject</th>
                                    <th class="text-center">Total Marks</th>
                                    <th class="text-center">Obtained Marks</th>
                                    <th class="text-center">Percentage</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $sr = 1; @endphp
                                @foreach ($results as $result)
                                <tr>
                                    <td class="fw-semibold">{{ $sr++ }}</td>
                                    <td class="fw-semibold">{{ $result->subject->subject_name ?? '' }}</td>
                                    <td class="text-center">{{ $result->total_marks }}</td>
                                    <td class="text-center fw-bold">{{ $result->obtained_marks }}</td>
                                    <td class="text-center">
                                        <div class="progress" style="height:8px;min-width:60px">
                                            <div class="progress-bar {{ $result->percentage >= 80 ? 'bg-success' : ($result->percentage >= 60 ? 'bg-primary' : ($result->percentage >= 50 ? 'bg-warning' : 'bg-danger')) }}"
                                                 style="width:{{ $result->percentage }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $result->percentage }}%</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $result->percentage >= 80 ? 'bg-success' : ($result->percentage >= 60 ? 'bg-primary' : ($result->percentage >= 50 ? 'bg-warning text-dark' : 'bg-danger')) }}">
                                            {{ $result->grade }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($result->percentage >= 50)
                                            <span class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Pass</span>
                                        @else
                                            <span class="text-danger fw-semibold"><i class="bi bi-x-circle-fill me-1"></i>Fail</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('exam_result.edit', $result->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Marks">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                            <form action="{{ route('exam_result.destroy', $result->id) }}" method="POST" onsubmit="return confirm('Delete this subject result?')">
                                                @method('DELETE') @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td colspan="2">Total</td>
                                    <td class="text-center">{{ $totalMax }}</td>
                                    <td class="text-center">{{ $totalObt }}</td>
                                    <td class="text-center">{{ $percentage }}%</td>
                                    <td class="text-center">
                                        <span class="badge {{ $percentage >= 80 ? 'bg-success' : ($percentage >= 60 ? 'bg-primary' : ($percentage >= 50 ? 'bg-warning text-dark' : 'bg-danger')) }}">
                                            {{ $overallGrade }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ $overallRemark }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </section>
</main>
@endsection
