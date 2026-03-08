<?php

namespace App\Http\Controllers;

use App\Models\SystemErrorLog;
use Illuminate\Http\Request;

class SystemErrorLogController extends Controller
{
    public function index(Request $request)
    {
        $query = SystemErrorLog::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhere('file', 'like', "%{$search}%");
            });
        }

        $errorLogs = $query->orderBy('id', 'desc')->paginate(50)->withQueryString();

        $totalErrors   = SystemErrorLog::where('severity', 'error')->count();
        $totalWarnings = SystemErrorLog::where('severity', 'warning')->count();
        $totalCritical = SystemErrorLog::where('severity', 'critical')->count();
        $todayCount    = SystemErrorLog::whereDate('created_at', today())->count();

        return view('admin.pages.error_logs.index', compact(
            'errorLogs', 'totalErrors', 'totalWarnings', 'totalCritical', 'todayCount'
        ));
    }

    public function show(SystemErrorLog $errorLog)
    {
        return response()->json($errorLog);
    }

    public function destroy(Request $request)
    {
        if ($request->filled('before_date')) {
            SystemErrorLog::whereDate('created_at', '<=', $request->before_date)->delete();
        } else {
            SystemErrorLog::truncate();
        }

        return redirect()->route('error-logs.index')->with('success', 'Error logs cleared successfully.');
    }
}
