@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
      <h1>Send Notification</h1>
      <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('notifications.index') }}">Notifications</a></li>
        <li class="breadcrumb-item active">Send</li>
      </ol></nav>
    </div>

    <section class="section">
      <form action="{{ route('notifications.store') }}" method="POST">
        @csrf
        <div class="row">
          {{-- Notification Content --}}
          <div class="col-lg-7">
            <div class="card shadow-sm border-0">
              <div class="card-body p-4">
                <h5 class="card-title">Notification Content</h5>

                <div class="mb-3">
                  <label class="form-label">Title <span class="text-danger">*</span></label>
                  <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                         value="{{ old('title') }}" placeholder="e.g. Fee Reminder for March">
                  @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                  <label class="form-label">Message <span class="text-danger">*</span></label>
                  <textarea name="message" rows="4" class="form-control @error('message') is-invalid @enderror"
                            placeholder="Write the notification message here...">{{ old('message') }}</textarea>
                  @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror">
                      <option value="info"    {{ old('type') === 'info' ? 'selected' : '' }}>ℹ️ Info</option>
                      <option value="success" {{ old('type') === 'success' ? 'selected' : '' }}>✅ Success</option>
                      <option value="warning" {{ old('type') === 'warning' ? 'selected' : '' }}>⚠️ Warning</option>
                      <option value="danger"  {{ old('type') === 'danger' ? 'selected' : '' }}>🔴 Critical / Danger</option>
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Link (optional)</label>
                    <input type="text" name="link" class="form-control" value="{{ old('link') }}"
                           placeholder="e.g. /fee_voucher or leave blank">
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- Target --}}
          <div class="col-lg-5">
            <div class="card shadow-sm border-0">
              <div class="card-body p-4">
                <h5 class="card-title">Send To</h5>

                <div class="mb-3">
                  <label class="form-label">Target Audience <span class="text-danger">*</span></label>
                  <select name="target_type" id="targetType" class="form-select @error('target_type') is-invalid @enderror">
                    <option value="all"   {{ old('target_type') === 'all'   ? 'selected' : '' }}>📢 All Users</option>
                    <option value="role"  {{ old('target_type') === 'role'  ? 'selected' : '' }}>👥 By Role</option>
                    <option value="class" {{ old('target_type') === 'class' ? 'selected' : '' }}>🏫 By Class</option>
                    <option value="user"  {{ old('target_type') === 'user'  ? 'selected' : '' }}>👤 Specific Users</option>
                  </select>
                  @error('target_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Role selection --}}
                <div class="mb-3 target-field" id="targetRole" style="display:none;">
                  <label class="form-label">Select Role <span class="text-danger">*</span></label>
                  <select name="target_role_id" class="form-select @error('target_role_id') is-invalid @enderror">
                    <option value="">Choose role...</option>
                    @foreach($roles as $role)
                      <option value="{{ $role->id }}" {{ old('target_role_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->display_name }} ({{ $role->users()->count() }} users)
                      </option>
                    @endforeach
                  </select>
                  @error('target_role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Class selection --}}
                <div class="mb-3 target-field" id="targetClass" style="display:none;">
                  <label class="form-label">Select Class <span class="text-danger">*</span></label>
                  <select name="target_class_id" class="form-select @error('target_class_id') is-invalid @enderror">
                    <option value="">Choose class...</option>
                    @foreach($classes as $class)
                      <option value="{{ $class->id }}" {{ old('target_class_id') == $class->id ? 'selected' : '' }}>
                        {{ $class->class_name }}
                      </option>
                    @endforeach
                  </select>
                  @error('target_class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- User selection --}}
                <div class="mb-3 target-field" id="targetUser" style="display:none;">
                  <label class="form-label">Select Users <span class="text-danger">*</span></label>
                  <div class="border rounded p-2" style="max-height:200px;overflow-y:auto;">
                    @foreach($users as $u)
                    <div class="form-check">
                      <input type="checkbox" class="form-check-input" name="target_user_ids[]"
                             value="{{ $u->id }}" id="user-{{ $u->id }}"
                             {{ in_array($u->id, old('target_user_ids', [])) ? 'checked' : '' }}>
                      <label class="form-check-label small" for="user-{{ $u->id }}">
                        {{ $u->name }} <span class="text-muted">({{ $u->email }})</span>
                      </label>
                    </div>
                    @endforeach
                  </div>
                  @error('target_user_ids')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="alert alert-info small py-2" id="targetSummary">
                  <i class="bi bi-info-circle me-1"></i>
                  <span id="targetSummaryText">This notification will be sent to all users.</span>
                </div>

                <div class="d-flex gap-2 mt-3">
                  <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i> Send Notification</button>
                  <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </section>
</main>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const targetType = document.getElementById('targetType');
    const fields = { role: 'targetRole', class: 'targetClass', user: 'targetUser' };
    const summaries = {
        all:   'This notification will be sent to all users.',
        role:  'This notification will be sent to all users with the selected role.',
        class: 'This notification will be sent to students & teachers in the selected class.',
        user:  'This notification will be sent to the selected users only.'
    };

    function toggleFields() {
        document.querySelectorAll('.target-field').forEach(el => el.style.display = 'none');
        const val = targetType.value;
        if (fields[val]) document.getElementById(fields[val]).style.display = 'block';
        document.getElementById('targetSummaryText').textContent = summaries[val] || '';
    }

    targetType.addEventListener('change', toggleFields);
    toggleFields();
});
</script>
@endsection
@endsection
