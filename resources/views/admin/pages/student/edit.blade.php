@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
      <h1>Edit Student</h1>
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
              <form method="post" action="{{ route('student.update', $Student) }}" enctype="multipart/form-data" novalidate>
                @method('patch')
                @csrf
                <div class="row mb-3">
                <div class="col-lg-6">
                  <label for="inputText" class="col-form-label ">First Name<sup>*</sup></label>
                    <input type="text" name="first_name" placeholder="Enter the First Name" value="{{$Student->first_name}}"  class="form-control @error('first_name') is-invalid @enderror">
                    @error('first_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                  </div>
                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Last Name<sup>*</sup></label>
                      <input type="text" name="last_name"  value="{{$Student->last_name}}"  placeholder="Enter the Last Name"  class="form-control @error('first_name') is-invalid @enderror">
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
                        <input type="text" name="father_name"  value="{{$Student->father_name}}"  placeholder="Enter the Father Name" class="form-control">
                      </div>
                      <div class="col-lg-6">
                        <label for="inputText" class="col-form-label">Contact No</label>
                          <input type="text" name="contact_no"  value="{{$Student->contact_no}}"   placeholder="Enter Parents Contact No" class="form-control">
                        </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-6">
                      <label for="inputText" class="col-form-label">Student CNIC</label>
                        <input type="text" name="student_cnic"  value="{{$Student->student_cnic}}"  placeholder="Enter Student CNIC"  class="form-control">
                      </div>
                      <div class="col-lg-6">
                        <label for="inputText" class="col-form-label">Gender<sup>*</sup></label>
                        <select class="form-select @error('gender') is-invalid @enderror" name="gender"  value="{{$Student->gender}}"   aria-label="Default select example">
                            <option {{ ($Student->gender) == 'Boy' ? 'selected' : '' }}  value="Boy">Boy</option>
                            <option {{ ($Student->gender) == 'Girl' ? 'selected' : '' }}  value="Girl">Girl</option>
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
                    <input type="date"  value="{{$Student->student_dob}}"   name="student_dob" class="form-control">
                  </div>
                  <div class="col-lg-6">
                    <label for="inputDate" class="col-form-label">Admission Date</label>
                      <input type="date"  value="{{$Student->student_admission_date}}"   name="student_admission_date" class="form-control">
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
                      @foreach($class_room as $classroom)
                      <option value="{{$classroom->id}}" @if($classroom->id == $Student->class_room_id) selected @endif >{{$classroom->class_name .' '. $classroom->section_name}}</option>
                    @endforeach
                      </select>
                    </div>
                    <div class="col-lg-6">
                      <label for="inputText" class="col-form-label">Student Status<sup>*</sup></label>
                      <select class="form-select @error('student_status') is-invalid @enderror" value="{{ $Student->student_status }}"  name="student_status" aria-label="Default select example">
                        @error('student_status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <option {{ ($Student->student_status) == '1' ? 'selected' : '' }}  value="1">Active</option>
                    <option {{ ($Student->student_status) == '0' ? 'selected' : '' }}  value="0">Non Active</option>
                        </select>
                      </div>
                </div>
                <div class="row mb-3">
                  <div class="col-lg-6">
                    <label for="inputDate" class="col-form-label">Student Image</label>
                      <input type="file"  value="{{$Student->student_image}}"   name="student_image"  class="form-control" onchange="showPreviewOne(event);">
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