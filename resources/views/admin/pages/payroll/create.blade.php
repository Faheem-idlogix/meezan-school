@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
  <div class="pagetitle mb-3">
    <h1><i class="bi bi-plus-circle me-2 text-primary"></i>Generate Payroll</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li><li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Payroll</a></li><li class="breadcrumb-item active">Generate</li></ol></nav>
  </div>

  <form action="{{ route('payroll.store') }}" method="POST">
    @csrf
    <div class="row g-3">

      {{-- Left Column: Teacher & Period --}}
      <div class="col-lg-5">
        <div class="card">
          <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>Teacher & Period</h5></div>
          <div class="card-body">

            <div class="mb-3">
              <label class="form-label fw-semibold">Teacher <span class="text-danger">*</span></label>
              <select name="teacher_id" id="teacherSelect" class="form-select select2" required>
                <option value="">Select Teacher...</option>
                @foreach($teachers as $t)
                  <option value="{{ $t->id }}" data-salary="{{ $t->basic_salary }}" {{ old('teacher_id') == $t->id ? 'selected' : '' }}>
                    {{ $t->teacher_name }} {{ $t->employee_id ? "({$t->employee_id})" : '' }}
                  </option>
                @endforeach
              </select>
              @error('teacher_id')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="row g-2">
              <div class="col-6">
                <label class="form-label fw-semibold">Month <span class="text-danger">*</span></label>
                <select name="month" class="form-select" required>
                  @foreach($months as $m)
                    <option value="{{ $m['value'] }}" {{ old('month', date('n')) == $m['value'] ? 'selected' : '' }}>{{ $m['label'] }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-6">
                <label class="form-label fw-semibold">Year <span class="text-danger">*</span></label>
                <select name="year" class="form-select" required>
                  @foreach(range(date('Y'), date('Y')-3) as $y)
                    <option value="{{ $y }}" {{ old('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <hr>
            <h6 class="fw-semibold text-muted mb-2"><i class="bi bi-calendar-check me-1"></i>Attendance</h6>
            <div class="row g-2">
              <div class="col-6"><label class="form-label small">Working Days</label><input type="number" name="working_days" class="form-control" value="{{ old('working_days', 26) }}" min="0" max="31"></div>
              <div class="col-6"><label class="form-label small">Present Days</label><input type="number" name="present_days" class="form-control" value="{{ old('present_days', 26) }}" min="0" max="31"></div>
              <div class="col-6"><label class="form-label small">Absent Days</label><input type="number" name="absent_days" class="form-control" value="{{ old('absent_days', 0) }}" min="0"></div>
              <div class="col-6"><label class="form-label small">Leave Days</label><input type="number" name="leave_days" class="form-control" value="{{ old('leave_days', 0) }}" min="0"></div>
            </div>

            <hr>
            <div class="mb-2">
              <label class="form-label small">Payment Method</label>
              <select name="payment_method" class="form-select form-select-sm">
                <option value="cash">Cash</option>
                <option value="bank">Bank Transfer</option>
                <option value="cheque">Cheque</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label small">Notes</label>
              <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Optional notes...">{{ old('notes') }}</textarea>
            </div>

            @if($pendingAdvance > 0)
            <div class="alert alert-warning small p-2 mb-0">
              <i class="bi bi-exclamation-triangle me-1"></i>
              Pending advance: <strong>Rs. {{ number_format($pendingAdvance, 0) }}</strong>
            </div>
            @endif
          </div>
        </div>
      </div>

      {{-- Right Column: Salary Breakdown --}}
      <div class="col-lg-7">
        <div class="card">
          <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-calculator me-2 text-success"></i>Salary Breakdown</h5></div>
          <div class="card-body">

            <h6 class="fw-bold text-success mb-2">Earnings</h6>
            <div class="row g-2 mb-3">
              <div class="col-6"><label class="form-label small">Basic Salary <span class="text-danger">*</span></label><input type="number" name="basic_salary" id="basicSalary" class="form-control calc-field" value="{{ old('basic_salary', 0) }}" min="0" step="0.01" required></div>
              <div class="col-6"><label class="form-label small">House Rent</label><input type="number" name="house_rent_allowance" class="form-control calc-field" value="{{ old('house_rent_allowance', 0) }}" min="0" step="0.01"></div>
              <div class="col-6"><label class="form-label small">Medical</label><input type="number" name="medical_allowance" class="form-control calc-field" value="{{ old('medical_allowance', 0) }}" min="0" step="0.01"></div>
              <div class="col-6"><label class="form-label small">Transport</label><input type="number" name="transport_allowance" class="form-control calc-field" value="{{ old('transport_allowance', 0) }}" min="0" step="0.01"></div>
              <div class="col-6"><label class="form-label small">Bonus</label><input type="number" name="bonus" class="form-control calc-field" value="{{ old('bonus', 0) }}" min="0" step="0.01"></div>
              <div class="col-6"><label class="form-label small">Other Allowances</label><input type="number" name="other_allowances" class="form-control calc-field" value="{{ old('other_allowances', 0) }}" min="0" step="0.01"></div>
            </div>

            <h6 class="fw-bold text-danger mb-2">Deductions</h6>
            <div class="row g-2 mb-3">
              <div class="col-6"><label class="form-label small">Advance Recovery</label><input type="number" name="advance_deduction" class="form-control calc-field" value="{{ old('advance_deduction', $pendingAdvance) }}" min="0" step="0.01"></div>
              <div class="col-6"><label class="form-label small">Absence Deduction</label><input type="number" name="absence_deduction" class="form-control calc-field" value="{{ old('absence_deduction', 0) }}" min="0" step="0.01"></div>
              <div class="col-6"><label class="form-label small">Income Tax</label><input type="number" name="tax_deduction" class="form-control calc-field" value="{{ old('tax_deduction', 0) }}" min="0" step="0.01"></div>
              <div class="col-6"><label class="form-label small">Other Deductions</label><input type="number" name="other_deductions" class="form-control calc-field" value="{{ old('other_deductions', 0) }}" min="0" step="0.01"></div>
            </div>

            {{-- Live Summary --}}
            <div class="p-3 rounded" style="background:#f6f9ff;border:1px solid #e0e7ff">
              <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Gross Earnings</span><strong id="grossTotal" class="text-success">Rs. 0</strong></div>
              <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Total Deductions</span><strong id="deductTotal" class="text-danger">Rs. 0</strong></div>
              <hr class="my-1">
              <div class="d-flex justify-content-between"><span class="fw-bold">Net Salary</span><strong id="netTotal" class="fs-5" style="color:var(--ea-primary)">Rs. 0</strong></div>
            </div>

          </div>
        </div>

        <div class="d-flex gap-2 mt-3 justify-content-end">
          <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Save Payroll</button>
        </div>
      </div>
    </div>
  </form>
</main>
@endsection

@section('scripts')
<script>
// Auto-fill basic salary when teacher selected
$('#teacherSelect').on('change', function() {
  var salary = $(this).find(':selected').data('salary') || 0;
  $('#basicSalary').val(salary);
  calculate();
});

// Live calculation
function calculate() {
  var earnings = ['basic_salary','house_rent_allowance','medical_allowance','transport_allowance','bonus','other_allowances'];
  var deductions = ['advance_deduction','absence_deduction','tax_deduction','other_deductions'];
  var gross = earnings.reduce((s, n) => s + (parseFloat($('[name="'+n+'"]').val())||0), 0);
  var deduct = deductions.reduce((s, n) => s + (parseFloat($('[name="'+n+'"]').val())||0), 0);
  var net = gross - deduct;
  $('#grossTotal').text('Rs. ' + gross.toLocaleString('en-PK', {minimumFractionDigits:0}));
  $('#deductTotal').text('Rs. ' + deduct.toLocaleString('en-PK', {minimumFractionDigits:0}));
  $('#netTotal').text('Rs. ' + net.toLocaleString('en-PK', {minimumFractionDigits:0}));
}

$(document).on('input', '.calc-field', calculate);
calculate();
</script>
@endsection
