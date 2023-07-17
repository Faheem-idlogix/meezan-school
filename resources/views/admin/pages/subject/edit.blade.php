@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
      <h1>Add Subject</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item active">Subject</li>
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
              <h5 class="card-title">Subjectn</h5>

              <!-- General Form Elements -->
              <form action="{{ route('Subject.update', $Subject) }}" method="post">
                @method('patch')

                @csrf
                <div class="row mb-3">
                <div class="col-lg-6">
                  <label for="inputText" class="col-form-label">Enter Subject Code</label>
                    <input type="text" name="subject_code" value="{{ $Subject->subject_code }}" placeholder="Enter the Subject code" class="form-control">
                  </div>
                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Enter Subject Name</label>
                      <input type="text" name="subject_name" value="{{ $Subject->subject_name }}" placeholder="Enter the Subject Name"  class="form-control">
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
    </section>
</main>
@endsection

