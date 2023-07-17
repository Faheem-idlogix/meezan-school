@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
      <h1>Add Student</h1>
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
              <h5 class="card-title">Student General Information</h5>

              <!-- General Form Elements -->
              <form method="post" action="{{ route('Student.store') }}" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="row mb-3">
                <div class="col-lg-6">
                  <label for="inputText" class="col-form-label ">First Name<sup>*</sup></label>
                    <input type="text" name="first_name" placeholder="Enter the First Name" value="{{ old('first_name') }}"  class="form-control @error('first_name') is-invalid @enderror">
                    @error('first_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                  </div>
                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Last Name<sup>*</sup></label>
                      <input type="text" name="last_name" value="{{ old('last_name') }}"  placeholder="Enter the Last Name"  class="form-control @error('first_name') is-invalid @enderror">
                      @error('last_name')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-6">
                      <label for="inputText" class="col-form-label">Father Name</label>
                        <input type="text" name="father_name" value="{{ old('father_name') }}"  placeholder="Enter the Father Name" class="form-control">
                      </div>
                      <div class="col-lg-6">
                        <label for="inputText" class="col-form-label">Contact No</label>
                          <input type="text" name="contact_no" value="{{ old('contact_no') }}"  placeholder="Enter Parents Contact No" class="form-control">
                        </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-6">
                      <label for="inputText" class="col-form-label">Student CNIC</label>
                        <input type="text" name="student_cnic" value="{{ old('student_cnic') }}"  placeholder="Enter Student CNIC"  class="form-control">
                      </div>
                      <div class="col-lg-6">
                        <label for="inputText" class="col-form-label">Gender<sup>*</sup></label>
                        <select class="form-select @error('gender') is-invalid @enderror" name="gender" value="{{ old('gender') }}"  aria-label="Default select example">
                            <option value="Boy">Boy</option>
                            <option value="Girl">Girl</option>
                          </select>
                          @error('gender')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                        </div>
                </div>
               
                <div class="row mb-3">
                    <div class="col-lg-6">
                  <label for="inputDate" class="col-form-label">Date of Birth</label>
                    <input type="date" value="{{ old('student_dob') }}"  name="student_dob" class="form-control">
                  </div>
                  <div class="col-lg-6">
                    <label for="inputDate" class="col-form-label">Admission Date</label>
                      <input type="date" value="{{ old('student_admission_date') }}"  name="student_admission_date" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                  <div class="col-lg-6">
                    <label for="inputDate" class="col-form-label">Student Class<sup>*</sup></label>
                    <select class="form-select @error('class_room_id') is-invalid @enderror" value="{{ old('class_room_id') }}"  name="class_room_id"  aria-label="Default select example">
                      @error('class_room_id')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                      </select>
                    </div>
                    <div class="col-lg-6">
                      <label for="inputText" class="col-form-label">Student Status<sup>*</sup></label>
                      <select class="form-select @error('student_status') is-invalid @enderror" value="{{ old('student_status') }}"  name="student_status" aria-label="Default select example">
                        @error('student_status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                          <option value="1">Active</option>
                          <option value="0">Non Active</option>
                        </select>
                      </div>
                </div>
                <div class="row mb-3">
                  <div class="col-lg-6">
                    <label for="inputDate" class="col-form-label">Student Image</label>
                      <input type="file" value="{{ old('student_image') }}"  name="student_image" class="form-control" onchange="showPreviewOne(event);">
                      <p class="mt-4"><img id="file-ip-1-preview"  width="100" /></p>
                    </div>
                   
              </div>
                <div class="row mb-3">
                 
                </div>

          

                <div class="row mb-3">
                 
                  <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary">Submit Form</button>
                  </div>
                </div>

              </form><!-- End General Form Elements -->

            </div>
          </div>

        </div>
    </section>
</main>
@endsection

@section('script')
<script>

    function showPreviewOne(event){
      if(event.target.files.length > 0){
        let src = URL.createObjectURL(event.target.files[0]);
        let preview = document.getElementById("file-ip-1-preview");
        preview.src = src;
        preview.style.display = "block";
      } 
    }
    function myImgRemoveFunctionOne() {
      document.getElementById("file-ip-1-preview").src = "https://i.ibb.co/ZVFsg37/default.png";
    };
</script>
@endsection