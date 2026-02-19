@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
      <h1>New Notice</h1>
      <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('notice.index') }}">Notices</a></li>
        <li class="breadcrumb-item active">Create</li>
      </ol></nav>
    </div>

    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card shadow-sm border-0">
            <div class="card-body p-4">
              <h5 class="card-title mb-4"><i class="bi bi-megaphone me-2"></i>Notice Details</h5>
              <form action="{{ route('notice.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Notice title">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                  <div class="col-12">
                    <label class="form-label">Content <span class="text-danger">*</span></label>
                    <textarea name="content" rows="5" class="form-control @error('content') is-invalid @enderror" placeholder="Notice content...">{{ old('content') }}</textarea>
                    @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Audience <span class="text-danger">*</span></label>
                    <select name="audience" class="form-select">
                      <option value="all" {{ old('audience') === 'all' ? 'selected' : '' }}>All</option>
                      <option value="students" {{ old('audience') === 'students' ? 'selected' : '' }}>Students</option>
                      <option value="teachers" {{ old('audience') === 'teachers' ? 'selected' : '' }}>Teachers</option>
                      <option value="parents" {{ old('audience') === 'parents' ? 'selected' : '' }}>Parents</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-select">
                      <option value="low">Low</option>
                      <option value="medium" selected>Medium</option>
                      <option value="high">High</option>
                    </select>
                  </div>
                  <div class="col-12">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" name="send_whatsapp" id="sendWhatsapp">
                      <label class="form-check-label" for="sendWhatsapp">
                        <i class="bi bi-whatsapp text-success me-1"></i> Send via WhatsApp to contacts
                      </label>
                    </div>
                  </div>
                  <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i> Publish Notice</button>
                    <a href="{{ route('notice.index') }}" class="btn btn-outline-secondary">Cancel</a>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
</main>
@endsection
