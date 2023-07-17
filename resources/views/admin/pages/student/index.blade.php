@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
      <h1>All Students</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Data</li>
        </ol>
      </nav>
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
             
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Student Name</th>
                    <th scope="col">Father Name</th>
                    <th scope="col">Contact No</th>
                    <th scope="col">Class</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                @php
                    $i=1;
                @endphp
                <tbody>
                    @foreach ($student as $item)
                    <tr>
                        <th >{{ $i++ }}</th>
                        <td>{{ $item->student_name }}</td>
                        <td>{{ $item->father_name ?? '' }}</td>
                        <td>{{ $item->contact_no ?? '' }}</td>
                        <td>{{ $item->class_room_id ?? ''}}</td>
                        <td >
                          <div class="btn-group">
                           <form action="{{route('Student.destroy', $item)}}" method="post">
                            @method('delete')
                            @csrf
                          <button type="submit"><i class="bi bi-trash-fill"></i></button>
                          </form>
                          
                          <a href="{{route('Student.edit', $item)}}"> <i class="bi bi-pencil-fill"></i></a>
                          <a> <i class="bi bi-eye-fill"></i></a>
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