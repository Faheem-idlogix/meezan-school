{{-- ══════════ CONTACTS & SYNC TAB ══════════ --}}
<div class="row g-4">

  {{-- ── STUDENTS ── --}}
  <div class="col-lg-6">
    <h6 class="form-section-title"><i class="bi bi-people-fill me-1"></i>Student Contacts
      <span class="badge bg-success ms-2">{{ $studentWhatsapp }} on WhatsApp</span>
      <span class="badge bg-secondary ms-1">{{ $studentTotal }} total</span>
    </h6>
    <form action="{{ route('whatsapp.syncNumbers') }}" method="POST">
      @csrf
      <input type="hidden" name="type" value="student">
      <div class="table-responsive" style="max-height:420px;overflow:auto">
        <table class="table table-sm table-hover mb-0">
          <thead class="sticky-top bg-white">
            <tr>
              <th style="width:30px">#</th>
              <th>Name</th>
              <th>Class</th>
              <th>Contact No</th>
              <th>WhatsApp No</th>
              <th style="width:60px">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($students as $student)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td class="fw-semibold" style="font-size:.84rem">{{ $student->student_name }}</td>
              <td><span class="badge" style="background:#f0f4ff;color:#012970">{{ $student->classroom->class_name ?? '—' }}</span></td>
              <td style="font-size:.82rem">{{ $student->contact_no ?? '—' }}</td>
              <td>
                <input type="hidden" name="numbers[{{ $loop->index }}][id]" value="{{ $student->id }}">
                <input type="text" name="numbers[{{ $loop->index }}][whatsapp_number]"
                       class="form-control form-control-sm" style="width:140px"
                       value="{{ $student->whatsapp_number }}"
                       placeholder="92XXXXXXXXXX">
              </td>
              <td class="text-center">
                @if($student->has_whatsapp)
                  <span class="cc-badge-wa bg-success text-white"><i class="bi bi-whatsapp"></i> On</span>
                @else
                  <span class="cc-badge-wa bg-secondary text-white">Off</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="mt-3">
        <button type="submit" class="btn btn-success"><i class="bi bi-arrow-repeat me-1"></i>Sync Student Numbers</button>
      </div>
    </form>
  </div>

  {{-- ── TEACHERS ── --}}
  <div class="col-lg-6">
    <h6 class="form-section-title"><i class="bi bi-person-badge-fill me-1"></i>Teacher Contacts
      <span class="badge bg-success ms-2">{{ $teacherWhatsapp }} on WhatsApp</span>
      <span class="badge bg-secondary ms-1">{{ $teacherTotal }} total</span>
    </h6>
    <form action="{{ route('whatsapp.syncNumbers') }}" method="POST">
      @csrf
      <input type="hidden" name="type" value="teacher">
      <div class="table-responsive" style="max-height:420px;overflow:auto">
        <table class="table table-sm table-hover mb-0">
          <thead class="sticky-top bg-white">
            <tr>
              <th style="width:30px">#</th>
              <th>Name</th>
              <th>Contact No</th>
              <th>WhatsApp No</th>
              <th style="width:60px">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($teachers as $teacher)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td class="fw-semibold" style="font-size:.84rem">{{ $teacher->teacher_name }}</td>
              <td style="font-size:.82rem">{{ $teacher->contact_no ?? '—' }}</td>
              <td>
                <input type="hidden" name="numbers[{{ $loop->index }}][id]" value="{{ $teacher->id }}">
                <input type="text" name="numbers[{{ $loop->index }}][whatsapp_number]"
                       class="form-control form-control-sm" style="width:140px"
                       value="{{ $teacher->whatsapp_number }}"
                       placeholder="92XXXXXXXXXX">
              </td>
              <td class="text-center">
                @if($teacher->has_whatsapp)
                  <span class="cc-badge-wa bg-success text-white"><i class="bi bi-whatsapp"></i> On</span>
                @else
                  <span class="cc-badge-wa bg-secondary text-white">Off</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="mt-3">
        <button type="submit" class="btn btn-success"><i class="bi bi-arrow-repeat me-1"></i>Sync Teacher Numbers</button>
      </div>
    </form>
  </div>

</div>
