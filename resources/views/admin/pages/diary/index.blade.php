@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

  {{-- ─── Page Header ────────────────────────────────────────────── --}}
  <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
    <div>
      <h1 class="pagetitle-h1 mb-0"><i class="bi bi-journal-bookmark-fill me-2 text-primary"></i>Daily Diary</h1>
      <nav><ol class="breadcrumb mb-0 mt-1">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Diary</li>
      </ol></nav>
    </div>

    {{-- ─── Inline Filter + New Button ───────────────────────────── --}}
    <form method="GET" class="d-flex flex-wrap align-items-end gap-2">
      <div>
        <label class="form-label small fw-semibold mb-1">Date</label>
        <input type="date" name="date" class="form-control form-control-sm" style="width:155px"
               value="{{ $date }}">
      </div>
      <div>
        <label class="form-label small fw-semibold mb-1">Class</label>
        <select name="class_id" class="form-select form-select-sm" style="width:160px">
          <option value="">All Classes</option>
          @foreach($classes as $c)
            <option value="{{ $c->id }}" @selected($c->id == $classId)>{{ $c->class_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="d-flex gap-2 align-items-end">
        <button type="submit" class="btn btn-primary btn-sm px-3">
          <i class="bi bi-funnel me-1"></i>Filter
        </button>
        @if($date != today()->toDateString() || $classId)
          <a href="{{ route('diary.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-x-circle me-1"></i>Clear
          </a>
        @endif
        <a href="{{ route('diary.create') }}" class="btn btn-primary btn-sm px-3">
          <i class="bi bi-plus-lg me-1"></i>New Entry
        </a>
      </div>
    </form>
  </div>

  {{-- ─── Active Filter Badge ──────────────────────────────────────── --}}
  <div class="mb-3 d-flex align-items-center gap-2">
    <span class="text-muted small">
      <i class="bi bi-calendar3 me-1"></i>
      Showing diary for: <strong>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong>
    </span>
    @if($classId)
      <span class="badge bg-primary-soft text-primary border border-primary" style="font-size:.75rem">
        {{ $classes->firstWhere('id', $classId)?->class_name ?? 'Class '.$classId }}
      </span>
    @else
      <span class="badge bg-secondary" style="font-size:.75rem">All Classes</span>
    @endif
    <span class="badge bg-light text-dark border" style="font-size:.75rem">
      {{ $grouped->sum('count') ?? $grouped->flatten()->count() }} subject(s)
    </span>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible border-0 shadow-sm mb-3">
      <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- ─── Diary Cards (grouped by class) ──────────────────────────── --}}
  @if($grouped->isEmpty())
    <div class="card">
      <div class="card-body text-center py-5">
        <i class="bi bi-journal-x fs-1 text-muted"></i>
        <p class="text-muted mt-3 mb-1">No diary entries for <strong>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong>.</p>
        <a href="{{ route('diary.create') }}" class="btn btn-primary btn-sm mt-2">
          <i class="bi bi-plus-lg me-1"></i>Create Diary for Today
        </a>
      </div>
    </div>
  @else
    <div class="row g-3">
      @foreach($grouped as $classId => $entries)
        @php $first = $entries->first(); $waSent = $entries->contains('whatsapp_sent', true); @endphp
        <div class="col-12">
          <div class="card shadow-sm" style="border-left:4px solid var(--ea-primary,#4154f1)">

            {{-- Card Header --}}
            <div class="card-header d-flex flex-wrap align-items-center gap-2 py-2"
                 style="background:linear-gradient(135deg,rgba(65,84,241,.06),rgba(65,84,241,.02))">
              <i class="bi bi-journal-richtext fs-5 text-primary"></i>
              <div class="me-auto">
                <span class="fw-bold text-dark fs-6">{{ $first->classroom?->class_name ?? 'Class '.$classId }}</span>
                <span class="text-muted small ms-2">
                  <i class="bi bi-calendar3 me-1"></i>{{ $first->diary_date->format('d M Y') }}
                </span>
              </div>
              {{-- WhatsApp badge --}}
              @if($waSent)
                <span class="badge px-2 py-1" style="background:#e7f9f0;color:#25d366;border:1px solid #25d366;font-size:.75rem">
                  <i class="bi bi-whatsapp me-1"></i>Sent to {{ $entries->max('whatsapp_recipients') }} parents
                </span>
              @else
                <form action="{{ route('diary.whatsapp', $first) }}" method="POST" class="m-0">
                  @csrf
                  <button type="submit" class="btn btn-sm px-2 py-1" style="background:#25d366;color:#fff;font-size:.8rem"
                          title="Send WhatsApp to all parents of this class"
                          onclick="return confirm('Send diary to all parents of {{ $first->classroom?->class_name }}?')">
                    <i class="bi bi-whatsapp me-1"></i>Send WhatsApp
                  </button>
                </form>
              @endif
              <a href="{{ route('diary.create') }}?class_id={{ $classId }}&date={{ $first->diary_date->toDateString() }}"
                 class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size:.8rem">
                <i class="bi bi-plus me-1"></i>Add Subject
              </a>
            </div>

            {{-- Subject Table --}}
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:.9rem">
                  <thead style="background:#f8f9fa">
                    <tr>
                      <th style="width:150px;padding:.6rem 1rem">Subject</th>
                      <th style="padding:.6rem 1rem">Class Work / Description</th>
                      <th style="width:250px;padding:.6rem 1rem">Homework</th>
                      <th style="width:100px;padding:.6rem 1rem">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($entries as $diary)
                    <tr>
                      <td style="padding:.6rem 1rem">
                        <span class="badge bg-primary-soft text-primary fw-semibold px-2 py-1"
                              style="background:rgba(65,84,241,.1);border-radius:6px;font-size:.82rem">
                          <i class="bi bi-book me-1"></i>{{ $diary->subject ?: $diary->title }}
                        </span>
                      </td>
                      <td style="padding:.6rem 1rem">
                        @if($diary->description)
                          <span class="text-dark">{{ $diary->description }}</span>
                        @else
                          <span class="text-muted fst-italic">—</span>
                        @endif
                      </td>
                      <td style="padding:.6rem 1rem">
                        @if($diary->homework)
                          <div class="d-flex align-items-start gap-1">
                            <i class="bi bi-pencil-square text-warning mt-1" style="font-size:.8rem"></i>
                            <span>{{ $diary->homework }}</span>
                          </div>
                        @else
                          <span class="text-muted fst-italic">No homework</span>
                        @endif
                      </td>
                      <td style="padding:.5rem 1rem">
                        <div class="d-flex align-items-center gap-1">
                          <a href="{{ route('diary.edit', $diary) }}"
                             class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="bi bi-pencil-fill"></i>
                          </a>
                          <form action="{{ route('diary.destroy', $diary) }}" method="POST"
                                onsubmit="return confirm('Delete this subject entry?')" class="m-0">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete">
                              <i class="bi bi-trash-fill"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

              {{-- Important Notes Row (shared — show once per class) --}}
              @php $note = $entries->firstWhere('important_notes', '!=', null)?->important_notes; @endphp
              @if($note)
                <div class="px-3 py-2 border-top"
                     style="background:#fff8e1;border-left:3px solid #ffc107 !important">
                  <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                  <strong class="small text-warning-emphasis">Note:</strong>
                  <span class="small text-dark ms-1">{{ $note }}</span>
                </div>
              @endif
            </div>

            {{-- Card Footer --}}
            <div class="card-footer d-flex align-items-center gap-2 py-2" style="background:#fafafa">
              <small class="text-muted">
                <i class="bi bi-person me-1"></i>By {{ $first->createdBy?->name ?? 'Admin' }}
                &bull;
                <i class="bi bi-clock me-1"></i>{{ $first->created_at->diffForHumans() }}
              </small>
              <span class="ms-auto badge bg-light text-muted border">
                {{ $entries->count() }} subject(s)
              </span>
            </div>

          </div>
        </div>
      @endforeach
    </div>
  @endif

</main>
@endsection

