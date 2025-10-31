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
              <h5 class="card-title">Create Student fee Voucher</h5>

              <!-- General Form Elements -->
              <form  action="{{ route('store_student_fee') }}" method="post">
                @csrf

                <div class="row mb-3">

                    <div class="col-lg-6">
                        <label for="classroom_id" class="col-form-label">Classroom<sup>*</sup></label>
                        <select name="classroom_id" id="classroom_id" class="form-control" required>
                            <option value="">Select Classroom</option>
                            @foreach($classrooms as $classroom)
                                <option value="{{ $classroom->id }}">{{ $classroom->class_name.'-'.$classroom->section_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-6">
                        <label for="student_id" class="col-form-label">Student<sup>*</sup></label>
                        <select name="student_id" id="student_id" class="form-control" required>
                            <option value="">Select Student</option>
                        </select>
                    </div>


                 
                 </div>

                <div class="row mb-3">
                <div class="col-lg-6">
                      <label for="status" class="col-form-label">Status</label>
                      <select name="status" class="form-control" disabled>
                        <option value="unpaid">Unpaid</option>
                      </select>
                </div>

                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Enter Academic Fee</label>
                      <input type="text" name="academic_fee" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Academic Fee"  class="form-control">
                    </div>
                </div>


                <div class="row mb-3">

                <div class="col-lg-6">
                  <label for="inputText" class="col-form-label">Voucher Issue date Date</label>
                  <input type="date" name="issue_date" class="form-control">
                </div>
                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Voucher Submit Date</label>
                      <input type="date"  name="submit_date"  class="form-control">
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
                    <input type="text"  name="test_series_charges" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Test Charges"  class="form-control">
                  </div>
                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Enter Exam Charges</label>
                      <input type="text" name="exam_charges" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Exam Charges"  class="form-control">
                    </div>
           </div>

              <div class="row mb-3">
                <div class="col-lg-6">
                  <label for="inputText" class="col-form-label">Enter Notebook Charges</label>
                    <input type="text"  name="notebook_charges" pattern="-?[0-9]+$" oninput="validateNumber(this)" placeholder="Enter the Test Charges"  class="form-control">
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
                    <button type="submit" class="btn btn-primary">Submit</button>
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

<script>
$(document).on('change', '#classroom_id', function() {
    let classId = $(this).val();

    // Clear previous student options
    $('#student_id').html('<option value="">Loading...</option>');

    if (classId) {
        $.ajax({
            url: "{{ route('getStudentsByClass') }}", // Route name
            type: "GET",
            data: { class_id: classId },
            success: function(response) {
                $('#student_id').empty().append('<option value="">Select Student</option>');
                $.each(response.students, function(key, student) {
                    $('#student_id').append(
                        `<option value="${student.id}">${student.student_name}</option>`
                    );
                });
            },
            error: function() {
                alert('Error fetching students');
                $('#student_id').html('<option value="">Select Student</option>');
            }
        });
    } else {
        $('#student_id').html('<option value="">Select Student</option>');
    }
});
</script>


@endsection
