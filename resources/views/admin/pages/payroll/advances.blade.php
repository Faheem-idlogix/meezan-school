@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
  <div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1><i class="bi bi-cash me-2 text-warning"></i>Salary Advances</h1>
      <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('payroll.index') }}">Payroll</a></li><li class="breadcrumb-item active">Advances</li></ol></nav>
    </div>
    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#addAdvanceModal">
      <i class="bi bi-plus-lg me-1"></i>Add Advance
    </button>
  </div>

  @if(session('success'))<div class="alert alert-success alert-dismissible border-0 mb-3"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>{{ session('success') }}</div>@endif

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead><tr><th>#</th><th>Teacher</th><th>Amount</th><th>Date</th><th>Reason</th><th>Status</th><th>Deducted In</th></tr></thead>
          <tbody>
            @forelse($advances as $i => $a)
            <tr>
              <td>{{ $i+1 }}</td>
              <td><strong>{{ $a->teacher?->teacher_name ?? '—' }}</strong></td>
              <td><strong>Rs. {{ number_format($a->amount,0) }}</strong></td>
              <td>{{ \Carbon\Carbon::parse($a->advance_date)->format('d M Y') }}</td>
              <td>{{ $a->reason ?? '—' }}</td>
              <td>
                @if($a->is_deducted)<span class="badge bg-success">Deducted</span>
                @else<span class="badge bg-warning text-dark">Pending</span>@endif
              </td>
              <td>{{ $a->is_deducted ? \Carbon\Carbon::create()->month($a->deduct_month)->format('M').' '.$a->deduct_year : '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-4 text-muted">No advance records found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  {{ $advances->links() }}
</main>

{{-- Add Advance Modal --}}
<div class="modal fade" id="addAdvanceModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('payroll.storeAdvance') }}" method="POST">@csrf
        <div class="modal-header"><h5 class="modal-title">Add Salary Advance</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Teacher</label>
            <select name="teacher_id" class="form-select select2" required>
              <option value="">Select Teacher...</option>
              @foreach($teachers as $t)<option value="{{ $t->id }}">{{ $t->teacher_name }}</option>@endforeach
            </select>
          </div>
          <div class="row g-2">
            <div class="col-6"><label class="form-label small">Amount (Rs.)</label><input type="number" name="amount" class="form-control" min="1" required></div>
            <div class="col-6"><label class="form-label small">Date</label><input type="date" name="advance_date" class="form-control" value="{{ today()->toDateString() }}" required></div>
          </div>
          <div class="mt-2"><label class="form-label small">Reason</label><input type="text" name="reason" class="form-control" placeholder="Medical, emergency, etc."></div>
          <div class="mt-2"><label class="form-label small">Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-warning btn-sm">Save Advance</button></div>
      </form>
    </div>
  </div>
</div>
@endsection
