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
            <select name="whatsapp_provider" class="form-select" id="waProvider" onchange="toggleProviderFields()">
              @foreach(['cloud_api' => 'WhatsApp Cloud API (FREE)', 'ultramsg' => 'UltraMsg (Paid)', 'twilio' => 'Twilio (Paid)', 'wati' => 'WATI (Paid)'] as $k => $v)
              <option value="{{ $k }}" {{ ($settings['whatsapp_provider']->value ?? 'cloud_api') === $k ? 'selected' : '' }}>{{ $v }}</option>
              @endforeach
            </select>
            <div class="form-text">Cloud API = FREE 1,000 conversations/month from Meta. Best for schools.</div>
          </div>
          <div class="col-12">
            <label class="form-label" id="lblApiKey">Access Token</label>
            <input type="text" name="whatsapp_api_key" class="form-control" value="{{ $settings['whatsapp_api_key']->value ?? '' }}" placeholder="Your access token">
            <div class="form-text" id="helpApiKey">Permanent token from Meta Developer Portal</div>
          </div>
          <div class="col-12" id="divFrom">
            <label class="form-label">From Number / Account SID</label>
            <input type="text" name="whatsapp_from" class="form-control" value="{{ $settings['whatsapp_from']->value ?? '' }}" placeholder="e.g. +1234567890">
          </div>
          <div class="col-12">
            <label class="form-label" id="lblInstance">Phone Number ID</label>
            <input type="text" name="whatsapp_instance" class="form-control" value="{{ $settings['whatsapp_instance']->value ?? '' }}" placeholder="Your Phone Number ID">
            <div class="form-text" id="helpInstance">From Meta Developer Dashboard > WhatsApp > API Setup</div>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i>Save Configuration</button>
          </div>
        </div>
      </form>
    </div>

    {{-- ── Rate Limits ── --}}
    <div class="compose-area mt-3">
      <h6 class="form-section-title"><i class="bi bi-speedometer2 text-primary me-1"></i>Rate Limits & Queue Settings</h6>
      <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Daily Limit</label>
            <input type="number" name="wa_daily_limit" class="form-control" value="{{ $settings['wa_daily_limit']->value ?? 200 }}" min="1" max="1000">
            <div class="form-text">Max messages per day (recommended: 200)</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Monthly Limit</label>
            <input type="number" name="wa_monthly_limit" class="form-control" value="{{ $settings['wa_monthly_limit']->value ?? 3000 }}" min="1" max="30000">
            <div class="form-text">Max messages per month (recommended: 3000)</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Delay Between Messages (seconds)</label>
            <input type="number" name="wa_delay_seconds" class="form-control" value="{{ $settings['wa_delay_seconds']->value ?? 8 }}" min="3" max="60">
            <div class="form-text">Gap between each queued message (min: 3s, recommended: 8s)</div>
          </div>
          <div class="col-md-6 d-flex align-items-end">
            <div class="w-100">
              <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i>Save Rate Limits</button>
            </div>
          </div>
          <div class="col-12">
            <div class="alert alert-info mb-0 py-2" style="font-size:.8rem">
              <i class="bi bi-info-circle me-1"></i>
              <strong>Why limits?</strong> WhatsApp may block numbers that send too many messages too fast.
              Safe zone: ~200/day with 8s gap between messages (~7-8 msgs/min).
              The queue worker sends messages gradually to keep your number safe.
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="compose-area">
      <h6 class="form-section-title"><i class="bi bi-info-circle me-1"></i>Setup Guide</h6>

      {{-- Cloud API Guide --}}
      <div class="mb-3 p-3" style="background:#e8f5e9;border-radius:8px;border:1px solid #c8e6c9">
        <h6 class="fw-bold" style="font-size:.9rem"><i class="bi bi-stars text-success me-1"></i>WhatsApp Cloud API — FREE (Recommended)</h6>
        <div class="mb-2" style="font-size:.82rem">
          <span class="badge bg-success">FREE</span> <span class="text-muted">1,000 conversations/month at no cost</span>
        </div>
        <ol style="font-size:.84rem;color:#444">
          <li>Go to <strong>developers.facebook.com</strong> and create a <strong>Meta Developer</strong> account</li>
          <li>Create a new App &rarr; select <strong>Business</strong> type</li>
          <li>Add <strong>WhatsApp</strong> product to your app</li>
          <li>In WhatsApp &rarr; <strong>API Setup</strong>, you will see:
            <ul>
              <li><strong>Phone Number ID</strong> &rarr; paste in "Phone Number ID" field above</li>
              <li><strong>Temporary Access Token</strong> &rarr; paste in "Access Token" above</li>
            </ul>
          </li>
          <li>For <strong>permanent token</strong>: Go to Business Settings &rarr; System Users &rarr; Generate Token with <code>whatsapp_business_messaging</code> permission</li>
          <li>Add your phone number or use the free test number provided by Meta</li>
        </ol>
        <div class="alert alert-success mb-0 py-2" style="font-size:.78rem">
          <i class="bi bi-check-circle me-1"></i>
          <strong>Free tier includes:</strong> 1,000 service conversations + 1,000 utility conversations/month.
          Perfect for school notifications, fee reminders, diary alerts.
        </div>
      </div>

      <div class="mb-3">
        <h6 class="fw-bold" style="font-size:.9rem">UltraMsg <span class="badge bg-warning text-dark" style="font-size:.65rem">Paid ~$39/mo</span></h6>
        <ol style="font-size:.84rem;color:#444">
          <li>Go to ultramsg.com and create an account</li>
          <li>Get your <strong>Instance ID</strong> and <strong>Token</strong></li>
          <li>Scan QR code to connect your WhatsApp number</li>
          <li>Paste credentials above and save</li>
        </ol>
      </div>
      <div class="mb-3">
        <h6 class="fw-bold" style="font-size:.9rem">Twilio <span class="badge bg-warning text-dark" style="font-size:.65rem">Paid</span></h6>
        <ol style="font-size:.84rem;color:#444">
          <li>Sign up at twilio.com</li>
          <li>Enable WhatsApp Sandbox in Messaging</li>
          <li>Use your <strong>Account SID</strong> as "From Number" and <strong>Auth Token</strong> as "API Key"</li>
        </ol>
      </div>
      <div>
        <h6 class="fw-bold" style="font-size:.9rem">WATI <span class="badge bg-warning text-dark" style="font-size:.65rem">Paid ~$49/mo</span></h6>
        <ol style="font-size:.84rem;color:#444">
          <li>Create account at wati.io</li>
          <li>Get your <strong>Bearer Token</strong> and <strong>Instance ID</strong></li>
        </ol>
      </div>
    </div>

    <div class="compose-area mt-3">
      <h6 class="form-section-title"><i class="bi bi-shield-check me-1"></i>Current Status</h6>
      <table class="table table-sm mb-0" style="font-size:.84rem">
        <tr>
          <td class="fw-semibold">Provider</td>
          <td>
            @php $pv = $settings['whatsapp_provider']->value ?? 'cloud_api'; @endphp
            @if($pv === 'cloud_api')
              <span class="badge bg-success">Cloud API (FREE)</span>
            @else
              {{ ucfirst($pv) }}
            @endif
          </td>
        </tr>
        <tr>
          <td class="fw-semibold">Access Token</td>
          <td>
            @if(!empty($settings['whatsapp_api_key']->value ?? ''))
              <span class="badge bg-success">Configured</span>
            @else
              <span class="badge bg-danger">Not Set</span>
            @endif
          </td>
        </tr>
        <tr>
          <td class="fw-semibold">Phone Number ID</td>
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

<script>
function toggleProviderFields() {
  const p = document.getElementById('waProvider').value;
  const isCloud = p === 'cloud_api';
  const isTwilio = p === 'twilio';

  document.getElementById('lblApiKey').textContent = isCloud ? 'Access Token' : (isTwilio ? 'Auth Token' : 'API Key / Token');
  document.getElementById('helpApiKey').textContent = isCloud ? 'Permanent token from Meta Developer Portal' : 'Your provider API key';
  document.getElementById('divFrom').style.display = (isCloud ? 'none' : '');
  document.getElementById('lblInstance').textContent = isCloud ? 'Phone Number ID' : (p === 'ultramsg' || p === 'wati' ? 'Instance ID' : 'Account SID');
  document.getElementById('helpInstance').textContent = isCloud ? 'From Meta Developer Dashboard > WhatsApp > API Setup' : 'Your provider instance/account ID';
}
toggleProviderFields();
</script>
