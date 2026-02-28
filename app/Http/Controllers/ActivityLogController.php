<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::latest();

        // Filters
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('user')) {
            $query->where('user_name', 'like', "%{$request->user}%");
        }
        if ($request->filled('model')) {
            $query->where('model_type', 'like', "%{$request->model}%");
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);

        $actions = ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');
        $models  = ActivityLog::select('model_type')->whereNotNull('model_type')
                    ->distinct()->orderBy('model_type')
                    ->pluck('model_type')
                    ->map(fn($m) => class_basename($m));

        return view('admin.pages.activity_logs.index', compact('logs', 'actions', 'models'));
    }

    public function show(ActivityLog $activityLog)
    {
        return view('admin.pages.activity_logs.show', compact('activityLog'));
    }

    public function destroy(Request $request)
    {
        $days = $request->input('days', 90);
        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        return redirect()->route('activity_logs.index')
            ->with('success', "Cleared {$deleted} log entries older than {$days} days.");
    }
}
