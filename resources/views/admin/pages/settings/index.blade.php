@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
      <h1>Settings</h1>
      <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Settings</li>
      </ol></nav>
    </div>

    <section class="section">
      <div class="row">

        <!-- School Settings -->
        <div class="col-lg-6">
          <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
              <h5 class="card-title mb-4"><i class="bi bi-building me-2"></i>School Information</h5>
              <form action="{{ route('settings.update') }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label">School Name</label>
                    <input type="text" name="school_name" class="form-control" value="{{ $settings['school_name']->value ?? '' }}">
                  </div>
                  <div class="col-12">
                    <label class="form-label">School Phone</label>
                    <input type="text" name="school_phone" class="form-control" value="{{ $settings['school_phone']->value ?? '' }}">
                  </div>
                  <div class="col-12">
                    <label class="form-label">School Address</label>
                    <textarea name="school_address" class="form-control" rows="3">{{ $settings['school_address']->value ?? '' }}</textarea>
                  </div>
                  <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Settings</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- WhatsApp Settings -->
        <div class="col-lg-6">
          <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
              <h5 class="card-title mb-4"><i class="bi bi-whatsapp text-success me-2"></i>WhatsApp Integration</h5>
              <form action="{{ route('settings.update') }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label">Provider</label>
                    <select name="whatsapp_provider" class="form-select">
                      @foreach(['ultramsg' => 'UltraMsg', 'twilio' => 'Twilio', 'wati' => 'WATI'] as $k => $v)
                      <option value="{{ $k }}" {{ ($settings['whatsapp_provider']->value ?? '') === $k ? 'selected' : '' }}>{{ $v }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-12">
                    <label class="form-label">API Key / Auth Token</label>
                    <input type="text" name="whatsapp_api_key" class="form-control" value="{{ $settings['whatsapp_api_key']->value ?? '' }}" placeholder="Your API key">
                  </div>
                  <div class="col-12">
                    <label class="form-label">From Number / Account SID</label>
                    <input type="text" name="whatsapp_from" class="form-control" value="{{ $settings['whatsapp_from']->value ?? '' }}" placeholder="e.g. +1234567890">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Instance ID (UltraMsg / WATI)</label>
                    <input type="text" name="whatsapp_instance" class="form-control" value="{{ $settings['whatsapp_instance']->value ?? '' }}" placeholder="instance12345">
                  </div>
                  <div class="col-12">
                    <button type="submit" class="btn btn-success"><i class="bi bi-whatsapp me-1"></i> Save WhatsApp Settings</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Test WhatsApp -->
        <div class="col-lg-12">
          <div class="card shadow-sm border-0">
            <div class="card-body p-4">
              <h5 class="card-title mb-4"><i class="bi bi-send me-2"></i>Send Test WhatsApp Message</h5>
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">To (Phone Number)</label>
                  <input type="text" id="testTo" class="form-control" placeholder="92XXXXXXXXXX">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Message</label>
                  <input type="text" id="testMsg" class="form-control" placeholder="Test message from Meezan School">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                  <button onclick="sendTestWhatsapp()" class="btn btn-success w-100">
                    <i class="bi bi-send me-1"></i> Send
                  </button>
                </div>
              </div>

              <hr>
              <h5 class="card-title mb-3"><i class="bi bi-broadcast me-2"></i>Broadcast Notice via WhatsApp</h5>
              <form action="{{ route('settings.broadcastNotice') }}" method="POST">
                @csrf
                <div class="row g-3 align-items-end">
                  <div class="col-md-6">
                    <label class="form-label">Select Notice</label>
                    <select name="notice_id" class="form-select">
                      @foreach(\App\Models\Notice::where('is_active',1)->latest()->get() as $n)
                      <option value="{{ $n->id }}">{{ $n->title }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-3">
                    <button type="submit" class="btn btn-warning">
                      <i class="bi bi-megaphone me-1"></i> Broadcast to All Contacts
                    </button>
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

@section('script')
<script>
function sendTestWhatsapp() {
  const to  = $('#testTo').val();
  const msg = $('#testMsg').val();
  if (!to || !msg) { toastr.warning('Please fill in phone and message'); return; }

  $.ajax({
    url: '{{ route("settings.sendWhatsApp") }}',
    method: 'POST',
    data: { _token: '{{ csrf_token() }}', to: to, message: msg },
    success: function(res) {
      if (res.success) toastr.success('Message sent successfully!');
      else toastr.error('Failed: ' + (res.error || 'Unknown error'));
    },
    error: function(xhr) { toastr.error('Error: ' + xhr.responseJSON?.message || 'Request failed'); }
  });
}
</script>
@endsection
