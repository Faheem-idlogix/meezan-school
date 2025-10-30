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
            width: 50px;   /* slightly smaller logo */
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
    @foreach ($data as $item)
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
                        <h3>Fee Voucher - {{ $item->fee_month }}<</h3>
                        <h3>School Copy </h3>
                    </div>
                    <table class="info-table">
                        <tr>
                            <td><strong>Student: </strong>{{ $item->student->student_name }}</td>
                            <td><strong>Date: </strong>{{ $item->issue_date }}</td>
                        </tr>
                        <tr>
                            <td><strong>Father Name: </strong>{{ $item->student->father_name }}</td>
                            <td><strong>Class: </strong>{{ $item->student->classroom->class_name.' '.$item->student->classroom->section_name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Invoice No: </strong>{{ $item->voucher_no }}</td>
                            <td><strong>Contact: </strong>{{ $item->student->contact_no ?? '' }}</td>
                        </tr>
                         <tr>
                            <td><strong>Issue Date: </strong>{{ $item->issue_date ?? ''}}</td>
                            <td><strong>Due Date: </strong>{{ $item->submit_date ?? '' }}</td>
                        </tr>
                    </table>
                    <table class="fee-table">
                        <tr><th>Description</th><th>Amount</th></tr>
                        <tr><td>Monthly Fee</td><td>{{ $item->academic_fee ?? 0 }}</td></tr>
                        <tr><td>Exam charges / Annual Test Fund</td><td>{{ $item->exam_charges ?? 0 }}</td></tr>
                        <tr><td>Stationery Charges</td><td>{{ $item->stationery_charges ?? 0 }}</td></tr>
                        <tr><td>Note Book / Diary</td><td>{{ $item->notebook_charges ?? 0 }}</td></tr>
                        <tr><td>Books</td><td>{{ $item->book_charges ?? 0 }}</td></tr>
                        <tr><td>Arrears</td><td>{{ $item->arrears ?? 0 }}</td></tr>
                        <tr><td>Fine</td><td>{{ $item->fine ?? 0 }}</td></tr>
                    </table>
                    <p class="total">Grand Total: {{ $item->total_fee }}</p>
                    <p class="note-total">Terms & Conditions</p>
                    <p class="note">Fee must be paid before 10th of every Month. Rs.50 charge if fee paid after Due Date.</p>
                </td>

                <!-- Student Copy -->
                <td>
                    <div class="header">
                        <img src="{{ public_path('img/logo/school_logo.jpg') }}">
                        <p class="school-name">The Meezan School</p>
                        <p class="address">Chak No.149/9L</p>
                        <p class="address">Contact/WhatsApp: 03406581549</p>
                       <h3>Fee Voucher - {{ $item->fee_month }}<</h3>
                        <h3>Student Copy</h3>
                    </div>
                     <table class="info-table">
                        <tr>
                            <td><strong>Student: </strong>{{ $item->student->student_name }}</td>
                            <td><strong>Date: </strong>{{ $item->issue_date }}</td>
                        </tr>
                        <tr>
                            <td><strong>Father Name: </strong>{{ $item->student->father_name }}</td>
                            <td><strong>Class: </strong>{{ $item->student->classroom->class_name.' '.$item->student->classroom->section_name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Invoice No: </strong>{{ $item->voucher_no }}</td>
                            <td><strong>Contact: </strong>{{ $item->student->contact_no ?? '' }}</td>
                        </tr>
                         <tr>
                            <td><strong>Issue Date: </strong>{{ $item->issue_date ?? ''}}</td>
                            <td><strong>Due Date: </strong>{{ $item->submit_date ?? '' }}</td>
                        </tr>
                    </table>
                    <table class="fee-table">
                        <tr><th>Description</th><th>Amount</th></tr>
                        <tr><td>Monthly Fee</td><td>{{ $item->academic_fee ?? 0 }}</td></tr>
                        <tr><td>Exam charges / Annual Test Fund</td><td>{{ $item->exam_charges  ?? 0}}</td></tr>
                        <tr><td>Stationery Charges</td><td>{{ $item->stationery_charges ?? 0 }}</td></tr>
                        <tr><td>Note Book / Diary</td><td>{{ $item->notebook_charges ?? 0 }}</td></tr>
                        <tr><td>Books</td><td>{{ $item->book_charges ?? 0 }}</td></tr>
                        <tr><td>Arrears</td><td>{{ $item->arrears ?? 0 }}</td></tr>
                        <tr><td>Fine</td><td>{{ $item->fine ?? 0 }}</td></tr>
                    </table>
                    <p class="total">Grand Total: {{ $item->total_fee }}</p>
                    <p class="note-total">Terms & Conditions</p>
                    <p class="note">Fee must be paid before 10th of every Month. Rs.50 charge if fee paid after Due Date.</p>
                </td>
            </tr>
        </table>
    </div>
    @endforeach
</body>
</html>
