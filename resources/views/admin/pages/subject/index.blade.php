@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="row col-lg-12 pagetitle">
        <div class="col-lg-10">
      <h1>All Subjects</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Subjects</li>
        </ol>
      </nav>
    </div>
      <div class="col-lg-2">
        <a href="{{ route('subject.create') }}" class="btn btn-primary">Add Subject</a>
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
              <h5 class="card-title">Subjects</h5>

              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Subject Code</th>
                    <th scope="col">Subject Name</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                @php
                $sr_no = 1;
                @endphp
                <tbody>
                  @foreach ($subject as $item)
                  <tr>
                    <th>{{ $sr_no++ }}</th>
                    <td>{{ $item->subject_code }}</td>
                    <td>{{ $item->subject_name }}</td>
                    <td>
                    <div class="btn-group">
                      <form action="{{route('subject.destroy', $item)}}" method="post">
                       @method('delete')
                       @csrf
                     <button type="submit"><i class="bi bi-trash-fill"></i></button>
                     </form>
                     
                     <a href="{{route('subject.edit', $item)}}"> <i class="bi bi-pencil-fill"></i></a>
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

  </main><!-- End #main -->
@endsection
