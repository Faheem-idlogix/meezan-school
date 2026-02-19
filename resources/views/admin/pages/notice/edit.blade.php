@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
      <h1>Edit Notice</h1>
      <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('notice.index') }}">Notices</a></li>
        <li class="breadcrumb-item active">Edit</li>
      </ol></nav>
    </div>
    <section class="section">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card shadow-sm border-0">
            <div class="card-body p-4">
              <form action="{{ route('notice.update', $notice) }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $notice->title) }}">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Content</label>
                    <textarea name="content" rows="5" class="form-control">{{ old('content', $notice->content) }}</textarea>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Audience</label>
                    <select name="audience" class="form-select">
                      @foreach(['all','students','teachers','parents'] as $a)
                      <option value="{{ $a }}" {{ $notice->audience === $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-select">
                      @foreach(['low','medium','high'] as $p)
                      <option value="{{ $p }}" {{ $notice->priority === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-6">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" name="send_whatsapp" {{ $notice->send_whatsapp ? 'checked' : '' }}>
                      <label class="form-check-label"><i class="bi bi-whatsapp text-success me-1"></i> Send via WhatsApp</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" name="is_active" {{ $notice->is_active ? 'checked' : '' }}>
                      <label class="form-check-label">Active</label>
                    </div>
                  </div>
                  <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update Notice</button>
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
