<form action="{{ route('finance.voucher.store') }}" method="POST">
    @csrf
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select" required>
                <option value="income">Income</option>
                <option value="expense">Expense</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-select" required>
                @foreach($categories as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Amount</label>
            <input type="number" name="amount" class="form-control" min="0.01" step="0.01" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Voucher Date</label>
            <input type="date" name="voucher_date" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Payment Mode</label>
            <select name="payment_mode" class="form-select" required>
                <option value="cash">Cash</option>
                <option value="bank">Bank</option>
                <option value="cheque">Cheque</option>
                <option value="online">Online</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Reference No</label>
            <input type="text" name="reference_no" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Description</label>
            <input type="text" name="description" class="form-control">
        </div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Add Voucher</button>
</form>
