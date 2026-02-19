@extends('admin.layout.master')
@section('content')
<main id="main" class="main">
    <div class="pagetitle d-flex align-items-center justify-content-between">
        <div>
            <h1>Finance Hub</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Finance</li>
                </ol>
            </nav>
        </div>
        <div class="text-muted small"><i class="bi bi-calendar3 me-1"></i>{{ date('l, d M Y') }}</div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs mb-3" id="financeTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link @if($tab=='ledger') active @endif" id="ledger-tab" data-bs-toggle="tab" data-bs-target="#ledger" type="button" role="tab">Ledger</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link @if($tab=='fee') active @endif" id="fee-tab" data-bs-toggle="tab" data-bs-target="#fee" type="button" role="tab">Fee Report</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link @if($tab=='add') active @endif" id="add-tab" data-bs-toggle="tab" data-bs-target="#add" type="button" role="tab">Add Voucher/Expense</button>
                    </li>
                </ul>
                <div class="tab-content" id="financeTabsContent">
                    <div class="tab-pane fade @if($tab=='ledger') show active @endif" id="ledger" role="tabpanel">
                        @include('admin.pages.finance.ledger_widget')
                    </div>
                    <div class="tab-pane fade @if($tab=='fee') show active @endif" id="fee" role="tabpanel">
                        @include('admin.pages.finance.fee_report')
                    </div>
                    <div class="tab-pane fade @if($tab=='add') show active @endif" id="add" role="tabpanel">
                        @include('admin.pages.finance.add_voucher')
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
