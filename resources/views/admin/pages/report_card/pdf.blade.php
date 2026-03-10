<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $student->student_name }} - {{ $exam->name }}</title>
<style>
    @page { size: A4; margin: 15mm; }
    body { margin: 0; font-family: "DejaVu Sans", sans-serif; background: #fff; color: #000; font-size: 12px; }
    .page { width: 100%; }
    .header { text-align: center; margin-bottom: 5px; }
    .school-name { text-align: center; font-weight: bold; font-size: 20px; }
    .school-address { text-align: center; font-size: 11px; color: #555; margin-bottom: 4px; }
    .report-title { text-align: center; font-weight: bold; font-size: 18px; text-decoration: underline; margin: 6px 0 20px; }
    .info-table { width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 12px; }
    .info-table td { padding: 6px 4px; vertical-align: bottom; }
    .line { display: inline-block; border-bottom: 1px solid #000; min-width: 160px; }
    .line-small { min-width: 120px; border-bottom: 1px solid #000; display: inline-block; }
    .marks-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-top: 8px; }
    .marks-table th, .marks-table td { border: 1px solid #000; padding: 6px; text-align: center; }
    .marks-table th { font-weight: bold; }
    .subject { text-align: left !important; padding-left: 10px !important; }
    .grading-title { margin-top: 12px; font-weight: bold; font-size: 14px; }
    .grading-table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 4px; }
    .grading-table th, .grading-table td { border: 1px solid #000; padding: 4px; text-align: center; }
    .summary-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    .summary-table td { border: 1px solid #000; padding: 6px; text-align: center; font-size: 13px; }
    .footer-table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 20px; }
    .footer-table td { width: 25%; padding: 12px 10px; vertical-align: top; }
    .footer-label { display: block; font-size: 12px; margin-bottom: 6px; }
    .footer-line { display: block; width: 100%; height: 18px; border-bottom: 1px solid #000; line-height: 18px; font-size: 12px; }
    .signature-table { width: 100%; margin-top: 80px; text-align: center; font-size: 13px; }
</style>
</head>
<body>
<div class="page">

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

    <div class="report-title">{{ $exam->name }}</div>

    {{-- Student Info --}}
    <table class="info-table">
        <tr>
            <td>Name: <span class="line">{{ $student->student_name }}</span></td>
            <td>Father's Name: <span class="line">{{ $student->father_name }}</span></td>
        </tr>
        <tr>
            <td>Class: <span class="line-small">{{ $classRoom->class_name ?? '—' }}</span></td>
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
            <td class="subject">{{ $result->subject->subject_name ?? '—' }}</td>
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

    {{-- Footer --}}
    <table class="footer-table">
        <tr>
            <td><span class="footer-label">Class Position</span><span class="footer-line"></span></td>
            <td><span class="footer-label">Total Working Days</span><span class="footer-line"></span></td>
            <td><span class="footer-label">Present Days</span><span class="footer-line"></span></td>
            <td><span class="footer-label">Absent Days</span><span class="footer-line"></span></td>
        </tr>
        <tr>
            <td><span class="footer-label">Neatness &amp; Behavior</span><span class="footer-line"></span></td>
            <td><span class="footer-label">Grade</span><span class="footer-line">{{ $overallGrade }}</span></td>
            <td><span class="footer-label">Remarks</span><span class="footer-line">{{ $overallRemark }}</span></td>
            <td><span class="footer-label">Issue Date</span><span class="footer-line">{{ now()->format('d M Y') }}</span></td>
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