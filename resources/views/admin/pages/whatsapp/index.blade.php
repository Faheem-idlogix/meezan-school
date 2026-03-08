@extends('admin.layout.master')
@section('css')
<style>
  .wa-stat{padding:1rem 1.2rem;border-radius:10px;position:relative;overflow:hidden}
  .wa-stat.wa-green{background:linear-gradient(135deg,#25d366 0%,#128c7e 100%);color:#fff}
  .wa-stat.wa-blue{background:linear-gradient(135deg,#4154f1 0%,#717ff5 100%);color:#fff}
  .wa-stat.wa-orange{background:linear-gradient(135deg,#fd7e14 0%,#ffc107 100%);color:#fff}
  .wa-stat.wa-red{background:linear-gradient(135deg,#dc3545 0%,#e35d6a 100%);color:#fff}
  .wa-stat .wa-val{font-size:1.6rem;font-weight:700;line-height:1}
  .wa-stat .wa-lbl{font-size:.7rem;text-transform:uppercase;letter-spacing:.8px;opacity:.85;font-weight:600}
  .wa-stat .wa-ico{position:absolute;right:1rem;top:50%;transform:translateY(-50%);font-size:2.8rem;opacity:.15}

  .contact-card{border:1px solid #e9ecef;border-radius:8px;padding:.75rem 1rem;display:flex;align-items:center;gap:.75rem;transition:all .2s}
  .contact-card:hover{border-color:#25d366;background:#f0fff4}
  .contact-card .cc-avatar{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.75rem;color:#fff;flex-shrink:0}
  .contact-card .cc-name{font-size:.84rem;font-weight:600;color:#012970}
  .contact-card .cc-phone{font-size:.76rem;color:#6c757d}
  .cc-badge-wa{font-size:.65rem;padding:2px 6px;border-radius:4px;font-weight:600}

  .compose-area{background:#f8faff;border:1px solid #e9ecef;border-radius:10px;padding:1.5rem}
</style>
@endsection
@section('content')
<main id="main" class="main">
    <div class="pagetitle d-flex align-items-center justify-content-between">
      <div>
        <h1><i class="bi bi-whatsapp text-success me-2"></i>WhatsApp Hub</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active">WhatsApp</li>
          </ol>
        </nav>
      </div>
      <div class="text-muted small"><i class="bi bi-calendar3 me-1"></i>{{ date('l, d M Y') }}</div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ══════════ STATS ROW ══════════ --}}
    <div class="row g-3 mb-4">
      <div class="col-xl-2 col-md-4">
        <div class="wa-stat wa-green">
          <div class="wa-lbl">Students on WA</div>
          <div class="wa-val">{{ $studentWhatsapp }} / {{ $studentTotal }}</div>
          <i class="bi bi-whatsapp wa-ico"></i>
        </div>
      </div>
      <div class="col-xl-2 col-md-4">
        <div class="wa-stat wa-blue">
          <div class="wa-lbl">Teachers on WA</div>
          <div class="wa-val">{{ $teacherWhatsapp }} / {{ $teacherTotal }}</div>
          <i class="bi bi-person-badge wa-ico"></i>
        </div>
      </div>
      <div class="col-xl-2 col-md-4">
        <div class="wa-stat wa-orange">
          <div class="wa-lbl">Messages Sent</div>
          <div class="wa-val">{{ $logStats['sent'] }}</div>
          <i class="bi bi-send wa-ico"></i>
        </div>
      </div>
      <div class="col-xl-2 col-md-4">
        <div class="wa-stat wa-red">
          <div class="wa-lbl">Failed Messages</div>
          <div class="wa-val">{{ $logStats['failed'] }}</div>
          <i class="bi bi-exclamation-triangle wa-ico"></i>
        </div>
      </div>
      <div class="col-xl-2 col-md-4">
        <div class="wa-stat" style="background:linear-gradient(135deg,#6f42c1 0%,#a470e8 100%);color:#fff">
          <div class="wa-lbl">Queued / Pending</div>
          <div class="wa-val">{{ $logStats['queued'] }}</div>
          <i class="bi bi-hourglass-split wa-ico"></i>
        </div>
      </div>
      <div class="col-xl-2 col-md-4">
        <div class="wa-stat" style="background:linear-gradient(135deg,#20c997 0%,#6edbb5 100%);color:#fff">
          <div class="wa-lbl">Today Remaining</div>
          <div class="wa-val">{{ $usage['daily_remaining'] }}</div>
          <i class="bi bi-speedometer2 wa-ico"></i>
        </div>
      </div>
    </div>

    {{-- ══════════ TABS ══════════ --}}
    <section class="section">
      <div class="card">
        <div class="card-body">
          <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
              <button class="nav-link @if($tab=='contacts') active @endif" data-bs-toggle="tab" data-bs-target="#wa-contacts" type="button"><i class="bi bi-people me-1"></i>Contacts & Sync</button>
            </li>
            <li class="nav-item">
              <button class="nav-link @if($tab=='compose') active @endif" data-bs-toggle="tab" data-bs-target="#wa-compose" type="button"><i class="bi bi-pencil-square me-1"></i>Compose Message</button>
            </li>
            <li class="nav-item">
              <button class="nav-link @if($tab=='logs') active @endif" data-bs-toggle="tab" data-bs-target="#wa-logs" type="button"><i class="bi bi-clock-history me-1"></i>Message Logs</button>
            </li>
            <li class="nav-item">
              <button class="nav-link @if($tab=='config') active @endif" data-bs-toggle="tab" data-bs-target="#wa-config" type="button"><i class="bi bi-gear me-1"></i>Configuration</button>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade @if($tab=='contacts') show active @endif" id="wa-contacts">
              @include('admin.pages.whatsapp._contacts')
            </div>
            <div class="tab-pane fade @if($tab=='compose') show active @endif" id="wa-compose">
              @include('admin.pages.whatsapp._compose')
            </div>
            <div class="tab-pane fade @if($tab=='logs') show active @endif" id="wa-logs">
              @include('admin.pages.whatsapp._logs')
            </div>
            <div class="tab-pane fade @if($tab=='config') show active @endif" id="wa-config">
              @include('admin.pages.whatsapp._config')
            </div>
          </div>
        </div>
      </div>
    </section>
</main>
@endsection

@section('script')
<script>
// ── Test send ──
function sendTestMessage() {
  const to  = $('#testTo').val();
  const msg = $('#testMsg').val();
  if (!to || !msg) { toastr.warning('Enter phone number and message'); return; }
  $('#testSendBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Sending...');
  $.ajax({
    url: '{{ route("whatsapp.sendTest") }}',
    method: 'POST',
    data: { _token: '{{ csrf_token() }}', to: to, message: msg },
    success: function(res) {
      if (res.success) toastr.success('Test message sent!');
      else toastr.error('Failed: ' + (res.error || 'Unknown error'));
    },
    error: function(xhr) { toastr.error('Error: ' + (xhr.responseJSON?.message || 'Request failed')); },
    complete: function() { $('#testSendBtn').prop('disabled', false).html('<i class="bi bi-send me-1"></i>Send Test'); }
  });
}

// ── Select all checkboxes ──
function toggleAllCheckboxes(el, cls) {
  document.querySelectorAll('.' + cls).forEach(cb => cb.checked = el.checked);
}
</script>
@endsection
