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
        <a href="{{ route('exam_result.create') }}" class="btn btn-primary">Add Exam Result</a>

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
              <h5 class="card-title">Exam Results</h5>

              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Student Name</th>
                    <th scope="col">Class Name</th>
                    <th scope="col">Exam Name</th>
                    <th scope="col">Subject Name</th>
                    <th scope="col">Marks Obtained</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                @php
                $sr_no = 1;
                @endphp
                <tbody>
                  @foreach ($exam_results as $item)
                  <tr>
                    <th>{{ $sr_no++ }}</th>
                    <td>{{ $item->student->student_name }}</td>
                    <td>{{ $item->classRoom->class_name }}</td>
                    <td>{{ $item->exam->name }}</td>
                    <td>{{ $item->subject->subject_name }}</td>
                    <td>{{ $item->obtained_marks }}</td>
                    <td>
                    <div class="btn-group">
                      <form action="{{route('exam_result.destroy', $item)}}" method="post">
                       @method('delete')
                       @csrf
                     <button type="submit"><i class="bi bi-trash-fill"></i></button>
                     </form>
                     <a href="{{ route('exam_result.edit', $item) }}" ><i class="bi bi-pencil-fill"></i></a> 
                     <a href="{{ route('exam_result.show', $item) }}" target="_blank"><i class="bi bi-printer"></i></a>
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