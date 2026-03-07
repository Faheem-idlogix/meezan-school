@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>Behavior Records</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Behavior Tracking</li>
          </ol>
        </nav>
      </div>
      <a href="{{ route('behavior.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Add Record
      </a>
    </div>

    {{-- Quick Stats --}}
    <div class="row mb-3">
      <div class="col-md-4">
        <div class="card border-start border-success border-3 shadow-sm">
          <div class="card-body py-2 px-3">
            <div class="text-muted small">Positive</div>
            <div class="fw-bold fs-5 text-success">{{ $behaviors->where('type','positive')->count() }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-start border-danger border-3 shadow-sm">
          <div class="card-body py-2 px-3">
            <div class="text-muted small">Negative</div>
            <div class="fw-bold fs-5 text-danger">{{ $behaviors->where('type','negative')->count() }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-start border-secondary border-3 shadow-sm">
          <div class="card-body py-2 px-3">
            <div class="text-muted small">Neutral</div>
            <div class="fw-bold fs-5">{{ $behaviors->where('type','neutral')->count() }}</div>
          </div>
        </div>
      </div>
    </div>

    <section class="section">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover datatable align-middle">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Student</th>
                  <th>Class</th>
                  <th>Type</th>
                  <th>Category</th>
                  <th>Title</th>
                  <th>Points</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($behaviors as $b)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td class="fw-semibold">{{ $b->student->student_name ?? '—' }}</td>
                  <td>{{ $b->classRoom->class_name ?? '—' }}</td>
                  <td>{!! $b->type_badge !!}</td>
                  <td>{{ $categories[$b->category] ?? $b->category }}</td>
                  <td>{{ $b->title }}</td>
                  <td>
                    @if($b->points > 0) <span class="text-success">+{{ $b->points }}</span>
                    @elseif($b->points < 0) <span class="text-danger">{{ $b->points }}</span>
                    @else <span class="text-muted">0</span>
                    @endif
                  </td>
                  <td>{{ $b->incident_date->format('d M Y') }}</td>
                  <td>
                    <div class="d-flex gap-1">
                      <a href="{{ route('behavior.edit', $b) }}" class="btn btn-sm btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                      <form action="{{ route('behavior.destroy', $b) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                      </form>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
</main>
@endsection
