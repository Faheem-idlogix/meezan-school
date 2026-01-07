@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
      <h1>Edit Class Subject</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item active">Class Subject</li>
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
              <h5 class="card-title">Class Subject</h5>

              <!-- General Form Elements -->
              <form action="{{ route('class_subject.update', $classSubject->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                <div class="col-lg-6">
                  <label for="inputText" class="col-form-label">Select Class Name</label>
                  <select name="class_id" class="form-select">
                    <option selected disabled>Select Class</option>
                    @foreach ($class as $item)
                    <option value="{{ $item->id }}">{{ $item->class_name }}</option>
                    @endforeach
                  </select>
                  </div>
                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label">Select Subject Name</label>
                    <select name="subject_id" class="form-select">
                      <option selected disabled>Select Subject</option>
                      @foreach ($subject as $item)
                      <option value="{{ $item->id }}">{{ $item->subject_name }}</option>
                      @endforeach
                    </select>
                    </div>
                </div>

         

          

                <div class="row mb-3">
                 
                  <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary">Add Class Subject</button>
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