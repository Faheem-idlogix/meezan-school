<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Annual Exam Report Card</title>

<style>
    /* PAGE SETUP */
    @page {
        size: A4;
        margin: 15mm;
    }

    body {
        margin: 0;
        font-family: "Times New Roman", serif;
        background: #fff;
        color: #000;
    }

    .page {
        width: 100%;
    }

    /* HEADER */
    .school-name {
        text-align: center;
        font-weight: bold;
        font-size: 20px;
    }

    .report-title {
        text-align: center;
        font-weight: bold;
        font-size: 18px;
        text-decoration: underline;
        margin: 6px 0 20px;
    }

    /* INFO LINES */
    .info-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 16px;
        margin-bottom: 12px;
    }

    .info-table td {
        padding: 6px 4px;
        vertical-align: bottom;
    }

    .line {
        display: inline-block;
        border-bottom: 1px solid #000;
        width: 180px;
        height: 14px;
    }

    .line-small {
        width: 120px;
    }

    /* MARKS TABLE */
    .marks-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 15px;
        margin-top: 8px;
    }

    .marks-table th,
    .marks-table td {
        border: 1px solid #000;
        padding: 6px;
        text-align: center;
    }

    .marks-table th {
        font-weight: bold;
    }

    .subject {
        text-align: left;
        padding-left: 10px;
    }

    /* GRADING TABLE */
    .grading-title {
        margin-top: 12px;
        font-weight: bold;
        font-size: 15px;
    }

    .grading-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        margin-top: 4px;
    }

    .grading-table th,
    .grading-table td {
        border: 1px solid #000;
        padding: 4px;
        text-align: center;
    }

    /* ATTENDANCE */
    .attendance {
        margin-top: 12px;
        font-size: 15px;
    }

    .attendance-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 4px;
    }

    .attendance-table td {
        border: 1px solid #000;
        height: 28px;
        text-align: center;
    }

    /* FOOTER */
    .footer-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 15px;
        margin-top: 14px;
    }

    .footer-table td {
        padding: 6px 4px;
        vertical-align: bottom;
    }

    .signature-table {
        width: 100%;
        margin-top: 35px;
        text-align: center;
        font-size: 14px;
    }

    /* PRINT CLEAN */
    @media print {
        body {
            margin: 0;
        }
    }
</style>
</head>

<body>

<div class="page">
    <div class="header">
        <img src="img/logo/school_logo.ico" alt="School Logo" style="width:60px; height:60px; display:block; margin: 0 auto 10px auto;">
    </div>
    <div class="school-name">
        The Meezan School System 149/9.L Sahiwal
    </div>

    <div class="report-title">
        Annual Exam Report Card 2024-2025
    </div>

    <!-- STUDENT INFO -->
    <table class="info-table">
        <tr>
            <td>Name: <span class="line"></span></td>
            <td>Father’s Name: <span class="line"></span></td>
        </tr>
        <tr>
            <td>Class: <span class="line-small"></span></td>
            <td>Section: <span class="line-small"></span></td>
            <td>Roll No: <span class="line-small"></span></td>
        </tr>
    </table>

    <!-- MARKS -->
    <table class="marks-table">
        <tr>
            <th>Sr#</th>
            <th>Subject</th>
            <th>Total Marks</th>
            <th>Obtained Marks</th>
        </tr>
        <tr><td>1</td><td class="subject">English</td><td></td><td></td></tr>
        <tr><td>2</td><td class="subject">Urdu</td><td></td><td></td></tr>
        <tr><td>3</td><td class="subject">Mathematics</td><td></td><td></td></tr>
        <tr><td>4</td><td class="subject">Islamic Studies</td><td></td><td></td></tr>
        <tr><td>5</td><td class="subject">Social Study</td><td></td><td></td></tr>
        <tr><td>6</td><td class="subject">Science</td><td></td><td></td></tr>
        <tr><td>7</td><td class="subject">Drawing</td><td></td><td></td></tr>
        <tr><td>8</td><td class="subject">Computer</td><td></td><td></td></tr>
        <tr><td>9</td><td class="subject">Holy Quran</td><td></td><td></td></tr>
        <tr><td>10</td><td class="subject">General Knowledge</td><td></td><td></td></tr>
        <tr><td>11</td><td class="subject"></td><td></td><td></td></tr>
        <tr>
            <th colspan="2">Total</th>
            <th></th>
            <th></th>
        </tr>
    </table>

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

    <!-- ATTENDANCE -->
    <div class="attendance">
        Attendance Record:
        <table class="attendance-table">
            <tr>
                <td>Total Working Days</td>
                <td>Present Day</td>
                <td>Absent Days</td>
                <td>%</td>
            </tr>
        </table>
    </div>

    <!-- FOOTER -->
    <table class="footer-table">
        <tr>
            <td>Neatness & Behavior: <span class="line"></span></td>
            <td>Grade: <span class="line-small"></span></td>
        </tr>
        <tr>
            <td>Remarks: <span class="line"></span></td>
            <td>Class Position: <span class="line-small"></span></td>
        </tr>
        <tr>
            <td>Issue Date: <span class="line-small"></span></td>
        </tr>
    </table>

    <table class="signature-table">
        <tr>
            <td>Parent’s Sign</td>
            <td>Teacher’s Signature</td>
            <td>Principal Signature</td>
        </tr>
    </table>

</div>

</body>
</html>
