<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Student::class   => \App\Policies\StudentPolicy::class,
        \App\Models\Teacher::class   => \App\Policies\TeacherPolicy::class,
        \App\Models\ClassRoom::class => \App\Policies\ClassRoomPolicy::class,
        \App\Models\User::class      => \App\Policies\UserPolicy::class,
        \App\Models\Exam::class      => \App\Policies\ExamPolicy::class,
        \App\Models\Notice::class    => \App\Policies\NoticePolicy::class,
        \App\Models\Voucher::class   => \App\Policies\VoucherPolicy::class,
        \App\Models\Payroll::class   => \App\Policies\PayrollPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Admin-only gates
        Gate::define('manage-settings', fn ($user) => in_array($user->role, ['admin', 'super_admin']));
        Gate::define('view-logs', fn ($user) => in_array($user->role, ['admin', 'super_admin']));
        Gate::define('manage-backup', fn ($user) => in_array($user->role, ['admin', 'super_admin']));
    }
}
