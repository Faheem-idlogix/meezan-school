<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;

class LogAuthEvents
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Log successful login
        if ($request->is('login') && $request->isMethod('post') && auth()->check()) {
            ActivityLog::log('login', 'User logged in: ' . auth()->user()->name);
        }

        return $response;
    }

    public function terminate(Request $request, $response): void
    {
        // Log logout
        if ($request->is('logout') && $request->isMethod('post')) {
            // At this point auth may already be cleared, but we logged via observer
        }
    }
}
