@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="row col-lg-12 pagetitle">
        <div class="col-lg-10">
      <h1>Session Data</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Session</li>
        </ol>
      </nav>
    </div>
      <div class="col-lg-2">
        <a href="{{ route('Session.create') }}" class="btn btn-primary">Add Session</a>
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
              <h5 class="card-title">Sessions</h5>

              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Session Name</th>
                    <th scope="col">Status</th>
                    <th scope="col">Start Date</th>
                    <th scope="col">End Date</th>
                    <th scope="col">Action</th>

                  </tr>
                </thead>
                @php
                $sr_no = 1 ;
                @endphp
                <tbody>
                  @foreach ($session as $item)
                  <tr>
                    <th >{{ $sr_no++ }}</th>
                    <td>{{ $item->session_name }}</td>
                    <td>
                      @if ($item->status == 1)
                      active
                     @else
                      non active
                     @endif
                    </td>
                    <td>{{ $item->start_date }}</td>
                    <td>{{ $item->end_date }}</td>
                    <td>  
                      <div class="btn-group">
                      <form action="{{route('Session.destroy', $item)}}" method="post">
                       @method('delete')
                       @csrf
                     <button type="submit"><i class="bi bi-trash-fill"></i></button>
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

  </main><!-- End #main -->
@endsection
