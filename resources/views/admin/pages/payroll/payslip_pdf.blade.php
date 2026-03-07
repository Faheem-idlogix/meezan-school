<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; margin: 20px; }
    .header { text-align: center; border-bottom: 2px solid #4154f1; padding-bottom: 10px; margin-bottom: 15px; }
    .header h2 { color: #4154f1; margin: 0; font-size: 16px; }
    .header p  { color: #666; margin: 3px 0 0; font-size: 10px; }
    .info-table { width: 100%; margin-bottom: 12px; }
    .info-table td { padding: 3px 6px; }
    .info-table .label { color: #777; width: 35%; }
    .salary-row { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    .salary-row th { background: #f0f4ff; color: #012970; padding: 5px 8px; border: 1px solid #ddd; font-size: 10px; }
    .salary-row td { padding: 4px 8px; border: 1px solid #eee; }
    .earnings-header { background: #e6f9ee !important; color: #1a7a45 !important; }
    .deductions-header { background: #ffeaea !important; color: #c0392b !important; }
    .net-box { background: #4154f1; color: #fff; text-align: center; padding: 12px; border-radius: 6px; margin: 10px 0; }
    .net-box .label { color: rgba(255,255,255,.8); font-size: 9px; }
    .net-box .amount { font-size: 20px; font-weight: bold; }
    .footer { border-top: 1px solid #ddd; padding-top: 8px; margin-top: 15px; text-align: center; font-size: 9px; color: #999; }
    .badge-paid   { background: #27ae60; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 9px; }
    .badge-draft  { background: #95a5a6; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 9px; }
  </style>
</head>
<body>
  <div class="header">
    <h2>{{ setting('school_name', 'School') }}</h2>
    <p>SALARY SLIP &mdash; {{ $payroll->month_name }} {{ $payroll->year }}
       &nbsp;&nbsp;
       <span class="{{ $payroll->status === 'paid' ? 'badge-paid' : 'badge-draft' }}">{{ strtoupper($payroll->status) }}</span>
    </p>
  </div>

  <table class="info-table">
    <tr>
      <td class="label">Employee Name</td><td><strong>{{ $payroll->teacher?->teacher_name }}</strong></td>
      <td class="label">Employee ID</td><td>{{ $payroll->teacher?->employee_id ?? '—' }}</td>
    </tr>
    <tr>
      <td class="label">Designation</td><td>{{ $payroll->teacher?->specialization ?? 'Teacher' }}</td>
      <td class="label">Pay Period</td><td>{{ $payroll->month_name }} {{ $payroll->year }}</td>
    </tr>
    <tr>
      <td class="label">Working Days</td><td>{{ $payroll->working_days }}</td>
      <td class="label">Present Days</td><td>{{ $payroll->present_days }} (Absent: {{ $payroll->absent_days }})</td>
    </tr>
  </table>

  <table class="salary-row">
    <tr>
      <th class="earnings-header" style="width:50%">EARNINGS</th>
      <th class="earnings-header" style="width:15%; text-align:right">Amount</th>
      <th class="deductions-header" style="width:35%">DEDUCTIONS</th>
      <th class="deductions-header" style="width:15%; text-align:right">Amount</th>
    </tr>
    <tr><td>Basic Salary</td><td style="text-align:right">{{ number_format($payroll->basic_salary,0) }}</td>
        <td>Advance Recovery</td><td style="text-align:right">{{ number_format($payroll->advance_deduction,0) }}</td></tr>
    <tr><td>House Rent Allowance</td><td style="text-align:right">{{ number_format($payroll->house_rent_allowance,0) }}</td>
        <td>Absence Deduction</td><td style="text-align:right">{{ number_format($payroll->absence_deduction,0) }}</td></tr>
    <tr><td>Medical Allowance</td><td style="text-align:right">{{ number_format($payroll->medical_allowance,0) }}</td>
        <td>Income Tax</td><td style="text-align:right">{{ number_format($payroll->tax_deduction,0) }}</td></tr>
    <tr><td>Transport Allowance</td><td style="text-align:right">{{ number_format($payroll->transport_allowance,0) }}</td>
        <td>Other Deductions</td><td style="text-align:right">{{ number_format($payroll->other_deductions,0) }}</td></tr>
    <tr><td>Bonus</td><td style="text-align:right">{{ number_format($payroll->bonus,0) }}</td><td></td><td></td></tr>
    <tr><td>Other Allowances</td><td style="text-align:right">{{ number_format($payroll->other_allowances,0) }}</td><td></td><td></td></tr>
    <tr>
      <td><strong>TOTAL EARNINGS</strong></td>
      <td style="text-align:right;color:#1a7a45;font-weight:bold">{{ number_format($payroll->total_earnings,0) }}</td>
      <td><strong>TOTAL DEDUCTIONS</strong></td>
      <td style="text-align:right;color:#c0392b;font-weight:bold">{{ number_format($payroll->total_deductions,0) }}</td>
    </tr>
  </table>

  <div class="net-box">
    <div class="label">NET SALARY PAYABLE</div>
    <div class="amount">Rs. {{ number_format($payroll->net_salary,0) }}</div>
    @if($payroll->paid_date)<div style="font-size:9px;opacity:.8;margin-top:3px">Paid on {{ $payroll->paid_date->format('d M Y') }} &bull; {{ ucfirst($payroll->payment_method ?? 'Cash') }}</div>@endif
  </div>

  @if($payroll->notes)<p style="font-size:9px;color:#666"><strong>Notes:</strong> {{ $payroll->notes }}</p>@endif

  <div class="footer">
    <p>This is a computer-generated payslip. No signature required.</p>
    <p>Generated on {{ now()->format('d M Y H:i') }}</p>
  </div>
</body>
</html>
