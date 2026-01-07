@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="row col-lg-12 pagetitle">
        <div class="col-lg-10">
      <h1>Vouchers</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item active">Vouchers</li>
        </ol>
      </nav>
              </div>

        <div class="col-lg-2">
        <a href="{{ route('voucher.create') }}" class="btn btn-primary">Add Voucher</a>
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
              <h5 class="card-title">Vouchers</h5>

              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Student Name</th>
                    <th scope="col">Voucher Code</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Expiry Date</th>
                    <th scope="col">Items</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                @php
                $sr_no = 1;
                @endphp
                <tbody>
                  @foreach ($vouchers as $voucher)
                  <tr>
                    <th>{{ $sr_no++ }}</th>
                    <td>{{ $voucher->student->student_name }}</td>
                    <td>{{ $voucher->voucher_code }}</td>
                    <td>{{ $voucher->amount }}</td>
                    <td>{{ $voucher->expiry_date }}</td>
                    <td>
                      <ul>
                        @foreach ($voucher->items as $item)
                          <li>{{ $item->item_name }} - {{ $item->item_price }}</li>
                        @endforeach
                      </ul>
                    </td>
                    <td>
                    <div class="btn-group">
                      <form action="{{route('voucher.destroy', $voucher)}}" method="post">
                       @method('delete')
                       @csrf
                     <button type="submit"><i class="bi bi-trash-fill"></i></button>
                     </form>
                     <a href="{{ route('voucher.edit', $voucher->id) }}"><i class="bi bi-pencil-fill"></i></a>
                     <a href="{{ route('voucher.show', $voucher->id) }}"><i class="bi bi-printer"></i></a>
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