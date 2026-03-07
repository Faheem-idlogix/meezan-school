@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>Transfer Certificates</h1>
        <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item active">Transfer Certificates</li></ol></nav>
      </div>
      <a href="{{ route('transfer-certificate.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>New TC</a>
    </div>
    <section class="section">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover datatable align-middle">
              <thead class="table-light">
                <tr><th>#</th><th>TC No</th><th>Student</th><th>Class</th><th>Issue Date</th><th>Reason</th><th>Status</th><th>Actions</th></tr>
              </thead>
              <tbody>
                @foreach($certificates as $tc)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td class="fw-semibold">{{ $tc->tc_number }}</td>
                  <td>{{ $tc->student->student_name ?? '—' }}</td>
                  <td>{{ $tc->student->classroom->class_name ?? '—' }}</td>
                  <td>{{ $tc->issue_date->format('d M Y') }}</td>
                  <td>{{ $tc->reason ?? '—' }}</td>
                  <td>{!! $tc->status_badge !!}</td>
                  <td>
                    <div class="d-flex gap-1">
                      <a href="{{ route('transfer-certificate.show', $tc) }}" class="btn btn-sm btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                      @if($tc->status !== 'cancelled')
                      <a href="{{ route('transfer-certificate.pdf', $tc) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="PDF"><i class="bi bi-file-pdf"></i></a>
                      @endif
                      <form action="{{ route('transfer-certificate.destroy', $tc) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
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
