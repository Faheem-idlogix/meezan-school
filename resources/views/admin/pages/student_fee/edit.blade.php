@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
      <h1>Edit Fee Voucher</h1>
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

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Edit Student fee Voucher</h5>

              <!-- General Form Elements -->
              <form  action="{{ route('student_fee_updated', $studentFee->student_fee_id) }}" method="post">
                @csrf

                <div class="row mb-3">
                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Student Name</label>
                      <input type="text" value="{{$studentFee->student->student_name}}"  name="student_name" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Stationery Charges"  class="form-control" disabled >
                    </div>
                    <div class="col-lg-6">
                      <label for="status" class="col-form-label">Status</label>
                      <select name="status" class="form-control">
                          <option value="paid" {{ $studentFee->status === 'paid' ? 'selected' : '' }}>Paid</option>
                          <option value="unpaid" {{ $studentFee->status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                      </select>
                  </div>
                 </div>

                <div class="row mb-3">
                  <div class="col-lg-6">
                    <label for="inputDate" class="col-form-label">Student Class<sup>*</sup></label>
                    <input type="text" value="{{$studentFee->student->classroom->class_name}}"  name="student_class" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Stationery Charges"  class="form-control" disabled>

                    </div>

                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Enter Academic Fee</label>
                      <input type="text" name="academic_fee" value="{{$studentFee->academic_fee}}" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Academic Fee"  class="form-control">
                    </div>
                </div>


                <div class="row mb-3">

                <div class="col-lg-6">
                  <label for="inputText" class="col-form-label">Voucher Issue date Date</label>
                  <input type="date" value="{{ $studentFee->issue_date ? date('Y-m-d', strtotime($studentFee->issue_date)) : '' }}" name="issue_date" class="form-control">
                </div>
                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Voucher Submit Date</label>
                      <input type="date" value="{{ $studentFee->submit_date ? date('Y-m-d', strtotime($studentFee->submit_date)) : '' }}"  name="submit_date"  class="form-control">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-6">
                      <label for="inputText" class="col-form-label">Enter Stationery Charges</label>
                        <input type="text" value="{{$studentFee->stationery_charges}}"  name="stationery_charges" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Stationery Charges"  class="form-control">
                      </div>
                      <div class="col-lg-6">
                        <label for="inputText" class="col-form-label">Enter Arrears Charges</label>
                          <input type="text"  value="{{$studentFee->arrears}}"  name="arrears" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Arrears Charges"  class="form-control">
                        </div>
               </div>

               <div class="row mb-3">
                <div class="col-lg-6">
                  <label for="inputText" class="col-form-label">Enter Test Charges</label>
                    <input type="text" value="{{$studentFee->test_series_charges}}"  name="test_series_charges" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Test Charges"  class="form-control">
                  </div>
                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Enter Exam Charges</label>
                      <input type="text" value="{{$studentFee->exam_charges}}" name="exam_charges" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Exam Charges"  class="form-control">
                    </div>
           </div>

           <div class="row mb-3">
            <div class="col-lg-6">
              <label for="inputText" class="col-form-label">Enter Fine</label>
                <input type="text" value="{{$studentFee->fine}}" name="fine" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Fine"  class="form-control">
              </div>

              <div class="col-lg-6">
                <label for="inputText" class="col-form-label">Enter any Note</label>
                  <input type="text" value="{{$studentFee->note}}" name="note" placeholder="Enter Note"  class="form-control">
                </div>
            
       </div>
              
            

          

                <div class="row mb-3">
                 
                  <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary">Update</button>
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
});


  </script>

@endsection
