@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="row col-lg-12 pagetitle">
        <div class="col-lg-10">
      <h1>All Exam Results</h1>
      <nav>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
              <li class="breadcrumb-item active">Exam Results</li>
            </ol>
            </nav>
        </div>
      <div class="col-lg-2">
        <a href="{{ route('exam_result.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Exam Result</a>

        </div>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          @if (session('success'))
          <div class="alert alert-success alert-dismissible border-0 fade show" role="alert">
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              {{ session('success') }}
          </div>
       @endif

          @if (session('error'))
          <div class="alert alert-danger alert-dismissible border-0 fade show" role="alert">
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              {{ session('error') }}
          </div>
          @endif

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Exam Results — By Student</h5>

              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Student Name</th>
                    <th scope="col">Class</th>
                    <th scope="col">Exam</th>
                    <th scope="col">Subjects</th>
                    <th scope="col">Total / Obtained</th>
                    <th scope="col">Percentage</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                @php $sr_no = 1; @endphp
                <tbody>
                  @foreach ($exam_results as $item)
                  @php
                    $pct = $item->total_marks_sum > 0 ? round(($item->obtained_marks_sum / $item->total_marks_sum) * 100, 1) : 0;
                  @endphp
                  <tr>
                    <th>{{ $sr_no++ }}</th>
                    <td>{{ $item->student->student_name ?? '' }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $item->classRoom->class_name ?? '' }}</span></td>
                    <td>{{ $item->exam->name ?? '' }}</td>
                    <td><span class="badge bg-info text-white">{{ $item->subject_count }} subjects</span></td>
                    <td>
                      <span class="fw-semibold">{{ $item->obtained_marks_sum }}</span>
                      <span class="text-muted"> / {{ $item->total_marks_sum }}</span>
                    </td>
                    <td>
                      @if($pct >= 80)
                        <span class="badge bg-success">{{ $pct }}%</span>
                      @elseif($pct >= 60)
                        <span class="badge bg-primary">{{ $pct }}%</span>
                      @elseif($pct >= 50)
                        <span class="badge bg-warning text-dark">{{ $pct }}%</span>
                      @else
                        <span class="badge bg-danger">{{ $pct }}%</span>
                      @endif
                    </td>
                    <td>
                      <div class="d-flex gap-1">
                        @if($item->student && $item->exam)
                        <a href="{{ route('exam_result.student_detail', ['studentId' => $item->student_id, 'examId' => $item->exam_id]) }}"
                           class="btn btn-sm btn-outline-info" title="View Details">
                          <i class="bi bi-eye-fill me-1"></i>Detail
                        </a>
                        <a href="{{ route('report-cards.pdf', ['student_id' => $item->student_id, 'exam_id' => $item->exam_id]) }}"
                           class="btn btn-sm btn-outline-secondary" title="Print Result Card" target="_blank">
                          <i class="bi bi-printer me-1"></i>Print
                        </a>
                        @else
                        <span class="badge bg-danger">Missing student/exam</span>
                        @endif
                      </div>
                    </td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
              <!-- End Table with stripped rows -->
            </div>
          </div>
        </div>
        </div>
    </section>
</main>
@endsection   