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
              <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="row g-3">

                  {{-- Current Logo Preview --}}
                  <div class="col-12 text-center">
                    <div class="mb-2">
                      @if($settings['school_logo']->value ?? false)
                        <img src="{{ asset('storage/' . $settings['school_logo']->value) }}" alt="School Logo"
                             style="max-height:80px; border-radius:8px; border:2px solid #dee2e6; padding:4px;">
                      @else
                        <div class="d-inline-flex align-items-center justify-content-center rounded bg-light text-muted" style="width:80px;height:80px;">
                          <i class="bi bi-image fs-1"></i>
                        </div>
                      @endif
                    </div>
                    <small class="text-muted">Current Logo</small>
                  </div>

                  {{-- Logo Upload --}}
                  <div class="col-12">
                    <label class="form-label">School Logo</label>
                    <input type="file" name="school_logo" class="form-control @error('school_logo') is-invalid @enderror"
                           accept="image/*">
                    <small class="text-muted">JPG, PNG, ICO, SVG — Max 2 MB</small>
                    @error('school_logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>

                  <div class="col-12">
                    <label class="form-label">School Name <span class="text-danger">*</span></label>
                    <input type="text" name="school_name" class="form-control"
                           value="{{ $settings['school_name']->value ?? '' }}"
                           placeholder="e.g. The Meezan School">
                  </div>

                  <div class="col-12">
                    <label class="form-label">Tagline</label>
                    <input type="text" name="school_tagline" class="form-control"
                           value="{{ $settings['school_tagline']->value ?? '' }}"
                           placeholder="e.g. Educating Tomorrow's Leaders">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Phone / WhatsApp</label>
                    <input type="text" name="school_phone" class="form-control"
                           value="{{ $settings['school_phone']->value ?? '' }}"
                           placeholder="e.g. 03001234567">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="school_email" class="form-control"
                           value="{{ $settings['school_email']->value ?? '' }}"
                           placeholder="e.g. info@school.com">
                  </div>

                  <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="school_address" class="form-control" rows="2"
                              placeholder="e.g. Chak No.149/9L, Sahiwal">{{ $settings['school_address']->value ?? '' }}</textarea>
                  </div>

                  <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save School Info</button>
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
                  <input type="text" id="testMsg" class="form-control" placeholder="Test message from {{ setting('school_name', 'School') }}">
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

        {{-- Report & Invoice View Settings --}}
        <div class="col-lg-6">
          <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
              <h5 class="card-title mb-4"><i class="bi bi-file-earmark-bar-graph me-2"></i>Report & Invoice Settings</h5>
              <form action="{{ route('settings.update') }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label">Invoice / Reports View Mode</label>
                    <select name="report_view_mode" class="form-select">
                      <option value="basic" {{ ($settings['report_view_mode']?->value ?? 'basic') === 'basic' ? 'selected' : '' }}>
                        Basic View — Standard reports layout
                      </option>
                      <option value="advanced" {{ ($settings['report_view_mode']?->value ?? 'basic') === 'advanced' ? 'selected' : '' }}>
                        Advanced View — Detailed reports with charts & breakdowns
                      </option>
                    </select>
                    <small class="text-muted d-block mt-1">
                      <i class="bi bi-info-circle me-1"></i>
                      <strong>Basic:</strong> Standard summary-style reports and invoices.<br>
                      <strong>Advanced:</strong> Detailed view with breakdowns, charts, export options, and extended analytics.
                    </small>
                  </div>

                  <div class="col-12">
                    <label class="form-label">Invoice Layout</label>
                    <select name="invoice_layout" class="form-select">
                      <option value="compact" {{ ($settings['invoice_layout']?->value ?? 'compact') === 'compact' ? 'selected' : '' }}>
                        Compact — Single-page invoice
                      </option>
                      <option value="detailed" {{ ($settings['invoice_layout']?->value ?? 'compact') === 'detailed' ? 'selected' : '' }}>
                        Detailed — Full breakdown with fee components
                      </option>
                    </select>
                  </div>

                  <div class="col-12">
                    <div class="form-check form-switch">
                      <input type="hidden" name="show_fee_breakdown" value="0">
                      <input class="form-check-input" type="checkbox" name="show_fee_breakdown" value="1"
                             id="showFeeBreakdown" {{ ($settings['show_fee_breakdown']?->value ?? '0') === '1' ? 'checked' : '' }}>
                      <label class="form-check-label" for="showFeeBreakdown">Show fee breakdown on invoices</label>
                    </div>
                  </div>

                  <div class="col-12">
                    <div class="form-check form-switch">
                      <input type="hidden" name="show_payment_history" value="0">
                      <input class="form-check-input" type="checkbox" name="show_payment_history" value="1"
                             id="showPaymentHistory" {{ ($settings['show_payment_history']?->value ?? '0') === '1' ? 'checked' : '' }}>
                      <label class="form-check-label" for="showPaymentHistory">Show payment history on student invoices</label>
                    </div>
                  </div>

                  <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Report Settings</button>
                  </div>
                </div>
              </form>

              {{-- Current Mode Indicator --}}
              <div class="mt-3 p-3 rounded {{ ($settings['report_view_mode']?->value ?? 'basic') === 'advanced' ? 'bg-primary bg-opacity-10' : 'bg-light' }}">
                <small>
                  <i class="bi bi-eye me-1"></i>
                  Currently active: <strong>{{ ucfirst($settings['report_view_mode']?->value ?? 'basic') }} View</strong>
                </small>
              </div>
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
