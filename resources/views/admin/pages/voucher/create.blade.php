@extends('admin.layout.master')
@section('content')
<main id="main" class="main">

        <div class="pagetitle">
            <h1>{{ isset($voucher) ? 'Edit Voucher' : 'Create Voucher' }}</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">{{ isset($voucher) ? 'Edit Voucher' : 'Create Voucher' }}</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
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
                <h5 class="card-title">{{ isset($voucher) ? 'Edit Voucher' : 'Create Voucher' }}</h5>

                <!-- Voucher Form -->
                <form action="{{ isset($voucher) ? route('voucher.update', $voucher->id) : route('voucher.store') }}" method="POST">
                    @csrf
                    @if(isset($voucher))
                        @method('PUT')
                    @endif

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="voucher_code" class="form-label">Voucher Code</label>
                            <input type="text" id="voucher_code" class="form-control" value="{{ $voucher->voucher_code ?? $nextVoucherCode }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="student_id" class="form-label">Student</label>
                            <select class="form-select" id="student_id" name="student_id" required>
                                <option value="" disabled {{ !isset($voucher) ? 'selected' : '' }}>Select Student</option>
                                @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ (isset($voucher) && $voucher->student_id == $student->id) ? 'selected' : '' }}>
                                    {{ $student->student_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="expiry_date" class="form-label">Expiry Date</label>
                            <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="{{ $voucher->expiry_date ?? '' }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Voucher Items</label>
                        <div id="itemsWrapper" class="d-flex flex-column gap-2">
                            @php
                                $existingItems = isset($voucher) ? $voucher->items : collect();
                                $items = $existingItems->count() ? $existingItems : collect([['item_name' => '', 'item_price' => '']]);
                            @endphp
                            @foreach($items as $idx => $item)
                            <div class="row g-2 item-row align-items-center">
                                <div class="col-md-6">
                                    <input type="text" name="items[{{ $loop->index }}][item_name]" class="form-control" placeholder="Item name" value="{{ $item['item_name'] ?? '' }}" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" step="0.01" min="0" name="items[{{ $loop->index }}][item_price]" class="form-control item-price" placeholder="Price" value="{{ $item['item_price'] ?? '' }}" required>
                                </div>
                                <div class="col-md-2 d-flex gap-1">
                                    <button type="button" class="btn btn-danger w-100 remove-row">-</button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-success" id="addItemBtn">+ Add Item</button>
                            <small class="text-muted ms-2">Use add to insert new items and - to remove a row.</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Total Amount</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{{ $voucher->amount ?? '0.00' }}" readonly>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">{{ isset($voucher) ? 'Update Voucher' : 'Create Voucher' }}</button>
                </form>
    
                </div>
            </div>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const wrapper = document.getElementById('itemsWrapper');
    const addBtn = document.getElementById('addItemBtn');
    const amountInput = document.getElementById('amount');

    const recalc = () => {
        let total = 0;
        wrapper.querySelectorAll('.item-price').forEach(inp => {
            const val = parseFloat(inp.value);
            if (!isNaN(val)) total += val;
        });
        amountInput.value = total.toFixed(2);
    };

    const addRow = (name = '', price = '') => {
        const idx = Date.now();
        const row = document.createElement('div');
        row.className = 'row g-2 item-row align-items-center';
        row.innerHTML = `
            <div class="col-md-6">
                <input type="text" name="items[${idx}][item_name]" class="form-control" placeholder="Item name" value="${name}" required>
            </div>
            <div class="col-md-4">
                <input type="number" step="0.01" min="0" name="items[${idx}][item_price]" class="form-control item-price" placeholder="Price" value="${price}" required>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="button" class="btn btn-danger w-100 remove-row">-</button>
            </div>
        `;
        wrapper.appendChild(row);
    };

    addBtn.addEventListener('click', () => {
        addRow();
    });

    wrapper.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-row')) {
            const rows = wrapper.querySelectorAll('.item-row');
            if (rows.length > 1) {
                e.target.closest('.item-row').remove();
                recalc();
            }
        }
    });

    wrapper.addEventListener('input', (e) => {
        if (e.target.classList.contains('item-price')) {
            recalc();
        }
    });

    // Initial calculation
    recalc();
});
</script>
@endsection