@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>Admission Enquiries</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">Admissions</li>
          </ol>
        </nav>
      </div>
      <a href="{{ route('admission.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>New Enquiry
      </a>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-3">
      <div class="col-md-2">
        <div class="card border-start border-primary border-3 shadow-sm">
          <div class="card-body py-2 px-3">
            <div class="text-muted small">Total</div>
            <div class="fw-bold fs-5">{{ $stats['total'] }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card border-start border-info border-3 shadow-sm">
          <div class="card-body py-2 px-3">
            <div class="text-muted small">Enquiries</div>
            <div class="fw-bold fs-5">{{ $stats['enquiry'] }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card border-start border-warning border-3 shadow-sm">
          <div class="card-body py-2 px-3">
            <div class="text-muted small">Test Scheduled</div>
            <div class="fw-bold fs-5">{{ $stats['test_scheduled'] }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card border-start border-success border-3 shadow-sm">
          <div class="card-body py-2 px-3">
            <div class="text-muted small">Approved</div>
            <div class="fw-bold fs-5">{{ $stats['approved'] }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card border-start border-primary border-3 shadow-sm">
          <div class="card-body py-2 px-3">
            <div class="text-muted small">Enrolled</div>
            <div class="fw-bold fs-5">{{ $stats['enrolled'] }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card border-start border-danger border-3 shadow-sm">
          <div class="card-body py-2 px-3">
            <div class="text-muted small">Rejected</div>
            <div class="fw-bold fs-5">{{ $stats['rejected'] }}</div>
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
                  <th>Student Name</th>
                  <th>Father</th>
                  <th>Contact</th>
                  <th>Class Applied</th>
                  <th>Enquiry Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($enquiries as $e)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td class="fw-semibold">{{ $e->student_name }}</td>
                  <td>{{ $e->father_name ?? '—' }}</td>
                  <td>{{ $e->contact_no }}</td>
                  <td>{{ $e->classRoom->class_name ?? $e->class_applied ?? '—' }}</td>
                  <td>{{ $e->enquiry_date ? $e->enquiry_date->format('d M Y') : '—' }}</td>
                  <td>{!! $e->status_badge !!}</td>
                  <td>
                    <div class="d-flex gap-1">
                      <a href="{{ route('admission.show', $e) }}" class="btn btn-sm btn-outline-info" title="View">
                        <i class="bi bi-eye"></i>
                      </a>
                      <a href="{{ route('admission.edit', $e) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                        <i class="bi bi-pencil"></i>
                      </a>
                      @if($e->status === 'approved')
                      <form action="{{ route('admission.enroll', $e) }}" method="POST" class="d-inline" onsubmit="return confirm('Enroll this student?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-success" title="Enroll">
                          <i class="bi bi-person-check"></i>
                        </button>
                      </form>
                      @endif
                      <form action="{{ route('admission.destroy', $e) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this enquiry?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                          <i class="bi bi-trash"></i>
                        </button>
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
