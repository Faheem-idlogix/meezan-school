@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
      <h1>Add Class</h1>
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
              <h5 class="card-title">Class General Information</h5>

              <!-- General Form Elements -->
                <form method="post" action="{{ route('class_update', $class->id) }}" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="row mb-3">
                <div class="col-lg-6">
                  <label for="inputText" class="col-form-label">Enter Class Name</label>
                    <input type="text" name="class_name" placeholder="Enter the Class Name" value="{{$class->class_name}}" class="form-control">
                  </div>
                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Enter Section Name</label>
                      <input type="text" name="section_name" placeholder="Enter the Subject Name" value="{{$class->section_name}}"  class="form-control">
                    </div>
                </div>

                <div class="row mb-3">
                  <div class="col-lg-6">
                      <label for="inputText" class="col-form-label">Select Session</label>
                      <select class="form-select" name="session_id" value="{{$class->session_id}}" aria-label="Default select example">
                          @foreach($session as $item)
                              <option value="{{$item->id}}"  @if($item->id == $class->session_id) selected @endif >{{$item->session_name}}</option>
                          @endforeach
                      </select>
                  </div>
                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Select Status</label>
                    <select class="form-select" name="status" value="{{$class->status}}" aria-label="Default select example">
                      <option {{ ($class->status) == '1' ? 'selected' : '' }}  value="1">Active</option>
                      <option {{ ($class->status) == '0' ? 'selected' : '' }}  value="0">Non Active</option>
                      </select>
                    </div>
              </div>
              
            

          

                <div class="row mb-3">
                 
                  <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary">Add Class</button>
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
