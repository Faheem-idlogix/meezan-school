@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
      <h1>Edit Class Fee Voucher</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('fee_voucher') }}">Vouchers</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          @if (session('success'))
            <div class="alert alert-success alert-dismissible border-0 fade show" role="alert">
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              {{ session('success') }}
            </div>
          @endif

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">
                {{ $classFeeVoucher->name }}
                <small class="text-muted">— changes apply to every student in this class voucher</small>
              </h5>

              <form action="{{ route('class_fee_voucher.update', $classFeeVoucher->class_fee_voucher_id) }}" method="post">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                  <div class="col-lg-6">
                    <label class="col-form-label">Class</label>
                    <input type="text" class="form-control" disabled
                           value="{{ trim(($classFeeVoucher->classroom->class_name ?? '') . ' ' . ($classFeeVoucher->classroom->section_name ?? '')) }}">
                  </div>
                  <div class="col-lg-6">
                    <label class="col-form-label">Current Label</label>
                    <input type="text" class="form-control" disabled value="{{ $classFeeVoucher->month }}">
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-lg-12">
                    <label for="months" class="col-form-label">Voucher Month(s) <sup>*</sup>
                      <small class="text-muted">— hold Ctrl / Cmd to select multiple. Removing a month rewrites the voucher label for every student.</small>
                    </label>
                    <select name="months[]" id="months" class="form-select js-months-multi" multiple required>
                      @foreach($monthOptions as $val => $label)
                        <option value="{{ $val }}" @selected(in_array($val, old('months', $selectedMonths)))>{{ $label }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-lg-6">
                    <label class="col-form-label">Academic Fee</label>
                    <input type="number" name="academic_fee" class="form-control" value="{{ old('academic_fee', $template->academic_fee ?? 0) }}">
                  </div>
                  <div class="col-lg-6">
                    <label class="col-form-label">Arrears (admin entered)</label>
                    <input type="number" name="arrears" class="form-control" value="{{ old('arrears', 0) }}">
                    <small class="text-muted">Per-student carry-forward from the previous month is recomputed automatically.</small>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-lg-6">
                    <label class="col-form-label">Issue Date</label>
                    <input type="date" name="issue_date" class="form-control"
                           value="{{ old('issue_date', $template ? \Carbon\Carbon::parse($template->getRawOriginal('issue_date'))->format('Y-m-d') : '') }}">
                  </div>
                  <div class="col-lg-6">
                    <label class="col-form-label">Submit Date</label>
                    <input type="date" name="submit_date" class="form-control"
                           value="{{ old('submit_date', $template && $template->getRawOriginal('submit_date') ? \Carbon\Carbon::parse($template->getRawOriginal('submit_date'))->format('Y-m-d') : '') }}">
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-lg-6">
                    <label class="col-form-label">Stationery Charges</label>
                    <input type="number" name="stationery_charges" class="form-control" value="{{ old('stationery_charges', $template->stationery_charges ?? 0) }}">
                  </div>
                  <div class="col-lg-6">
                    <label class="col-form-label">Test Charges</label>
                    <input type="number" name="test_series_charges" class="form-control" value="{{ old('test_series_charges', $template->test_series_charges ?? 0) }}">
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-lg-6">
                    <label class="col-form-label">Exam Charges</label>
                    <input type="number" name="exam_charges" class="form-control" value="{{ old('exam_charges', $template->exam_charges ?? 0) }}">
                  </div>
                  <div class="col-lg-6">
                    <label class="col-form-label">Notebook / Diary Charges</label>
                    <input type="number" name="notebook_charges" class="form-control" value="{{ old('notebook_charges', $template->notebook_charges ?? 0) }}">
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-lg-6">
                    <label class="col-form-label">Book Charges</label>
                    <input type="number" name="book_charges" class="form-control" value="{{ old('book_charges', $template->book_charges ?? 0) }}">
                  </div>
                  <div class="col-lg-6">
                    <label class="col-form-label">Fine</label>
                    <input type="number" name="fine" class="form-control" value="{{ old('fine', $template->fine ?? 0) }}">
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-lg-12">
                    <label class="col-form-label">Note</label>
                    <input type="text" name="note" class="form-control" value="{{ old('note', $template->note ?? '') }}">
                  </div>
                </div>

                <div class="alert alert-warning small">
                  <strong>Heads up:</strong> Saving will recalculate <em>total_fee</em> and outstanding balance for every student
                  attached to this voucher. Already-collected payments are preserved; statuses (paid/pending/unpaid) are
                  recomputed from the new total.
                </div>

                <div class="row mb-3">
                  <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary">Update All Student Vouchers</button>
                    <a href="{{ route('fee_voucher') }}" class="btn btn-secondary">Cancel</a>
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
$(document).ready(function() {
    $('.js-months-multi').select2({
        placeholder: 'Select one or more months',
        width: '100%'
    });
});
</script>
@endsection
