@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="row col-lg-12 pagetitle">
        <div class="col-lg-10">
      <h1>Class Voucher Data</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Class Vouchers</li>
        </ol>
      </nav>
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
              <h5 class="card-title">Voucher</h5>

              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Month</th>
                    <th scope="col">Class</th>
                    <th scope="col">Fee</th>

                    <th scope="col">Action</th>

                  </tr>
                </thead>
                @php
                $sr_no = 1 ;
                @endphp
                <tbody>
                  @foreach ($studentFee as $item)
                  <tr>
                    <th >{{ $sr_no++ }}</th>
                    <td>{{ $item->student->student_name }}</td>
                    <td>{{ $item->fee_month }}</td>
                    <td>{{ $item->student->classroom->class_name ?? '' }}</td>
                    <td>{{ $item->total_fee }}</td>

                  
                    <td>  
                      <div class="btn-group">
                      <form action="{{route('class_destroy', $item->class_fee_voucher_id)}}" method="post">
                       @method('delete')
                       @csrf
                     <button type="submit"><i class="bi bi-trash-fill"></i></button>
                     </form>
                     <a href="{{route('student_fee_edit', $item->student_fee_id)}}"> <i class="bi bi-pencil-fill"></i></a>
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
