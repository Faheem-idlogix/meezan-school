@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
      <h1>Create Fee Voucher</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item active">Elements</li>
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
              <h5 class="card-title">Generate Class fee Voucher</h5>

              <!-- General Form Elements -->
              <form  action="{{ route('store_fee_voucher') }}" method="post">
                @csrf
                <div class="row mb-3">
                  <div class="col-lg-6">
                    <label for="inputDate" class="col-form-label">Student Class<sup>*</sup></label>
                    <select class="form-select @error('class_room_id') is-invalid @enderror" value="{{ old('class_room_id') }}"  name="class_room_id"  aria-label="Default select example">
                      @error('class_room_id')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                        @foreach($class_room as $classroom)
                          <option value="{{$classroom->id}}">{{$classroom->class_name .' '. $classroom->section_name}}</option>
                        @endforeach
                      </select>
                    </div>

                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Enter Academic Fee</label>
                      <input type="text" name="academic_fee" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Academic Fee"  class="form-control">
                    </div>
                </div>


                <div class="row mb-3">
                  <div class="col-lg-12">
                    <label for="months" class="col-form-label">Voucher Month(s) <sup>*</sup>
                      <small class="text-muted">— hold Ctrl / Cmd to pick multiple (e.g. summer voucher for June, July, August)</small>
                    </label>
                    <select name="months[]" id="months" class="form-select js-months-multi @error('months') is-invalid @enderror" multiple required>
                      @php
                        $monthOptions = [];
                        $startMonth = \Carbon\Carbon::now()->subMonths(6)->startOfMonth();
                        for ($i = 0; $i < 24; $i++) {
                            $m = $startMonth->copy()->addMonths($i);
                            $monthOptions[$m->format('F Y')] = $m->format('F Y');
                        }
                        $defaultMonth = \Carbon\Carbon::now()->format('F Y');
                      @endphp
                      @foreach($monthOptions as $val => $label)
                        <option value="{{ $val }}" @selected($val === $defaultMonth)>{{ $label }}</option>
                      @endforeach
                    </select>
                    @error('months')
                        <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Voucher Issue Date</label>
                    <input type="date" name="issue_date" class="form-control" value="{{ now()->toDateString() }}">
                  </div>
                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Voucher Submit Date</label>
                    <input type="date" name="submit_date"  class="form-control">
                  </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-6">
                      <label for="inputText" class="col-form-label">Enter Stationery Charges</label>
                        <input type="text" name="stationery_charges" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Stationery Charges"  class="form-control">
                      </div>
                      <div class="col-lg-6">
                        <label for="inputText" class="col-form-label">Enter Arrears Charges</label>
                          <input type="text" name="arrears" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Arrears Charges"  class="form-control">
                        </div>
               </div>

              <div class="row mb-3">
                <div class="col-lg-6">
                  <label for="inputText" class="col-form-label">Enter Test Charges</label>
                    <input type="text" name="test_series_charges" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Test Charges"  class="form-control">
                  </div>
                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Enter Exam Charges</label>
                      <input type="text" name="exam_charges" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Exam Charges"  class="form-control">
                    </div>
                </div>

              <div class="row mb-3">
                <div class="col-lg-6">
                  <label for="inputText" class="col-form-label">Enter Note Book / Dairy Charges</label>
                    <input type="text" name="notebook_charges" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Test Charges"  class="form-control">
                  </div>
                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Enter Book Charges</label>
                      <input type="text" name="book_charges" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Exam Charges"  class="form-control">
                    </div>
              </div>

           <div class="row mb-3">
            <div class="col-lg-6">
              <label for="inputText" class="col-form-label">Enter Fine</label>
                <input type="text" name="fine" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Fine"  class="form-control">
              </div>

              <div class="col-lg-6">
                <label for="inputText" class="col-form-label">Enter any Note</label>
                  <input type="text" name="note" placeholder="Enter Note"  class="form-control">
                </div>
            
       </div>
              
            

          

                <div class="row mb-3">
                 
                  <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary">Create</button>
                  </div>
                </div>

              </form><!-- End General Form Elements -->

            </div>
          </div>

        </div>
      </div>
    </section>
</main>
@endsection

@section('script')
<script>
$(document).ready(function() {
    $('.js-example-basic-multiple').select2();
    $('.js-months-multi').select2({
        placeholder: 'Select one or more months',
        width: '100%'
    });
});


  </script>

@endsection
