<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $student->student_name }} - {{ $exam->name }} Report Card</title>

<style>
    @page { size: A4; margin: 15mm; }
    body { margin: 0; font-family: "Times New Roman", serif; background: #fff; color: #000; }
    .page { width: 100%; }
    .school-name { text-align: center; font-weight: bold; font-size: 20px; }
    .report-title { text-align: center; font-weight: bold; font-size: 18px; text-decoration: underline; margin: 6px 0 20px; }
    .info-table { width: 100%; border-collapse: collapse; font-size: 16px; margin-bottom: 12px; }
    .info-table td { padding: 6px 4px; vertical-align: bottom; }
    .line { display: inline-block; border-bottom: 1px solid #000; min-width: 160px; }
    .line-small { min-width: 120px; border-bottom: 1px solid #000; display: inline-block; }
    .marks-table { width: 100%; border-collapse: collapse; font-size: 15px; margin-top: 8px; }
    .marks-table th, .marks-table td { border: 1px solid #000; padding: 6px; text-align: center; }
    .marks-table th { font-weight: bold; }
    .subject { text-align: left; padding-left: 10px; }
    .grading-title { margin-top: 12px; font-weight: bold; font-size: 15px; }
    .grading-table { width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 4px; }
    .grading-table th, .grading-table td { border: 1px solid #000; padding: 4px; text-align: center; }
    .summary { margin-top: 10px; font-size: 15px; }
    .summary-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
    .summary-table td { border: 1px solid #000; padding: 6px; text-align: center; }
    .footer-table { width: 100%; border-collapse: collapse; font-size: 15px; margin-top: 14px; }
    .footer-table td { padding: 6px 4px; vertical-align: bottom; }
    .signature-table { width: 100%; margin-top: 35px; text-align: center; font-size: 14px; }
    @media print { body { margin: 0; } }
</style>
</head>

<body>

<div class="page">
    <div class="header">
        <img src="{{ asset('img/logo/school_logo.ico') }}" alt="School Logo" style="width:60px; height:60px; display:block; margin: 0 auto 10px auto;">
    </div>
    <div class="school-name">The Meezan School System 149/9.L Sahiwal</div>

    <div class="report-title">{{ $exam->name }} Report Card</div>

    <!-- STUDENT INFO -->
    <table class="info-table">
        <tr>
            <td>Name: <span class="line">{{ $student->student_name }}</span></td>
            <td>Father’s Name: <span class="line">{{ $student->father_name }}</span></td>
        </tr>
        <tr>
            <td>Class: <span class="line-small">{{ $classRoom->class_name }}</span></td>
            <td>Section: <span class="line-small">{{ $classRoom->section_name ?? '-' }}</span></td>
            <td>Roll No: <span class="line-small">{{ $rollNo }}</span></td>
        </tr>
    </table>

    <!-- MARKS -->
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
            <td class="subject">{{ $result->subject->subject_name }}</td>
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

    <!-- SUMMARY -->
    <div class="summary">
        <table class="summary-table">
            <tr>
                <td><strong>Total Marks</strong><br>{{ $totalMax }}</td>
                <td><strong>Obtained Marks</strong><br>{{ $totalObt }}</td>
                <td><strong>Overall %</strong><br>{{ number_format($percentage, 2) }}%</td>
                <td><strong>Grade</strong><br>{{ $overallGrade }}</td>
                <td><strong>Remarks</strong><br>{{ $overallRemark }}</td>
            </tr>
        </table>
    </div>

    <!-- GRADING -->
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
            <td>Below 50% or passing marks</td><td>F</td><td>Fail</td>
        </tr>
    </table>

    <!-- FOOTER -->
    <table class="footer-table">
        <tr>
            <td>Neatness & Behavior: <span class="line"></span></td>
            <td>Grade: <span class="line-small">{{ $overallGrade }}</span></td>
        </tr>
        <tr>
            <td>Remarks: <span class="line">{{ $overallRemark }}</span></td>
            <td>Issue Date: <span class="line-small">{{ now()->format('d M Y') }}</span></td>
        </tr>
    </table>

    <table style="margin-top: 50px" class="signature-table">
        <tr>
            <td>Parent’s Sign</td>
            <td>Teacher’s Signature</td>
            <td>Principal Signature</td>
        </tr>
    </table>

</div>

</body>
</html>
