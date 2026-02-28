@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle d-flex align-items-center justify-content-between">
      <div>
        <h1>Add Class</h1>
        <nav><ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('class.index') }}">Classes</a></li>
          <li class="breadcrumb-item active">Add New</li>
        </ol></nav>
      </div>
      <a href="{{ route('class.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-header">
              <h5 class="card-title"><i class="bi bi-building me-2 text-primary"></i>Class Information</h5>
            </div>
            <div class="card-body pt-3">

              <!-- General Form Elements -->
              <form  action="{{ route('class.store') }}" method="post">
                @csrf
                <div class="row mb-3">
                <div class="col-lg-6">
                  <label for="inputText" class="col-form-label">Enter Class Name</label>
                    <input type="text" name="class_name" placeholder="Enter the Class Name" class="form-control">
                  </div>
                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Enter Section Name</label>
                      <input type="text" name="section_name" placeholder="Enter the Subject Name"  class="form-control">
                    </div>
                </div>

                <div class="row mb-3">
                  <div class="col-lg-6">
                      <label for="inputText" class="col-form-label">Select Session</label>
                      <select class="form-select" name="session_id" aria-label="Default select example">
                          @foreach($session as $item)
                              <option value="{{$item->id}}">{{$item->session_name}}</option>
                          @endforeach
                      </select>
                  </div>
                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Select Status</label>
                    <select class="form-select" name="status" aria-label="Default select example">
                        <option value="1">Active</option>
                        <option value="0">Non Active</option>
                      </select>
                    </div>
              </div>
              
            

          

                <div class="row mb-3">
                 
                  <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Add Class</button>
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
