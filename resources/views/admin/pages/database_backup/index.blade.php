@extends('admin.layout.master')
@section('css')
<style>
  .bk-stat{padding:1rem 1.2rem;border-radius:10px;position:relative;overflow:hidden}
  .bk-stat .bk-val{font-size:1.5rem;font-weight:700;line-height:1}
  .bk-stat .bk-lbl{font-size:.7rem;text-transform:uppercase;letter-spacing:.8px;opacity:.85;font-weight:600}
  .bk-stat .bk-ico{position:absolute;right:1rem;top:50%;transform:translateY(-50%);font-size:2.5rem;opacity:.15}
  .smtp-preset{cursor:pointer;border:2px solid #e9ecef;border-radius:10px;padding:.8rem;text-align:center;transition:all .2s}
  .smtp-preset:hover{border-color:#4154f1;background:#f0f4ff}
  .smtp-preset.active{border-color:#4154f1;background:#e8edff}
  .smtp-preset img,.smtp-preset i{font-size:1.8rem}
  .smtp-preset .sp-name{font-size:.82rem;font-weight:600;margin-top:.3rem}
</style>
@endsection
@section('content')
<main id="main" class="main">
  <div class="pagetitle d-flex align-items-center justify-content-between">
    <div>
      <h1><i class="bi bi-database-fill-gear me-2 text-primary"></i>Database Backup</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item active">Database Backup</li>
        </ol>
      </nav>
    </div>
    <form action="{{ route('backup.create') }}" method="POST" class="d-inline">
      @csrf
      <button type="submit" class="btn btn-success" onclick="this.disabled=true;this.innerHTML='<span class=\'spinner-border spinner-border-sm me-1\'></span>Creating...';this.form.submit();">
        <i class="bi bi-plus-circle me-1"></i>Create New Backup
      </button>
    </form>
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif
  @if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  {{-- ══════════ STATS ══════════ --}}
  <div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
      <div class="bk-stat" style="background:linear-gradient(135deg,#4154f1,#717ff5);color:#fff">
        <div class="bk-lbl">Total Backups</div>
        <div class="bk-val">{{ count($backups) }}</div>
        <i class="bi bi-database bk-ico"></i>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="bk-stat" style="background:linear-gradient(135deg,#2eca6a,#20c997);color:#fff">
        <div class="bk-lbl">Latest Backup</div>
        <div class="bk-val" style="font-size:1rem">{{ count($backups) > 0 ? $backups[0]['created_at'] : 'None' }}</div>
        <i class="bi bi-clock-history bk-ico"></i>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="bk-stat" style="background:linear-gradient(135deg,#fd7e14,#ffc107);color:#fff">
        <div class="bk-lbl">Total Size</div>
        <div class="bk-val">{{ number_format(collect($backups)->sum('size'), 1) }} KB</div>
        <i class="bi bi-hdd bk-ico"></i>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="bk-stat" style="background:linear-gradient(135deg,#6f42c1,#a470e8);color:#fff">
        <div class="bk-lbl">Database</div>
        <div class="bk-val" style="font-size:1rem">{{ config('database.connections.mysql.database') }}</div>
        <i class="bi bi-server bk-ico"></i>
      </div>
    </div>
  </div>

  {{-- ══════════ TABS ══════════ --}}
  <section class="section">
    <div class="card">
      <div class="card-body">
        <ul class="nav nav-tabs mb-3" role="tablist">
          <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-backups" type="button">
              <i class="bi bi-archive me-1"></i>Backups
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-smtp" type="button">
              <i class="bi bi-envelope-gear me-1"></i>Mail / SMTP Settings
            </button>
          </li>
        </ul>

        <div class="tab-content">
          {{-- ── Backups Tab ── --}}
          <div class="tab-pane fade show active" id="tab-backups">
            @if(count($backups) === 0)
              <div class="text-center py-5">
                <i class="bi bi-database-slash text-muted" style="font-size:3rem"></i>
                <p class="text-muted mt-2">No backups yet. Click <strong>"Create New Backup"</strong> to generate your first backup.</p>
              </div>
            @else
              <div class="table-responsive">
                <table class="table datatable">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Filename</th>
                      <th>Size</th>
                      <th>Created</th>
                      <th>Age</th>
                      <th class="text-center" style="width:220px">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($backups as $i => $bk)
                    <tr>
                      <td>{{ $i + 1 }}</td>
                      <td><i class="bi bi-file-earmark-zip text-warning me-1"></i>{{ $bk['filename'] }}</td>
                      <td>{{ $bk['size'] }} KB</td>
                      <td>{{ $bk['created_at'] }}</td>
                      <td><span class="text-muted">{{ $bk['age'] }}</span></td>
                      <td class="text-center">
                        <a href="{{ route('backup.download', $bk['filename']) }}" class="btn btn-sm btn-primary" title="Download">
                          <i class="bi bi-download"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-success" title="Email" onclick="openEmailModal('{{ $bk['filename'] }}')">
                          <i class="bi bi-envelope"></i>
                        </button>
                        <form action="{{ route('backup.destroy', $bk['filename']) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this backup?')">
                          @csrf @method('DELETE')
                          <button class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          </div>

          {{-- ── SMTP Settings Tab ── --}}
          <div class="tab-pane fade" id="tab-smtp">
            <div class="row g-4">
              {{-- Quick Presets --}}
              <div class="col-12">
                <h6 class="fw-bold mb-3" style="font-size:.9rem"><i class="bi bi-lightning me-1"></i>Quick SMTP Presets</h6>
                <div class="row g-2">
                  @php
                    $presets = [
                      ['name'=>'Gmail','host'=>'smtp.gmail.com','port'=>587,'enc'=>'tls','icon'=>'bi bi-google text-danger'],
                      ['name'=>'Hostinger','host'=>'smtp.hostinger.com','port'=>465,'enc'=>'ssl','icon'=>'bi bi-hdd-rack text-primary'],
                      ['name'=>'Outlook','host'=>'smtp.office365.com','port'=>587,'enc'=>'tls','icon'=>'bi bi-microsoft text-info'],
                      ['name'=>'Yahoo','host'=>'smtp.mail.yahoo.com','port'=>465,'enc'=>'ssl','icon'=>'bi bi-envelope text-purple'],
                      ['name'=>'Mailtrap','host'=>'sandbox.smtp.mailtrap.io','port'=>2525,'enc'=>'tls','icon'=>'bi bi-mailbox text-success'],
                      ['name'=>'Custom','host'=>'','port'=>587,'enc'=>'tls','icon'=>'bi bi-gear text-secondary'],
                    ];
                  @endphp
                  @foreach($presets as $p)
                  <div class="col-lg-2 col-md-3 col-4">
                    <div class="smtp-preset" onclick="applySmtpPreset('{{ $p['host'] }}', {{ $p['port'] }}, '{{ $p['enc'] }}', this)">
                      <i class="{{ $p['icon'] }}"></i>
                      <div class="sp-name">{{ $p['name'] }}</div>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>

              {{-- SMTP Form --}}
              <div class="col-lg-7">
                <div class="p-3" style="background:#f8faff;border:1px solid #e9ecef;border-radius:10px">
                  <h6 class="fw-bold mb-3" style="font-size:.9rem"><i class="bi bi-envelope-gear me-1"></i>SMTP Configuration</h6>
                  <form action="{{ route('backup.saveMailSettings') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                      <div class="col-md-8">
                        <label class="form-label">SMTP Host</label>
                        <input type="text" name="mail_host" id="smtpHost" class="form-control" value="{{ $settings['mail_host']->value ?? '' }}" placeholder="smtp.gmail.com" required>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">Port</label>
                        <input type="number" name="mail_port" id="smtpPort" class="form-control" value="{{ $settings['mail_port']->value ?? 587 }}" required>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Username / Email</label>
                        <input type="text" name="mail_username" class="form-control" value="{{ $settings['mail_username']->value ?? '' }}" placeholder="your@email.com" required>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Password / App Password</label>
                        <input type="password" name="mail_password" class="form-control" value="{{ $settings['mail_password']->value ?? '' }}" placeholder="••••••••" required>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">Encryption</label>
                        <select name="mail_encryption" id="smtpEnc" class="form-select">
                          <option value="tls" {{ ($settings['mail_encryption']->value ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                          <option value="ssl" {{ ($settings['mail_encryption']->value ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                          <option value="none" {{ ($settings['mail_encryption']->value ?? '') === 'none' ? 'selected' : '' }}>None</option>
                        </select>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">From Email</label>
                        <input type="email" name="mail_from" class="form-control" value="{{ $settings['mail_from']->value ?? '' }}" placeholder="noreply@school.com" required>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">From Name</label>
                        <input type="text" name="mail_from_name" class="form-control" value="{{ $settings['mail_from_name']->value ?? setting('school_name', 'Meezan School') }}" placeholder="School Name">
                      </div>
                      <div class="col-12">
                        <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i>Save SMTP Settings</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              {{-- Test & Guide --}}
              <div class="col-lg-5">
                <div class="p-3 mb-3" style="background:#f0fff4;border:1px solid #c8e6c9;border-radius:10px">
                  <h6 class="fw-bold mb-3" style="font-size:.9rem"><i class="bi bi-send-check me-1 text-success"></i>Test SMTP Connection</h6>
                  <form action="{{ route('backup.testMail') }}" method="POST">
                    @csrf
                    <div class="input-group">
                      <input type="email" name="test_email" class="form-control" placeholder="test@email.com" required>
                      <button type="submit" class="btn btn-success"><i class="bi bi-send me-1"></i>Send Test</button>
                    </div>
                    <div class="form-text mt-1">Save settings first, then send a test email to verify.</div>
                  </form>
                </div>

                <div class="p-3" style="background:#fff8e1;border:1px solid #ffe082;border-radius:10px">
                  <h6 class="fw-bold mb-2" style="font-size:.9rem"><i class="bi bi-lightbulb me-1 text-warning"></i>Setup Guide</h6>
                  <div style="font-size:.82rem;color:#555">
                    <p class="mb-2"><strong>Gmail:</strong></p>
                    <ol class="mb-3" style="padding-left:1.2rem">
                      <li>Enable 2-Step Verification in Google Account</li>
                      <li>Go to <strong>Security → App Passwords</strong></li>
                      <li>Generate app password for "Mail"</li>
                      <li>Use that 16-char password (not your Gmail password)</li>
                    </ol>
                    <p class="mb-2"><strong>Hostinger:</strong></p>
                    <ol class="mb-3" style="padding-left:1.2rem">
                      <li>Go to Hostinger <strong>hPanel → Emails</strong></li>
                      <li>Create email account (e.g. info@yourdomain.com)</li>
                      <li>Host: <code>smtp.hostinger.com</code>, Port: <code>465</code>, SSL</li>
                      <li>Use full email as username & its password</li>
                    </ol>
                    <p class="mb-2"><strong>Mailtrap (Free for testing):</strong></p>
                    <ol style="padding-left:1.2rem">
                      <li>Sign up at mailtrap.io (free plan)</li>
                      <li>Get SMTP credentials from inbox settings</li>
                      <li>Great for testing without sending real emails</li>
                    </ol>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

{{-- ══════════ Email Modal ══════════ --}}
<div class="modal fade" id="emailModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('backup.email') }}" method="POST">
        @csrf
        <input type="hidden" name="filename" id="emailFilename">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-envelope-fill text-success me-2"></i>Email Backup</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted mb-3" style="font-size:.85rem">
            Send backup <strong id="emailFileLabel"></strong> to an email address.
          </p>
          <div class="mb-3">
            <label class="form-label">Recipient Email</label>
            <input type="email" name="email" class="form-control" placeholder="admin@school.com" required value="{{ $settings['mail_from']->value ?? '' }}">
          </div>
          @if(empty($settings['mail_host']->value ?? ''))
          <div class="alert alert-warning py-2 mb-0" style="font-size:.82rem">
            <i class="bi bi-exclamation-triangle me-1"></i>SMTP not configured yet. Go to <strong>Mail / SMTP Settings</strong> tab first.
          </div>
          @endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success"><i class="bi bi-send me-1"></i>Send Email</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
function openEmailModal(filename) {
  document.getElementById('emailFilename').value = filename;
  document.getElementById('emailFileLabel').textContent = filename;
  new bootstrap.Modal(document.getElementById('emailModal')).show();
}

function applySmtpPreset(host, port, enc, el) {
  document.getElementById('smtpHost').value = host;
  document.getElementById('smtpPort').value = port;
  document.getElementById('smtpEnc').value  = enc;
  document.querySelectorAll('.smtp-preset').forEach(e => e.classList.remove('active'));
  el.classList.add('active');
}
</script>
@endsection
