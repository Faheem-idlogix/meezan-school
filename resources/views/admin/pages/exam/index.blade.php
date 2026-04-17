@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="row col-lg-12 pagetitle">
        <div class="col-lg-10">
      <h1>All Exams</h1>
        <nav>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
              <li class="breadcrumb-item active">Exams</li>
            </ol>
            </nav>
    </div>
      <div class="col-lg-2">
        <a href="{{ route('exam.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Exam</a>

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

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Exams</h5>

              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Exam Name</th>
                    <th scope="col">Exam Date</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                @php
                $sr_no = 1;
                @endphp
                <tbody>
                  @foreach ($exams as $item)
                  <tr>
                    <th>{{ $sr_no++ }}</th>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->date ? $item->date->format('d M Y') : '—' }}</td>
                    <td>
                      <div class="d-flex gap-1">
                        <a href="{{ route('exam-schedules.index', ['exam_id' => $item->id]) }}" class="btn btn-sm btn-outline-info" title="View Schedule"><i class="bi bi-calendar-event"></i></a>
                        <a href="{{ route('exam.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                        <form action="{{ route('exam.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete this exam?')">
                          @method('DELETE') @csrf
                          <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash-fill"></i></button>
                        </form>
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