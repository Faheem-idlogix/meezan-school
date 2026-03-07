<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transfer Certificate - {{ $transferCertificate->tc_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 13px; color: #333; margin: 30px; }
        .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 22px; text-transform: uppercase; }
        .header h2 { margin: 5px 0; font-size: 16px; color: #555; }
        .tc-title { text-align: center; font-size: 18px; font-weight: bold; margin: 20px 0; text-decoration: underline; text-transform: uppercase; }
        .tc-number { text-align: right; font-weight: bold; margin-bottom: 15px; }
        .info-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .info-table td { padding: 8px 5px; border-bottom: 1px dotted #999; }
        .info-table td:first-child { font-weight: bold; width: 40%; }
        .footer { margin-top: 60px; display: flex; justify-content: space-between; }
        .signature { text-align: center; width: 200px; display: inline-block; }
        .signature-line { border-top: 1px solid #333; margin-top: 50px; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ setting('school_name', 'School') }}</h1>
        <h2>Transfer Certificate</h2>
    </div>

    <div class="tc-number">TC No: {{ $transferCertificate->tc_number }}</div>

    <div class="tc-title">Transfer Certificate</div>

    <table class="info-table">
        <tr><td>Student Name</td><td>{{ $transferCertificate->student->student_name ?? '—' }}</td></tr>
        <tr><td>Father's Name</td><td>{{ $transferCertificate->student->father_name ?? '—' }}</td></tr>
        <tr><td>Class</td><td>{{ $transferCertificate->student->classroom->class_name ?? '—' }}</td></tr>
        <tr><td>Date of Admission</td><td>{{ $transferCertificate->student->student_admission_date ?? '—' }}</td></tr>
        <tr><td>Date of Leaving</td><td>{{ $transferCertificate->leaving_date ? $transferCertificate->leaving_date->format('d M Y') : '—' }}</td></tr>
        <tr><td>Reason for Leaving</td><td>{{ $transferCertificate->reason ?? '—' }}</td></tr>
        <tr><td>Character & Conduct</td><td>{{ $transferCertificate->conduct ?? '—' }}</td></tr>
        <tr><td>Date of Issue</td><td>{{ $transferCertificate->issue_date->format('d M Y') }}</td></tr>
        <tr><td>Remarks</td><td>{{ $transferCertificate->remarks ?? '—' }}</td></tr>
    </table>

    <div style="margin-top: 80px;">
        <table width="100%">
            <tr>
                <td style="text-align: left; width: 50%;">
                    <div class="signature-line">Class Teacher</div>
                </td>
                <td style="text-align: right; width: 50%;">
                    <div class="signature-line">Principal</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
