<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee Challan — Advanced</title>
    <style>
        html, body {
            margin: 0; padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px; line-height: 1.5; color: #333;
        }
        .challan {
            page-break-after: always;
            padding: 10px;
        }
        .challan:last-child { page-break-after: auto; }
        .main-table { width: 100%; border-collapse: collapse; }
        .main-table > tbody > tr > td { width: 50%; vertical-align: top; border: 1px solid #999; padding: 14px; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 2px solid #333; padding-bottom: 8px; }
        .header img { width: 48px; height: 48px; margin-bottom: 4px; }
        .school-name { font-size: 17px; font-weight: bold; margin: 0; color: #1a237e; }
        .address { font-size: 11px; margin: 2px 0; color: #555; }
        .copy-label { display: inline-block; background: #1a237e; color: #fff; font-size: 10px; font-weight: 700; padding: 2px 10px; border-radius: 3px; margin-top: 6px; }
        .voucher-title { text-align: center; font-size: 14px; font-weight: 700; margin: 8px 0 6px; color: #1a237e; border-bottom: 1px solid #ddd; padding-bottom: 4px; }
        .info-table { width: 100%; margin-bottom: 6px; font-size: 11.5px; }
        .info-table td { padding: 3px 4px; }
        .info-table .lbl { font-weight: 600; color: #555; width: 28%; }
        .fee-table { width: 100%; border-collapse: collapse; font-size: 11.5px; margin-bottom: 6px; }
        .fee-table th { background: #e8eaf6; padding: 5px 6px; text-align: left; font-size: 11px; font-weight: 700; border-bottom: 2px solid #1a237e; color: #1a237e; }
        .fee-table td { padding: 4px 6px; border-bottom: 1px solid #eee; }
        .fee-table td:last-child, .fee-table th:last-child { text-align: right; width: 30%; }
        .fee-table td:first-child, .fee-table th:first-child { width: 70%; }
        .fee-table tr.total-row td { font-weight: 800; font-size: 13px; border-top: 2px solid #1a237e; background: #f5f5f5; }
        .status-badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .status-paid { background: #c8e6c9; color: #2e7d32; }
        .status-unpaid { background: #ffcdd2; color: #c62828; }
        .status-pending { background: #fff9c4; color: #f57f17; }
        .payment-info { background: #f5f5f5; border-radius: 4px; padding: 6px 8px; margin-top: 6px; font-size: 11px; }
        .payment-info .row-item { display: flex; justify-content: space-between; padding: 2px 0; border-bottom: 1px dotted #ddd; }
        .payment-info .row-item:last-child { border-bottom: none; }
        .terms { font-size: 10px; color: #777; margin-top: 8px; padding-top: 4px; border-top: 1px dashed #ccc; }
        .terms strong { color: #333; }
        .qr-area { text-align: center; margin-top: 6px; }
    </style>
</head>
<body>
    @foreach ($data as $item)
    @php
        $status = strtolower($item->status ?? 'pending');
        $statusClass = $status === 'paid' ? 'status-paid' : ($status === 'unpaid' ? 'status-unpaid' : 'status-pending');
        $balance = ($item->total_fee ?? 0) - ($item->received_payment_fee ?? 0);
    @endphp
    <div class="challan">
        <table class="main-table">
            <tr>
                {{-- School Copy --}}
                <td>
                    <div class="header">
                        <img src="{{ school_logo(true) }}">
                        <p class="school-name">{{ setting('school_name', 'School') }}</p>
                        <p class="address">{{ setting('school_address', '') }}</p>
                        <p class="address">Contact: {{ setting('school_phone', '') }}</p>
                        <span class="copy-label">SCHOOL COPY</span>
                    </div>
                    <div class="voucher-title">Fee Voucher — {{ $item->fee_month }}</div>

                    <table class="info-table">
                        <tr><td class="lbl">Student</td><td>{{ $item->student->student_name ?? '' }}</td><td class="lbl">Voucher#</td><td>{{ $item->voucher_no }}</td></tr>
                        <tr><td class="lbl">Father</td><td>{{ $item->student->father_name ?? '' }}</td><td class="lbl">Class</td><td>{{ ($item->student->classroom->class_name ?? '') . ' ' . ($item->student->classroom->section_name ?? '') }}</td></tr>
                        <tr><td class="lbl">Contact</td><td>{{ $item->student->contact_no ?? '' }}</td><td class="lbl">Status</td><td><span class="status-badge {{ $statusClass }}">{{ ucfirst($status) }}</span></td></tr>
                        <tr><td class="lbl">Issue Date</td><td>{{ $item->issue_date ?? '' }}</td><td class="lbl">Due Date</td><td>{{ $item->submit_date ?? '' }}</td></tr>
                    </table>

                    <table class="fee-table">
                        <tr><th>Description</th><th>Amount (Rs.)</th></tr>
                        <tr><td>Monthly Fee</td><td>{{ number_format($item->academic_fee ?? 0) }}</td></tr>
                        @if(($item->exam_charges ?? 0) > 0)<tr><td>Exam / Annual Test Fund</td><td>{{ number_format($item->exam_charges) }}</td></tr>@endif
                        @if(($item->stationery_charges ?? 0) > 0)<tr><td>Stationery Charges</td><td>{{ number_format($item->stationery_charges) }}</td></tr>@endif
                        @if(($item->notebook_charges ?? 0) > 0)<tr><td>Notebook / Diary</td><td>{{ number_format($item->notebook_charges) }}</td></tr>@endif
                        @if(($item->book_charges ?? 0) > 0)<tr><td>Books</td><td>{{ number_format($item->book_charges) }}</td></tr>@endif
                        @if(($item->arrears ?? 0) > 0)<tr><td>Arrears</td><td>{{ number_format($item->arrears) }}</td></tr>@endif
                        @if(($item->fine ?? 0) > 0)<tr><td>Fine</td><td>{{ number_format($item->fine) }}</td></tr>@endif
                        <tr class="total-row"><td>Grand Total</td><td>Rs. {{ number_format($item->total_fee ?? 0) }}</td></tr>
                    </table>

                    @if(setting('show_fee_breakdown', '0') === '1')
                    <div class="payment-info">
                        <div class="row-item"><span>Total Fee:</span><span>Rs. {{ number_format($item->total_fee ?? 0) }}</span></div>
                        <div class="row-item"><span>Received:</span><span style="color:#2e7d32">Rs. {{ number_format($item->received_payment_fee ?? 0) }}</span></div>
                        <div class="row-item"><span><strong>Balance:</strong></span><span style="color:#c62828"><strong>Rs. {{ number_format($balance) }}</strong></span></div>
                    </div>
                    @endif

                    <div class="terms">
                        <strong>Terms:</strong> Fee must be paid before 10th of every month. Rs.50 late fee after due date.
                    </div>
                </td>

                {{-- Student Copy --}}
                <td>
                    <div class="header">
                        <img src="{{ school_logo(true) }}">
                        <p class="school-name">{{ setting('school_name', 'School') }}</p>
                        <p class="address">{{ setting('school_address', '') }}</p>
                        <p class="address">Contact: {{ setting('school_phone', '') }}</p>
                        <span class="copy-label" style="background:#2e7d32">STUDENT COPY</span>
                    </div>
                    <div class="voucher-title">Fee Voucher — {{ $item->fee_month }}</div>

                    <table class="info-table">
                        <tr><td class="lbl">Student</td><td>{{ $item->student->student_name ?? '' }}</td><td class="lbl">Voucher#</td><td>{{ $item->voucher_no }}</td></tr>
                        <tr><td class="lbl">Father</td><td>{{ $item->student->father_name ?? '' }}</td><td class="lbl">Class</td><td>{{ ($item->student->classroom->class_name ?? '') . ' ' . ($item->student->classroom->section_name ?? '') }}</td></tr>
                        <tr><td class="lbl">Contact</td><td>{{ $item->student->contact_no ?? '' }}</td><td class="lbl">Status</td><td><span class="status-badge {{ $statusClass }}">{{ ucfirst($status) }}</span></td></tr>
                        <tr><td class="lbl">Issue Date</td><td>{{ $item->issue_date ?? '' }}</td><td class="lbl">Due Date</td><td>{{ $item->submit_date ?? '' }}</td></tr>
                    </table>

                    <table class="fee-table">
                        <tr><th>Description</th><th>Amount (Rs.)</th></tr>
                        <tr><td>Monthly Fee</td><td>{{ number_format($item->academic_fee ?? 0) }}</td></tr>
                        @if(($item->exam_charges ?? 0) > 0)<tr><td>Exam / Annual Test Fund</td><td>{{ number_format($item->exam_charges) }}</td></tr>@endif
                        @if(($item->stationery_charges ?? 0) > 0)<tr><td>Stationery Charges</td><td>{{ number_format($item->stationery_charges) }}</td></tr>@endif
                        @if(($item->notebook_charges ?? 0) > 0)<tr><td>Notebook / Diary</td><td>{{ number_format($item->notebook_charges) }}</td></tr>@endif
                        @if(($item->book_charges ?? 0) > 0)<tr><td>Books</td><td>{{ number_format($item->book_charges) }}</td></tr>@endif
                        @if(($item->arrears ?? 0) > 0)<tr><td>Arrears</td><td>{{ number_format($item->arrears) }}</td></tr>@endif
                        @if(($item->fine ?? 0) > 0)<tr><td>Fine</td><td>{{ number_format($item->fine) }}</td></tr>@endif
                        <tr class="total-row"><td>Grand Total</td><td>Rs. {{ number_format($item->total_fee ?? 0) }}</td></tr>
                    </table>

                    @if(setting('show_fee_breakdown', '0') === '1')
                    <div class="payment-info">
                        <div class="row-item"><span>Total Fee:</span><span>Rs. {{ number_format($item->total_fee ?? 0) }}</span></div>
                        <div class="row-item"><span>Received:</span><span style="color:#2e7d32">Rs. {{ number_format($item->received_payment_fee ?? 0) }}</span></div>
                        <div class="row-item"><span><strong>Balance:</strong></span><span style="color:#c62828"><strong>Rs. {{ number_format($balance) }}</strong></span></div>
                    </div>
                    @endif

                    <div class="terms">
                        <strong>Terms:</strong> Fee must be paid before 10th of every month. Rs.50 late fee after due date.
                    </div>
                </td>
            </tr>
        </table>
    </div>
    @endforeach
</body>
</html>
