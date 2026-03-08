<?php

namespace App\Exceptions;

use App\Models\SystemErrorLog;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            $this->logToDatabase($e);
        });
    }

    /**
     * Log exception details to the system_error_logs table.
     */
    private function logToDatabase(Throwable $e): void
    {
        try {
            // Skip if the table doesn't exist yet
            if (!\Illuminate\Support\Facades\Schema::hasTable('system_error_logs')) {
                return;
            }

            $type = 'exception';
            $severity = 'error';

            if ($e instanceof QueryException) {
                $type = 'database';
                $severity = 'critical';
            } elseif ($e instanceof ValidationException) {
                $type = 'validation';
                $severity = 'warning';
            } elseif ($e instanceof \Error) {
                $severity = 'critical';
            }

            $request = request();

            SystemErrorLog::create([
                'type'     => $type,
                'severity' => $severity,
                'message'  => \Illuminate\Support\Str::limit($e->getMessage(), 2000),
                'file'     => $e->getFile(),
                'line'     => $e->getLine(),
                'trace'    => \Illuminate\Support\Str::limit($e->getTraceAsString(), 5000),
                'url'      => $request?->fullUrl(),
                'method'   => $request?->method(),
                'user_id'  => auth()->id(),
                'ip'       => $request?->ip(),
                'context'  => [
                    'class' => get_class($e),
                    'code'  => $e->getCode(),
                ],
            ]);
        } catch (\Throwable $logException) {
            // Silently fail to avoid infinite loop
        }
    }
}
