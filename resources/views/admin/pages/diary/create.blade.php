@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
  <div class="pagetitle mb-3">
    <h1><i class="bi bi-journal-plus me-2 text-primary"></i>New Diary Entry</h1>
    <nav><ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
      <li class="breadcrumb-item"><a href="{{ route('diary.index') }}">Diary</a></li>
      <li class="breadcrumb-item active">New Entry</li>
    </ol></nav>
  </div>

  <div class="card">
    <div class="card-header py-3" style="background:linear-gradient(135deg,rgba(65,84,241,.07),rgba(65,84,241,.02))">
      <h5 class="card-title mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Class Diary — All Subjects</h5>
    </div>
    <div class="card-body">
      <form action="{{ route('diary.store') }}" method="POST" id="diaryForm">
        @csrf

        {{-- ─── Top: Class + Date ───────────────────────────────────── --}}
        <div class="row g-3 mb-4">
          <div class="col-md-5">
            <label class="form-label fw-semibold">Class <span class="text-danger">*</span></label>
            <select name="class_room_id" class="form-select select2" required>
              <option value="">Select Class...</option>
              @foreach($classes as $c)
                <option value="{{ $c->id }}"
                  {{ (old('class_room_id', request('class_id')) == $c->id) ? 'selected' : '' }}>
                  {{ $c->class_name }}
                </option>
              @endforeach
            </select>
            @error('class_room_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
            <input type="date" name="diary_date" class="form-control"
                   value="{{ old('diary_date', request('date', today()->toDateString())) }}" required>
            @error('diary_date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
          </div>
        </div>

        {{-- ─── Subjects Table ──────────────────────────────────────── --}}
        <div class="mb-3">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <label class="form-label fw-semibold mb-0">
              <i class="bi bi-grid-3x3-gap me-1 text-primary"></i>
              Subjects & Work
              <span class="text-danger">*</span>
            </label>
            <button type="button" class="btn btn-outline-primary btn-sm" id="addSubjectBtn">
              <i class="bi bi-plus-circle me-1"></i>Add Subject
            </button>
          </div>

          <div class="table-responsive border rounded" style="overflow:visible">
            <table class="table align-middle mb-0" id="subjectsTable" style="font-size:.9rem">
              <thead style="background:#f0f3ff">
                <tr>
                  <th style="width:160px;padding:.7rem 1rem">
                    Subject <span class="text-danger">*</span>
                  </th>
                  <th style="padding:.7rem 1rem">
                    <i class="bi bi-journal-text me-1 text-muted"></i>Class Work / Description
                  </th>
                  <th style="width:260px;padding:.7rem 1rem">
                    <i class="bi bi-pencil-square me-1 text-warning"></i>Homework
                  </th>
                  <th style="width:52px;padding:.7rem .5rem"></th>
                </tr>
              </thead>
              <tbody id="subjectRows">
                {{-- Pre-filled rows or default 1 row --}}
                @if(old('subjects'))
                  @foreach(old('subjects') as $i => $row)
                    <tr class="subject-row">
                      <td style="padding:.5rem 1rem">
                        <input type="text" name="subjects[{{ $i }}][subject]"
                               class="form-control form-control-sm"
                               value="{{ $row['subject'] ?? '' }}"
                               placeholder="e.g. Mathematics" required>
                      </td>
                      <td style="padding:.5rem 1rem">
                        <textarea name="subjects[{{ $i }}][description]"
                                  class="form-control form-control-sm" rows="2"
                                  placeholder="What was covered in class...">{{ $row['description'] ?? '' }}</textarea>
                      </td>
                      <td style="padding:.5rem 1rem">
                        <textarea name="subjects[{{ $i }}][homework]"
                                  class="form-control form-control-sm" rows="2"
                                  placeholder="Homework assigned...">{{ $row['homework'] ?? '' }}</textarea>
                      </td>
                      <td style="padding:.5rem .5rem;text-align:center">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-row py-0 px-2"
                                title="Remove row"><i class="bi bi-x-lg"></i></button>
                      </td>
                    </tr>
                  @endforeach
                @else
                  {{-- Default 3 empty rows --}}
                  @foreach(['Mathematics','English','Science'] as $i => $sub)
                  <tr class="subject-row">
                    <td style="padding:.5rem 1rem">
                      <input type="text" name="subjects[{{ $i }}][subject]"
                             class="form-control form-control-sm"
                             value="{{ $sub }}" placeholder="e.g. Mathematics" required>
                    </td>
                    <td style="padding:.5rem 1rem">
                      <textarea name="subjects[{{ $i }}][description]"
                                class="form-control form-control-sm" rows="2"
                                placeholder="What was covered in class..."></textarea>
                    </td>
                    <td style="padding:.5rem 1rem">
                      <textarea name="subjects[{{ $i }}][homework]"
                                class="form-control form-control-sm" rows="2"
                                placeholder="Homework assigned..."></textarea>
                    </td>
                    <td style="padding:.5rem .5rem;text-align:center">
                      <button type="button" class="btn btn-sm btn-outline-danger remove-row py-0 px-2"
                              title="Remove row"><i class="bi bi-x-lg"></i></button>
                    </td>
                  </tr>
                  @endforeach
                @endif
              </tbody>
            </table>
          </div>
          @error('subjects')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
          @error('subjects.*.subject')<div class="text-danger small mt-1">All subject names are required.</div>@enderror
        </div>

        {{-- ─── Important Notes ─────────────────────────────────────── --}}
        <div class="mb-4">
          <label class="form-label fw-semibold">
            <i class="bi bi-exclamation-triangle me-1 text-warning"></i>
            Important Notes / Announcements
            <small class="text-muted fw-normal">(shared for all subjects)</small>
          </label>
          <textarea name="important_notes" class="form-control" rows="2"
                    placeholder="Parent meeting tomorrow at 3PM, test reminder, holiday notice...">{{ old('important_notes') }}</textarea>
        </div>

        {{-- ─── WhatsApp Toggle ─────────────────────────────────────── --}}
        <div class="mb-4 p-3 rounded" style="background:#f0fef4;border:1px solid #b7ebd0">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="send_whatsapp" id="sendWA" value="1" style="width:2.5rem;height:1.25rem">
            <label class="form-check-label ms-2" for="sendWA">
              <i class="bi bi-whatsapp me-1" style="color:#25d366;font-size:1.1rem"></i>
              <strong>Send to parents via WhatsApp immediately</strong>
              <small class="text-muted d-block mt-1">Will notify all parents of the selected class on WhatsApp after saving.</small>
            </label>
          </div>
        </div>

        {{-- ─── Action Buttons ──────────────────────────────────────── --}}
        <div class="d-flex gap-2 justify-content-end border-top pt-3">
          <a href="{{ route('diary.index') }}" class="btn btn-outline-secondary px-4">
            <i class="bi bi-x-circle me-1"></i>Cancel
          </a>
          <button type="submit" class="btn btn-primary px-5">
            <i class="bi bi-save me-1"></i>Save Diary
          </button>
        </div>

      </form>
    </div>
  </div>
</main>
@endsection

@section('scripts')
<script>
(function () {
  // ── Add/remove subject rows ──────────────────────────────────────
  let rowIndex = {{ old('subjects') ? count(old('subjects')) : 3 }};

  function reIndex() {
    document.querySelectorAll('#subjectRows .subject-row').forEach(function (row, i) {
      row.querySelectorAll('[name]').forEach(function (el) {
        el.name = el.name.replace(/subjects\[\d+\]/, 'subjects[' + i + ']');
      });
    });
  }

  document.getElementById('addSubjectBtn').addEventListener('click', function () {
    const tbody = document.getElementById('subjectRows');
    const idx   = rowIndex++;
    const tr    = document.createElement('tr');
    tr.className = 'subject-row';
    tr.innerHTML = `
      <td style="padding:.5rem 1rem">
        <input type="text" name="subjects[${idx}][subject]"
               class="form-control form-control-sm" placeholder="Subject name" required>
      </td>
      <td style="padding:.5rem 1rem">
        <textarea name="subjects[${idx}][description]"
                  class="form-control form-control-sm" rows="2"
                  placeholder="What was covered in class..."></textarea>
      </td>
      <td style="padding:.5rem 1rem">
        <textarea name="subjects[${idx}][homework]"
                  class="form-control form-control-sm" rows="2"
                  placeholder="Homework assigned..."></textarea>
      </td>
      <td style="padding:.5rem .5rem;text-align:center">
        <button type="button" class="btn btn-sm btn-outline-danger remove-row py-0 px-2"
                title="Remove"><i class="bi bi-x-lg"></i></button>
      </td>`;
    tbody.appendChild(tr);
    tr.querySelector('input').focus();
    bindRemove(tr);
  });

  function bindRemove(row) {
    row.querySelector('.remove-row').addEventListener('click', function () {
      if (document.querySelectorAll('#subjectRows .subject-row').length <= 1) {
        alert('At least one subject is required.');
        return;
      }
      row.remove();
      reIndex();
    });
  }

  // Bind existing remove buttons
  document.querySelectorAll('#subjectRows .remove-row').forEach(function (btn) {
    bindRemove(btn.closest('.subject-row'));
  });
})();
</script>
@endsection

