<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $student->student_name }} - {{ $exam->name }}</title>
<style>
    @page { size: A4; margin: 10mm; }
    body {
        margin: 0;
        font-family: "Times New Roman", serif;
        color: #000;
        background: #fff;
        font-size: 13px;
        line-height: 1.2;
    }
    .page { width: 100%; }
    table, tr, td, th, div { page-break-inside: avoid; }
    .header { text-align: center; margin-bottom: 5px; }
    .school-name { text-align: center; font-weight: 700; font-size: 19px; }
    .school-address { text-align: center; font-size: 11px; margin-top: 2px; }
    .report-title {
        text-align: center;
        font-size: 17px;
        font-weight: 700;
        text-decoration: underline;
        margin: 6px 0 14px;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        margin-bottom: 9px;
    }
    .info-table td { padding: 5px 4px; vertical-align: bottom; }
    .line {
        display: inline-block;
        border-bottom: 1px solid #000;
        min-width: 150px;
    }
    .line-small { min-width: 105px; }

    .marks-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        margin-top: 6px;
    }
    .marks-table th,
    .marks-table td {
        border: 1px solid #000;
        padding: 5px;
        text-align: center;
    }
    .marks-table th { font-weight: 700; }
    .subject { text-align: left !important; padding-left: 8px !important; }

    .summary-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
        font-size: 13px;
    }
    .summary-table td {
        border: 1px solid #000;
        text-align: center;
        padding: 6px 4px;
    }

    .grading-title {
        margin-top: 9px;
        font-size: 14px;
        font-weight: 700;
    }
    .grading-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        margin-top: 3px;
    }
    .grading-table th,
    .grading-table td {
        border: 1px solid #000;
        padding: 4px;
        text-align: center;
    }

    .attendance {
        margin-top: 8px;
        font-size: 13px;
        font-weight: 700;
    }
    .attendance-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 2px;
        font-size: 12px;
    }
    .attendance-table td {
        border: 1px solid #000;
        text-align: center;
        height: 26px;
    }

    .footer-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        margin-top: 8px;
    }
    .footer-table td { padding: 5px 4px; vertical-align: bottom; }

    .signature-table {
        width: 100%;
        margin-top: 22px;
        text-align: center;
        font-size: 13px;
    }

    .short-card .signature-table { margin-top: 34px; }

    .compact-card .report-title { font-size: 16px; margin: 5px 0 12px; }
    .compact-card .info-table { font-size: 13px; margin-bottom: 8px; }
    .compact-card .info-table td { padding: 4px 3px; }
    .compact-card .marks-table { font-size: 12px; }
    .compact-card .marks-table th,
    .compact-card .marks-table td { padding: 4px; }
    .compact-card .summary-table { font-size: 12px; margin-top: 7px; }
    .compact-card .summary-table td { padding: 5px 4px; }
    .compact-card .grading-table { font-size: 11px; }
    .compact-card .grading-table th,
    .compact-card .grading-table td { padding: 3px; }
    .compact-card .attendance-table { font-size: 11px; }
    .compact-card .attendance-table td { height: 24px; }
    .compact-card .footer-table { font-size: 12px; margin-top: 7px; }
    .compact-card .footer-table td { padding: 4px 3px; }
    .compact-card .signature-table { font-size: 12px; margin-top: 14px; }

    .compact-card .line { min-width: 140px; }
    .compact-card .line-small { min-width: 95px; }
</style>
</head>
<body>
@php
    $subjectCount = $results->count();
    $isShortCard = $subjectCount <= 8;
    $isCompactCard = $subjectCount >= 12;
