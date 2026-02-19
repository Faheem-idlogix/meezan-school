<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class TenantMiddleware
{
    /**
     * Detect the current school/tenant and bind it to the app container.
     * Detection order:
     *   1. Subdomain (school1.yourapp.com)
     *   2. Session (for local/single-domain setups)
     *   3. Default school_id = 1
     */
    public function handle(Request $request, Closure $next)
    {
        $school = null;

        // 1. Subdomain detection
        $host      = $request->getHost();
        $parts     = explode('.', $host);
        $subdomain = count($parts) >= 3 ? $parts[0] : null;

        if ($subdomain && !in_array($subdomain, ['www', 'app', 'admin'])) {
            $school = School::where('subdomain', $subdomain)
                            ->where('status', '!=', 'suspended')
                            ->first();
        }

        // 2. Session fallback
        if (!$school && Session::has('school_id')) {
            $school = School::find(Session::get('school_id'));
        }

        // 3. Default to first active school
        if (!$school) {
            $school = School::where('status', '!=', 'suspended')->first()
                      ?? new School(['id' => 1, 'name' => 'Meezan School', 'status' => 'active']);
        }

        // Bind to container so any code can resolve it
        App::instance('current_school', $school);
        Config::set('tenant.school_id', $school->id ?? 1);

        // Share with all views
        view()->share('currentSchool', $school);

        return $next($request);
    }
}
