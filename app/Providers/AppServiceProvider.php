<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use App\Services\MenuService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        require_once app_path('Helpers/helpers.php');

        // Share dynamic sidebar menu with all views
        View::composer('admin.layout.master', function ($view) {
            if (auth()->check()) {
                try {
                    $view->with('sidebarMenu', MenuService::getFilteredSidebar());
                } catch (\Exception $e) {
                    // Fallback: show full menu if RBAC tables don't exist yet
                    $view->with('sidebarMenu', MenuService::getSidebar());
                }
            } else {
                $view->with('sidebarMenu', []);
            }
        });

        // Custom Blade directives for permission checks
        Blade::if('permission', function (string $permission) {
            try {
                return auth()->check() && auth()->user()->hasPermission($permission);
            } catch (\Exception $e) {
                return auth()->check() && in_array(auth()->user()->role, ['admin', 'super_admin']);
            }
        });

        Blade::if('role', function (string $role) {
            try {
                return auth()->check() && auth()->user()->hasRole($role);
            } catch (\Exception $e) {
                return auth()->check() && auth()->user()->role === $role;
            }
        });

        Blade::if('anypermission', function (...$permissions) {
            try {
                return auth()->check() && auth()->user()->hasAnyPermission($permissions);
            } catch (\Exception $e) {
                return auth()->check() && in_array(auth()->user()->role, ['admin', 'super_admin']);
            }
        });
    }
}