@endphp
<div class="page{{ $isShortCard ? ' short-card' : '' }}{{ $isCompactCard ? ' compact-card' : '' }}">

    {{-- Header with Logo + School Name --}}
    <div class="header">
        @php
            $logoFull = school_logo(true);
        @endphp
        @if(file_exists($logoFull))
        <img src="{{ $logoFull }}" alt="Logo" style="width:60px; height:60px; display:block; margin: 0 auto 6px auto;">
        @endif
    </div>
    <div class="school-name">{{ setting('school_name', 'School') }}</div>
    <div class="school-address">{{ setting('school_address', '') }}</div>

    <div class="report-title">{{ $exam->name }} Report Card</div>

    {{-- Student Info --}}
    <table class="info-table">
        <tr>
            <td>Name: <span class="line">{{ $student->student_name }}</span></td>
            <td>Father's Name: <span class="line">{{ $student->father_name }}</span></td>
        </tr>
        <tr>
            <td>Class: <span class="line-small">{{ $classRoom->class_name ?? '-' }}</span></td>
            <td>Section: <span class="line-small">{{ $classRoom->section_name ?? '-' }}</span></td>
            <td>Roll No: <span class="line-small">{{ $rollNo }}</span></td>
        </tr>
    </table>

    {{-- Marks Table --}}
    <table class="marks-table">
        <tr>
            <th>Sr#</th>
            <th>Subject</th>
            <th>Total Marks</th>
            <th>Obtained Marks</th>
            <th>%</th>
            <th>Grade</th>
        </tr>
        @foreach ($results as $index => $result)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td class="subject">{{ $result->subject->subject_name ?? '-' }}</td>
            <td>{{ $result->total_marks }}</td>
            <td>{{ $result->obtained_marks }}</td>
            <td>{{ number_format($result->percentage, 2) }}%</td>
            <td>{{ $result->grade }}</td>
        </tr>
        @endforeach
        <tr>
            <th colspan="2">Total</th>
            <th>{{ $totalMax }}</th>
            <th>{{ $totalObt }}</th>
            <th>{{ number_format($percentage, 2) }}%</th>
            <th>{{ $overallGrade }}</th>
        </tr>
    </table>

    {{-- Summary --}}
    <table class="summary-table">
        <tr>
            <td><strong>Total Marks</strong><br>{{ $totalMax }}</td>
            <td><strong>Obtained Marks</strong><br>{{ $totalObt }}</td>
            <td><strong>Overall %</strong><br>{{ number_format($percentage, 2) }}%</td>
            <td><strong>Grade</strong><br>{{ $overallGrade }}</td>
            <td><strong>Remarks</strong><br>{{ $overallRemark }}</td>
        </tr>
    </table>

    {{-- Grading Formula --}}
    <div class="grading-title">Grading Formula</div>
    <table class="grading-table">
        <tr>
            <th>Percentage of Marks</th><th>Grade</th><th>Remarks</th>
            <th>Percentage of Marks</th><th>Grade</th><th>Remarks</th>
        </tr>
        <tr>
            <td>90% and above</td><td>A+</td><td>Excellent</td>
            <td>80% and above but below 90%</td><td>A</td><td>Very Good</td>
        </tr>
        <tr>
            <td>70% and above but below 80%</td><td>B</td><td>Good</td>
            <td>60% and above but below 70%</td><td>C</td><td>Average</td>
        </tr>
        <tr>
            <td>50% and above but below 60%</td><td>D</td><td>Average</td>
            <td>Below 50%</td><td>F</td><td>Fail</td>
        </tr>
    </table>

    {{-- Attendance --}}
    <div class="attendance">Attendance Record</div>
    <table class="attendance-table">
        <tr>
            <td>Total Working Days</td>
            <td>Present Days</td>
            <td>Absent Days</td>
            <td>Attendance %</td>
        </tr>
    </table>

    {{-- Footer --}}
    <table class="footer-table">
        <tr>
            <td>Neatness &amp; Behavior: <span class="line"></span></td>
            <td>Grade: <span class="line-small">{{ $overallGrade }}</span></td>
        </tr>
        <tr>
            <td>Remarks: <span class="line">{{ $overallRemark }}</span></td>
            <td>Class Position: <span class="line-small"></span></td>
        </tr>
        <tr>
            <td>Issue Date: <span class="line-small">{{ now()->format('d M Y') }}</span></td>
        </tr>
    </table>

    {{-- Signatures --}}
    <table class="signature-table">
        <tr>
            <td>Parent's Sign</td>
            <td>Teacher's Signature</td>
            <td>Principal Signature</td>
        </tr>
    </table>

</div>
</body>
</html>