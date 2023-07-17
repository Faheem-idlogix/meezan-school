@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
      <h1>Add Session</h1>
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
              <h5 class="card-title"> Session</h5>

              <!-- General Form Elements -->
              <form action="{{ route('Session.store') }}" method="post">
                @csrf
                <div class="row mb-3">
                <div class="col-lg-6">
                  <label for="inputText" class="col-form-label">Session Name</label>
                    <input type="text" name="session_name" placeholder="Enter the Session Name" class="form-control">
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

                <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Start Date</label>
                      <input type="date" name="start_date" class="form-control">
                    </div>
                    <div class="col-lg-6">
                      <label for="inputText" class="col-form-label">End Date</label>
                        <input type="date" name="end_date"  class="form-control">
                      </div>
                  </div>

         

          

                <div class="row mb-3">
                 
                  <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary">Add Session</button>
                  </div>
                </div>

              </form><!-- End General Form Elements -->

            </div>
          </div>

        </div>
    </section>
</main>
@endsection

