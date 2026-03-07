<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card - {{ $student->student_name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; margin: 20px; }
        .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 20px; text-transform: uppercase; color: #1a5276; }
        .header h2 { margin: 3px 0; font-size: 14px; color: #555; }
        .header p { margin: 2px 0; font-size: 11px; color: #777; }
        .header-note { text-align: center; font-style: italic; font-size: 11px; color: #666; margin-bottom: 10px; }
        .student-info { width: 100%; margin-bottom: 15px; }
        .student-info td { padding: 4px 8px; font-size: 12px; }
        .student-info td:first-child { font-weight: bold; width: 25%; background: #f8f9fa; }
        .marks-table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        .marks-table th, .marks-table td { border: 1px solid #ddd; padding: 6px 8px; text-align: center; font-size: 11px; }
        .marks-table th { background: #1a5276; color: white; font-size: 11px; }
        .marks-table tr:nth-child(even) { background: #f8f9fa; }
        .marks-table .total-row { font-weight: bold; background: #e8f4fd !important; }
        .marks-table .fail { color: #dc3545; font-weight: bold; }
        .marks-table .pass { color: #28a745; }
        .summary-box { display: inline-block; border: 1px solid #ddd; padding: 10px 15px; margin: 5px; text-align: center; background: #f8f9fa; }
        .summary-box .label { font-size: 10px; color: #777; text-transform: uppercase; }
        .summary-box .value { font-size: 16px; font-weight: bold; color: #1a5276; }
        .grade-table { width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 10px; }
        .grade-table th, .grade-table td { border: 1px solid #ddd; padding: 3px 6px; text-align: center; }
        .grade-table th { background: #f0f0f0; }
        .signatures { margin-top: 50px; width: 100%; }
        .signatures td { text-align: center; padding: 0 20px; vertical-align: bottom; }
        .signature-line { border-top: 1px solid #333; margin-top: 40px; padding-top: 5px; font-size: 11px; }
        .footer-note { text-align: center; font-size: 10px; color: #777; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 10px; }
        .result-status { font-size: 18px; font-weight: bold; text-align: center; padding: 8px; margin: 10px 0; }
        .result-pass { color: #28a745; border: 2px solid #28a745; }
        .result-fail { color: #dc3545; border: 2px solid #dc3545; }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>{{ $config->school_name ?? setting('school_name', 'School') }}</h1>
        <h2>Progress Report Card</h2>
        <p>{{ $config->school_address ?? '' }} {{ $config->school_phone ? '| Phone: ' . $config->school_phone : '' }}</p>
    </div>

    @if($config->header_note ?? false)
    <div class="header-note">{{ $config->header_note }}</div>
    @endif

    {{-- Student Info --}}
    <table class="student-info">
        <tr>
            <td>Student Name</td><td>{{ $student->student_name }}</td>
            <td>Father's Name</td><td>{{ $student->father_name }}</td>
        </tr>
        <tr>
            <td>Class</td><td>{{ $student->classroom->class_name ?? '—' }}</td>
            <td>Exam</td><td>{{ $exam->exam_name }}</td>
        </tr>
        <tr>
            <td>Roll No.</td><td>{{ $student->roll_number ?? $student->id }}</td>
            <td>Session</td><td>{{ $exam->session_year ?? date('Y') }}</td>
        </tr>
    </table>

    {{-- Marks Table --}}
    <table class="marks-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Subject</th>
                <th>Total Marks</th>
                <th>Obtained Marks</th>
                @if($config->show_percentage ?? true)<th>%</th>@endif
                @if($config->show_grade ?? true)<th>Grade</th>@endif
                @if($config->show_position ?? true)<th>Subject Pos.</th>@endif
                @if($config->show_remarks ?? true)<th>Remarks</th>@endif
            </tr>
        </thead>
        <tbody>
            @php $totalMarks = 0; $obtainedMarks = 0; $totalGP = 0; $subjectCount = 0; @endphp
            @foreach($results as $i => $result)
            @php
                $totalMarks += $result->total_marks ?? 0;
                $obtainedMarks += $result->obtain_marks ?? 0;
                if($result->grade_point) { $totalGP += $result->grade_point; $subjectCount++; }
                $passed = ($result->obtain_marks ?? 0) >= (($result->total_marks ?? 100) * 0.33);
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td style="text-align: left;">{{ $result->subject->subject_name ?? '—' }}</td>
                <td>{{ $result->total_marks ?? 100 }}</td>
                <td class="{{ $passed ? 'pass' : 'fail' }}">{{ $result->obtain_marks ?? 0 }}</td>
                @if($config->show_percentage ?? true)<td>{{ $result->percentage ? round($result->percentage, 1) . '%' : (($result->total_marks ?? 100) > 0 ? round(($result->obtain_marks / ($result->total_marks ?? 100)) * 100, 1) . '%' : '—') }}</td>@endif
                @if($config->show_grade ?? true)<td>{{ $result->grade ?? '—' }}</td>@endif
                @if($config->show_position ?? true)<td>{{ $result->subject_position ?? '—' }}</td>@endif
                @if($config->show_remarks ?? true)<td style="font-size: 10px;">{{ $result->teacher_remarks ?? ($passed ? 'Pass' : 'Fail') }}</td>@endif
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" style="text-align: right;">TOTAL</td>
                <td>{{ $totalMarks }}</td>
                <td>{{ $obtainedMarks }}</td>
                @if($config->show_percentage ?? true)<td>{{ $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 1) . '%' : '—' }}</td>@endif
                @if($config->show_grade ?? true)<td>—</td>@endif
                @if($config->show_position ?? true)<td>—</td>@endif
                @if($config->show_remarks ?? true)<td>—</td>@endif
            </tr>
        </tbody>
    </table>

    {{-- Summary --}}
    <div style="text-align: center; margin: 15px 0;">
        <div class="summary-box"><div class="label">Total Marks</div><div class="value">{{ $obtainedMarks }} / {{ $totalMarks }}</div></div>
        <div class="summary-box"><div class="label">Percentage</div><div class="value">{{ $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 1) : 0 }}%</div></div>
        @if(($config->show_gpa ?? true) && $subjectCount > 0)
        <div class="summary-box"><div class="label">GPA</div><div class="value">{{ round($totalGP / $subjectCount, 2) }}</div></div>
        @endif
        @if($config->show_position ?? true)
        <div class="summary-box"><div class="label">Class Position</div><div class="value">{{ $results->first()->class_position ?? '—' }}</div></div>
        @endif
    </div>

    {{-- Result Status --}}
    @php $overallPass = $totalMarks > 0 && ($obtainedMarks / $totalMarks) >= 0.33; @endphp
    <div class="result-status {{ $overallPass ? 'result-pass' : 'result-fail' }}">
        Result: {{ $overallPass ? 'PASS' : 'FAIL' }}
    </div>

    {{-- Grading Scale --}}
    @if(($config->show_grade ?? true) && isset($gradingSystem) && $gradingSystem)
    <h4 style="font-size: 12px; margin-top: 15px;">Grading Scale: {{ $gradingSystem->name }}</h4>
    <table class="grade-table">
        <thead><tr><th>Grade</th><th>Label</th><th>Range</th><th>GP</th></tr></thead>
        <tbody>
            @foreach($gradingSystem->gradeRules->sortByDesc('min_percentage') as $rule)
            <tr><td>{{ $rule->grade }}</td><td>{{ $rule->grade_label ?? '—' }}</td><td>{{ $rule->min_percentage }}% - {{ $rule->max_percentage }}%</td><td>{{ $rule->grade_point ?? '—' }}</td></tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Signatures --}}
    <table class="signatures">
        <tr>
            <td><div class="signature-line">{{ $config->class_teacher_signature ?? 'Class Teacher' }}</div></td>
            <td><div class="signature-line">Parent / Guardian</div></td>
            <td><div class="signature-line">{{ $config->principal_signature ?? 'Principal' }}</div></td>
        </tr>
    </table>

    @if($config->footer_note ?? false)
    <div class="footer-note">{{ $config->footer_note }}</div>
    @endif
</body>
</html>