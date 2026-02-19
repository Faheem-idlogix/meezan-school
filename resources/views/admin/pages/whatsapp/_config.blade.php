{{-- ══════════ CONFIGURATION TAB ══════════ --}}
<div class="row g-4">
  <div class="col-lg-6">
    <div class="compose-area">
      <h6 class="form-section-title"><i class="bi bi-whatsapp text-success me-1"></i>WhatsApp API Configuration</h6>
      <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Provider</label>
            <select name="whatsapp_provider" class="form-select">
              @foreach(['ultramsg' => 'UltraMsg', 'twilio' => 'Twilio', 'wati' => 'WATI'] as $k => $v)
              <option value="{{ $k }}" {{ ($settings['whatsapp_provider']->value ?? '') === $k ? 'selected' : '' }}>{{ $v }}</option>
              @endforeach
            </select>
            <div class="form-text">Choose your WhatsApp API provider.</div>
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
            <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i>Save Configuration</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="compose-area">
      <h6 class="form-section-title"><i class="bi bi-info-circle me-1"></i>Setup Guide</h6>
      <div class="mb-3">
        <h6 class="fw-bold" style="font-size:.9rem">UltraMsg (Recommended)</h6>
        <ol style="font-size:.84rem;color:#444">
          <li>Go to <a href="https://ultramsg.com" target="_blank">ultramsg.com</a> and create an account</li>
          <li>Get your <strong>Instance ID</strong> and <strong>Token</strong></li>
          <li>Scan QR code to connect your WhatsApp number</li>
          <li>Paste credentials above and save</li>
        </ol>
      </div>
      <div class="mb-3">
        <h6 class="fw-bold" style="font-size:.9rem">Twilio</h6>
        <ol style="font-size:.84rem;color:#444">
          <li>Sign up at <a href="https://www.twilio.com" target="_blank">twilio.com</a></li>
          <li>Enable WhatsApp Sandbox in Messaging</li>
          <li>Use your <strong>Account SID</strong> as "From Number" and <strong>Auth Token</strong> as "API Key"</li>
        </ol>
      </div>
      <div>
        <h6 class="fw-bold" style="font-size:.9rem">WATI</h6>
        <ol style="font-size:.84rem;color:#444">
          <li>Create account at <a href="https://www.wati.io" target="_blank">wati.io</a></li>
          <li>Get your <strong>Bearer Token</strong> and <strong>Instance ID</strong></li>
        </ol>
      </div>
    </div>

    <div class="compose-area mt-3">
      <h6 class="form-section-title"><i class="bi bi-shield-check me-1"></i>Current Status</h6>
      <table class="table table-sm mb-0" style="font-size:.84rem">
        <tr>
          <td class="fw-semibold">Provider</td>
          <td>{{ ucfirst($settings['whatsapp_provider']->value ?? 'Not Set') }}</td>
        </tr>
        <tr>
          <td class="fw-semibold">API Key</td>
          <td>
            @if(!empty($settings['whatsapp_api_key']->value ?? ''))
              <span class="badge bg-success">Configured</span>
            @else
              <span class="badge bg-danger">Not Set</span>
            @endif
          </td>
        </tr>
        <tr>
          <td class="fw-semibold">Instance ID</td>
          <td>{{ !empty($settings['whatsapp_instance']->value ?? '') ? '✅ Set' : '❌ Not Set' }}</td>
        </tr>
        <tr>
          <td class="fw-semibold">From Number</td>
          <td>{{ $settings['whatsapp_from']->value ?? '—' }}</td>
        </tr>
      </table>
    </div>
  </div>
</div>
