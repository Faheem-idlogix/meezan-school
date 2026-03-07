@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle d-flex justify-content-between align-items-center">
      <div>
        <h1>Student Documents — {{ $student->student_name }}</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.show', $student) }}">{{ $student->student_name }}</a></li>
            <li class="breadcrumb-item active">Documents</li>
          </ol>
        </nav>
      </div>
      <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadDocModal">
        <i class="bi bi-upload me-1"></i>Upload Document
      </button>
    </div>

    <section class="section">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          @if($documents->isEmpty())
          <div class="text-center py-5 text-muted">
            <i class="bi bi-folder2-open fs-1"></i>
            <p class="mt-2">No documents uploaded yet.</p>
          </div>
          @else
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Document Type</th>
                  <th>Title</th>
                  <th>File</th>
                  <th>Verified</th>
                  <th>Uploaded</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($documents as $doc)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td><span class="badge bg-secondary">{{ $documentTypes[$doc->document_type] ?? $doc->document_type }}</span></td>
                  <td>{{ $doc->document_title }}</td>
                  <td><a href="{{ asset($doc->file_path) }}" target="_blank" class="text-primary">{{ $doc->file_name }}</a></td>
                  <td>
                    @if($doc->is_verified)
                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Verified</span>
                    @else
                    <span class="badge bg-warning text-dark">Pending</span>
                    @endif
                  </td>
                  <td>{{ $doc->created_at->format('d M Y') }}</td>
                  <td>
                    <div class="d-flex gap-1">
                      <a href="{{ asset($doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                      @if(!$doc->is_verified)
                      <form action="{{ route('student.documents.verify', $doc) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-success" title="Verify"><i class="bi bi-check-lg"></i></button>
                      </form>
                      @endif
                      <form action="{{ route('student.documents.destroy', $doc) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this document?')">
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
          @endif
        </div>
      </div>
    </section>

    {{-- Upload Modal --}}
    <div class="modal fade" id="uploadDocModal" tabindex="-1">
      <div class="modal-dialog">
        <form action="{{ route('student.documents.store', $student) }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Upload Document</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Document Type <span class="text-danger">*</span></label>
                <select name="document_type" class="form-select" required>
                  <option value="">Select Type</option>
                  @foreach($documentTypes as $key => $label)
                  <option value="{{ $key }}">{{ $label }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="document_title" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">File <span class="text-danger">*</span></label>
                <input type="file" name="document_file" class="form-control" required>
                <small class="text-muted">Max 10MB. PDF, JPG, PNG allowed.</small>
              </div>
              <div class="mb-3">
                <label class="form-label">Expiry Date</label>
                <input type="date" name="expiry_date" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control" rows="2"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i>Upload</button>
            </div>
          </div>
        </form>
      </div>
    </div>
</main>
@endsection
