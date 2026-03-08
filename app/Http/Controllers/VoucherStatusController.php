<?php

namespace App\Http\Controllers;

use App\Models\StudentFee;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class VoucherStatusController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentFee::with(['student', 'class_fee_voucher.classroom'])
            ->whereHas('student');

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Class filter
        if ($request->filled('class_room_id')) {
            $query->whereHas('class_fee_voucher', function ($q) use ($request) {
                $q->where('class_room_id', $request->class_room_id);
            });
        }

        // Month filter
        if ($request->filled('fee_month')) {
            $query->where('fee_month', $request->fee_month);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        // Search by voucher number or student name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('voucher_no', 'like', "%{$search}%")
                  ->orWhereHas('student', fn($qb) => $qb->where('student_name', 'like', "%{$search}%"));
            });
        }

        // Clone query for filtered summaries before pagination
        $filteredQuery = clone $query;

        $vouchers = $query->orderBy('student_fee_id', 'desc')->get();

        // Summary counts (overall)
        $totalAll     = StudentFee::whereHas('student')->count();
        $totalPaid    = StudentFee::whereHas('student')->where('status', 'paid')->count();
        $totalUnpaid  = StudentFee::whereHas('student')->where('status', 'unpaid')->count();
        $totalPending = StudentFee::whereHas('student')->where(function ($q) {
            $q->where('status', 'pending')->orWhereNull('status');
        })->count();

        // Filtered amounts
        $filteredTotal    = (clone $filteredQuery)->sum('total_fee');
        $filteredReceived = (clone $filteredQuery)->sum('received_payment_fee');
        $filteredBalance  = $filteredTotal - $filteredReceived;
        $filteredCount    = (clone $filteredQuery)->count();

        // Per-status filtered amounts
        $filteredPaidAmt   = (clone $filteredQuery)->where('status', 'paid')->sum('total_fee');
        $filteredUnpaidAmt = (clone $filteredQuery)->where('status', 'unpaid')->sum('total_fee');

        $classrooms = ClassRoom::orderBy('class_name')->get();
        $months = StudentFee::distinct()->pluck('fee_month')->filter()->sort()->values();

        return view('admin.pages.voucher_status.index', compact(
            'vouchers', 'classrooms', 'months',
            'totalAll', 'totalPaid', 'totalUnpaid', 'totalPending',
            'filteredTotal', 'filteredReceived', 'filteredBalance', 'filteredCount',
            'filteredPaidAmt', 'filteredUnpaidAmt'
        ));
    }

    public function export(Request $request)
    {
        $query = StudentFee::with(['student', 'class_fee_voucher.classroom'])
            ->whereHas('student');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('class_room_id')) {
            $query->whereHas('class_fee_voucher', fn($q) => $q->where('class_room_id', $request->class_room_id));
        }
        if ($request->filled('fee_month')) {
            $query->where('fee_month', $request->fee_month);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('voucher_no', 'like', "%{$search}%")
                  ->orWhereHas('student', fn($qb) => $qb->where('student_name', 'like', "%{$search}%"));
            });
        }

        $records = $query->orderBy('student_fee_id', 'desc')->get();

        $filename = 'voucher_status_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Voucher No', 'Student', 'Father', 'Class', 'Fee Month', 'Issue Date', 'Due Date', 'Total Fee', 'Received', 'Balance', 'Status']);

            foreach ($records as $idx => $v) {
                $class = ($v->class_fee_voucher && $v->class_fee_voucher->classroom)
                    ? $v->class_fee_voucher->classroom->class_name . ' - ' . $v->class_fee_voucher->classroom->section_name
                    : 'N/A';
                fputcsv($file, [
                    $idx + 1,
                    $v->voucher_no,
                    $v->student->student_name ?? 'N/A',
                    $v->student->father_name ?? '',
                    $class,
                    $v->fee_month,
                    $v->issue_date,
                    $v->submit_date,
                    $v->total_fee ?? 0,
                    $v->received_payment_fee ?? 0,
                    ($v->total_fee ?? 0) - ($v->received_payment_fee ?? 0),
                    $v->status ?? 'pending',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
