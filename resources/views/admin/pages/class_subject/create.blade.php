@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
      <h1>Add Class Subject</h1>
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

          @if(!empty($usingArchivedClasses) && $usingArchivedClasses)
          <div class="alert alert-info border-0">
            Showing all classes. Entries marked <strong>(Archived)</strong> are inactive/soft-deleted classes.
          </div>
          @endif

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Class Subject</h5>

              <!-- General Form Elements -->
              <form action="{{ route('class_subject.store') }}" method="post">
                @csrf
                <div class="row mb-3">
                <div class="col-lg-6">
                  <label for="inputText" class="col-form-label ">Select Class Name</label>
                  <select name="class_id" class="form-select" required>
                    <option value="">-- Select Class --</option>
                    @forelse ($classRooms as $item)
                    <option value="{{ $item->id }}" {{ old('class_id') == $item->id ? 'selected' : '' }}>
                      {{ $item->class_name ?? $item->name ?? ('Class #' . $item->id) }}{{ !empty($item->section_name) ? ' - ' . $item->section_name : '' }}{{ !empty($item->deleted_at) ? ' (Archived)' : '' }}
                    </option>
                    @empty
                    <option value="" disabled>No classes found. Please add classes first.</option>
                    @endforelse
                  </select>
                  @error('class_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                  </div>
                  <div class="col-lg-6">
                    <label for="inputText" class="col-form-label ">Select Subject Name</label>
                    <select name="subject_id[]" class="form-select" multiple required>
                      @forelse ($subjects as $item)
                      <option value="{{ $item->id }}" {{ collect(old('subject_id', []))->contains($item->id) ? 'selected' : '' }}>{{ $item->subject_name }}</option>
                      @empty
                      <option value="" disabled>No subjects found. Please add subjects first.</option>
                      @endforelse
                    </select>
                    @error('subject_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    @error('subject_id.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

         

          

                <div class="row mb-3">
                 
                  <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Add Class Subject</button>
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
