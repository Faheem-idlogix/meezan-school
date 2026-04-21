@extends('admin.layout.master')
@section('css')
<style>
  .cs-card {
    border: 1px solid #e8edf7;
    border-radius: 14px;
    background: linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
    box-shadow: 0 8px 20px rgba(33, 52, 98, 0.08);
  }
  .cs-card-header {
    border-bottom: 1px solid #edf1fb;
    padding-bottom: .6rem;
    margin-bottom: .85rem;
  }
  .cs-chip {
    border-radius: 999px;
    border: 1px solid #dbe5ff;
    background: #fff;
    padding: .34rem .6rem;
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    font-size: .82rem;
    font-weight: 600;
    color: #213462;
  }
  .cs-chip .btn {
    padding: 0;
    line-height: 1;
  }
  .cs-badge {
    background: #eaf1ff;
    color: #213462;
    border: 1px solid #dbe5ff;
  }
</style>
@endsection
@section('content')
<main id="main" class="main">

    <div class="row col-lg-12 pagetitle">
        <div class="col-lg-10">
      <h1>All Class Subjects</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Class Subjects</li>
        </ol>
        </nav>
    </div>
      <div class="col-lg-2">
        <a href="{{ route('class_subject.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Class Subject</a>
        </div>
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
              <h5 class="card-title">Class Subjects By Class</h5>

              @if($groupedClassSubjects->isEmpty())
                <div class="alert alert-warning mb-0">
                  No class-subject assignments found yet. Click <strong>Add Class Subject</strong> to assign subjects.
                </div>
              @else
                <div class="row g-3">
                  @foreach($groupedClassSubjects as $classId => $items)
                    @php
                      $classRoom = optional($items->first())->classRoom;
                    @endphp
                    <div class="col-lg-6 col-xl-4">
                      <div class="cs-card p-3 h-100">
                        <div class="cs-card-header d-flex align-items-center justify-content-between">
                          <div>
                            <h6 class="mb-0 text-primary fw-bold">{{ $classRoom->class_name ?? 'Unknown Class' }}</h6>
                            <small class="text-muted">{{ $classRoom && $classRoom->section_name ? 'Section: ' . $classRoom->section_name : 'No section' }}</small>
                          </div>
                          <span class="badge cs-badge">{{ $items->count() }} subjects</span>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                          @foreach($items as $item)
                            <span class="cs-chip">
                              {{ $item->subject->subject_name ?? 'Unknown Subject' }}
                              <a href="{{ route('class_subject.edit', $item) }}" class="btn btn-sm text-primary" title="Edit mapping">
                                <i class="bi bi-pencil-square"></i>
                              </a>
                              <form action="{{ route('class_subject.destroy', $item) }}" method="POST" class="ms-2" onsubmit="return confirm('Remove this subject from class?')">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent text-danger" title="Remove" style="line-height:1;">
                                  <i class="bi bi-x-circle-fill"></i>
                                </button>
                              </form>
                            </span>
                          @endforeach
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              @endif

            </div>
          </div>

        </div>
      </div>
    </section>
</main>
@endsection