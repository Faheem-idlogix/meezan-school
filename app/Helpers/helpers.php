<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Get a setting value by key. Cached for 5 minutes.
 */
if (!function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        static $cache = null;

        if ($cache === null) {
            try {
                $cache = Cache::remember('app_settings', 300, function () {
                    return Setting::pluck('value', 'key')->toArray();
                });
            } catch (\Exception $e) {
                return $default;
            }
        }

        return $cache[$key] ?? $default;
    }
}

/**
 * Get school logo URL (from settings or fallback).
 * @param bool $publicPath  true = absolute disk path (for PDFs), false = URL (for HTML)
 */
if (!function_exists('school_logo')) {
    function school_logo(bool $publicPath = false): string
    {
        $logo = setting('school_logo');

        if ($logo) {
            // Support both new path (uploads/school/...) and legacy path (school/...)
            $diskPath = public_path($logo);
            if (!file_exists($diskPath)) {
                $diskPath = public_path('storage/' . $logo);
            }

            if (file_exists($diskPath)) {
                return $publicPath
                    ? $diskPath
                    : asset($logo);
            }
        }

        // Fallback to default logo
        return $publicPath
            ? public_path('img/logo/school_logo.ico')
            : asset('img/logo/school_logo.ico');
    }
}

/**
 * Flush the cached settings (call after saving).
 */
if (!function_exists('flush_settings_cache')) {
    function flush_settings_cache(): void
    {
        Cache::forget('app_settings');
    }
}
