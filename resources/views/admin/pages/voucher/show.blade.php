<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee Challan</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 13px;   /* reduced just a little */
            line-height: 1.4;
        }
        .challan {
            page-break-after: always;
            padding: 10px;
        }
        .challan:last-child {
            page-break-after: auto;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
        }
        .main-table td {
            width: 50%;
            vertical-align: top;
            border: 1px solid #000;
            padding: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
        }
        .header img {
            width: 50px;   
            height: 50px;
            margin-bottom: 5px;
        }
        .school-name {
            font-size: 18px;  /* reduced slightly */
            font-weight: bold;
            margin: 0;
        }
        .address {
            font-size: 12px;
            margin: 2px 0;
        }
        .info-table {
            width: 100%;
            margin-bottom: 8px;
            font-size: 13px;
        }
        .info-table td {
            padding: 4px;
        }
        .fee-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .fee-table th, .fee-table td {
            /* border: 1px solid #000; */
            padding: 6px;
        }
        .fee-table th {
            background: #f2f2f2;
            text-align: left;
            font-size: 14px;
        }
        .total {
            text-align: right;
            font-weight: bold;
            font-size: 15px;
            margin-top: 8px;
        }

        .note-total {
            text-align: left;
            font-weight: bold;
            font-size: 15px;
            margin-top: 8px;
        }
        .note {
            font-size: 14px;
            margin-top: 10px;
        }
        /* optional dashed cut line between school & student copy */
        .main-table td + td {
            /* border-left: 2px dashed #000; */
        }

        .fee-table td:first-child,
        .fee-table th:first-child {
            width: 70%;         /* Description column bigger */
            text-align: left;
        }

        .fee-table td:last-child,
        .fee-table th:last-child {
            width: 30%;         /* Amount column smaller */
            text-align: right;  /* Align values to right */
        }

    </style>
</head>
<body>
    <div class="challan">
        <table class="main-table">
            <tr>
                <!-- School Copy -->
                <td>
                    <div class="header">
                        <img src="{{ public_path('img/logo/school_logo.jpg') }}">
                        <p class="school-name">The Meezan School</p>
                        <p class="address">Chak No.149/9L</p>
                        <p class="address">Contact/WhatsApp: 03406581549</p>
                        <h3>Journal Voucher</h3>
                        <h3>School Copy</h3>
                    </div>
                    <table class="info-table">
                        <tr>
                            <td><strong>Student: </strong>{{ $voucher->student->student_name ?? '' }}</td>
                            <td><strong>Voucher Code: </strong>{{ $voucher->voucher_code }}</td>
                        </tr>
                        <tr>
                            <td><strong>Father Name: </strong>{{ $voucher->student->father_name ?? '' }}</td>
                            <td><strong>Class: </strong>{{ $voucher->student->classroom->class_name ?? '' }} {{ $voucher->student->classroom->section_name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Contact: </strong>{{ $voucher->student->contact_no ?? '' }}</td>
                            <td><strong>Due Date: </strong>{{ $voucher->expiry_date ?? '' }}</td>
                        </tr>
                    </table>
                    <table class="fee-table">
                        <tr><th>Description</th><th>Amount</th></tr>
                        @foreach($voucher->items as $item)
                        <tr>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ number_format($item->item_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </table>
                    <p class="total">Grand Total: {{ number_format($voucher->amount, 2) }}</p>
                    {{-- <p class="note-total">Terms & Conditions</p>
                    <p class="note">Fee must be paid before due date. Rs.50 charge if fee paid after Due Date.</p> --}}
                </td>

                <!-- Student Copy -->
                <td>
                    <div class="header">
                        <img src="{{ public_path('img/logo/school_logo.jpg') }}">
                        <p class="school-name">The Meezan School</p>
                        <p class="address">Chak No.149/9L</p>
                        <p class="address">Contact/WhatsApp: 03406581549</p>
                        <h3>Journal Voucher</h3>
                        <h3>Student Copy</h3>
                    </div>
                    <table class="info-table">
                        <tr>
                            <td><strong>Student: </strong>{{ $voucher->student->student_name ?? '' }}</td>
                            <td><strong>Voucher Code: </strong>{{ $voucher->voucher_code }}</td>
                        </tr>
                        <tr>
                            <td><strong>Father Name: </strong>{{ $voucher->student->father_name ?? '' }}</td>
                            <td><strong>Class: </strong>{{ $voucher->student->classroom->class_name ?? '' }} {{ $voucher->student->classroom->section_name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Contact: </strong>{{ $voucher->student->contact_no ?? '' }}</td>
                            <td><strong>Due Date: </strong>{{ $voucher->expiry_date ?? '' }}</td>
                        </tr>
                    </table>
                    <table class="fee-table">
                        <tr><th>Description</th><th>Amount</th></tr>
                        @foreach($voucher->items as $item)
                        <tr>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ number_format($item->item_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </table>
                    <p class="total">Grand Total: {{ number_format($voucher->amount, 2) }}</p>
                    {{-- <p class="note-total">Terms & Conditions</p>
                    <p class="note">Fee must be paid before due date. Rs.50 charge if fee paid after Due Date.</p> --}}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
