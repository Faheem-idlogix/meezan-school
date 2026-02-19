{{-- ══════════ COMPOSE MESSAGE TAB ══════════ --}}
<div class="row g-4">

  {{-- ── Send to Selected Recipients ── --}}
  <div class="col-lg-7">
    <div class="compose-area">
      <h6 class="form-section-title"><i class="bi bi-send me-1"></i>Send to Selected Recipients</h6>
      <form action="{{ route('whatsapp.sendBulk') }}" method="POST">
        @csrf
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label">Recipient Type</label>
            <select name="recipient_type" id="recipientType" class="form-select" onchange="toggleRecipientList()">
              <option value="student">Students</option>
              <option value="teacher">Teachers</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Message Type</label>
            <select name="message_type" class="form-select">
              <option value="manual">Manual Message</option>
              <option value="diary">Diary Reminder</option>
              <option value="notice">Notice</option>
              <option value="fee">Fee Reminder</option>
            </select>
          </div>
        </div>

        {{-- Student checkboxes --}}
        <div id="studentRecipients">
          <div class="d-flex align-items-center gap-2 mb-2">
            <input type="checkbox" onchange="toggleAllCheckboxes(this, 'cb-student')" class="form-check-input">
            <span class="fw-semibold" style="font-size:.82rem">Select All Students ({{ $students->where('has_whatsapp', true)->count() }} with WhatsApp)</span>
          </div>
          <div style="max-height:220px;overflow:auto;border:1px solid #e9ecef;border-radius:8px;padding:.5rem">
            @foreach($students->where('has_whatsapp', true) as $s)
            <div class="form-check">
              <input class="form-check-input cb-student" type="checkbox" name="recipient_ids[]" value="{{ $s->id }}" id="stu-{{ $s->id }}">
              <label class="form-check-label" for="stu-{{ $s->id }}" style="font-size:.82rem">
                {{ $s->student_name }} <span class="text-muted">({{ $s->whatsapp_number }})</span>
                <span class="badge" style="background:#f0f4ff;color:#012970;font-size:.65rem">{{ $s->classroom->class_name ?? '' }}</span>
              </label>
            </div>
            @endforeach
            @if($students->where('has_whatsapp', true)->count() == 0)
            <div class="text-muted text-center py-3"><small>No students with WhatsApp numbers. <a href="{{ route('whatsapp.index', ['tab'=>'contacts']) }}">Sync numbers first</a></small></div>
            @endif
          </div>
        </div>

        {{-- Teacher checkboxes --}}
        <div id="teacherRecipients" style="display:none">
          <div class="d-flex align-items-center gap-2 mb-2">
            <input type="checkbox" onchange="toggleAllCheckboxes(this, 'cb-teacher')" class="form-check-input">
            <span class="fw-semibold" style="font-size:.82rem">Select All Teachers ({{ $teachers->where('has_whatsapp', true)->count() }} with WhatsApp)</span>
          </div>
          <div style="max-height:220px;overflow:auto;border:1px solid #e9ecef;border-radius:8px;padding:.5rem">
            @foreach($teachers->where('has_whatsapp', true) as $t)
            <div class="form-check">
              <input class="form-check-input cb-teacher" type="checkbox" name="recipient_ids[]" value="{{ $t->id }}" id="tch-{{ $t->id }}">
              <label class="form-check-label" for="tch-{{ $t->id }}" style="font-size:.82rem">
                {{ $t->teacher_name }} <span class="text-muted">({{ $t->whatsapp_number }})</span>
              </label>
            </div>
            @endforeach
            @if($teachers->where('has_whatsapp', true)->count() == 0)
            <div class="text-muted text-center py-3"><small>No teachers with WhatsApp numbers.</small></div>
            @endif
          </div>
        </div>

        <div class="mt-3">
          <label class="form-label">Message</label>
          <textarea name="message" class="form-control" rows="4" required placeholder="Type your message here..."></textarea>
        </div>
        <div class="mt-3">
          <button type="submit" class="btn btn-success"><i class="bi bi-send me-1"></i>Send to Selected</button>
        </div>
      </form>
    </div>
  </div>

  {{-- ── Quick Actions Panel ── --}}
  <div class="col-lg-5">

    {{-- Test message --}}
    <div class="compose-area mb-4">
      <h6 class="form-section-title"><i class="bi bi-bug me-1"></i>Send Test Message</h6>
      <div class="row g-2">
        <div class="col-5">
          <input type="text" id="testTo" class="form-control form-control-sm" placeholder="92XXXXXXXXXX">
        </div>
        <div class="col-5">
          <input type="text" id="testMsg" class="form-control form-control-sm" placeholder="Test message">
        </div>
        <div class="col-2">
          <button id="testSendBtn" onclick="sendTestMessage()" class="btn btn-sm btn-success w-100">
            <i class="bi bi-send me-1"></i>Send Test
          </button>
        </div>
      </div>
    </div>

    {{-- Broadcast notice --}}
    <div class="compose-area">
      <h6 class="form-section-title"><i class="bi bi-broadcast me-1"></i>Broadcast Notice</h6>
      <form action="{{ route('whatsapp.broadcastNotice') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label class="form-label">Select Notice</label>
          <select name="notice_id" class="form-select" required>
            <option value="">— Select —</option>
            @foreach($notices as $n)
            <option value="{{ $n->id }}">{{ $n->title }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-warning"><i class="bi bi-megaphone me-1"></i>Broadcast to All Students</button>
        <div class="text-muted mt-2" style="font-size:.76rem">
          Will send to all {{ $studentWhatsapp }} students with WhatsApp numbers.
        </div>
      </form>
    </div>

  </div>
</div>

<script>
function toggleRecipientList() {
  const v = document.getElementById('recipientType').value;
  document.getElementById('studentRecipients').style.display = v === 'student' ? '' : 'none';
  document.getElementById('teacherRecipients').style.display = v === 'teacher' ? '' : 'none';
}
</script>
