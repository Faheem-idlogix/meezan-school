<?php

namespace App\Traits;

use App\Models\ActivityLog;

/**
 * Automatically logs created, updated, deleted, restored, and force-deleted events.
 *
 * Usage: Add `use \App\Traits\LogsActivity;` to any Eloquent model.
 */
trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            ActivityLog::log(
                'created',
                class_basename($model) . ' created: ' . ($model->getKey()),
                $model,
                null,
                $model->getAttributes()
            );
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            // Skip if only timestamps changed
            $meaningful = collect($dirty)->except(['updated_at', 'created_at'])->toArray();
            if (empty($meaningful)) return;

            $original = collect($model->getOriginal())->only(array_keys($dirty))->toArray();

            // Mask sensitive fields
            $sensitiveFields = ['password', 'api_key', 'whatsapp_api_key', 'remember_token'];
            foreach ($sensitiveFields as $field) {
                if (isset($original[$field])) $original[$field] = '***';
                if (isset($dirty[$field])) $dirty[$field] = '***';
            }

            ActivityLog::log(
                'updated',
                class_basename($model) . ' updated: ' . ($model->getKey()),
                $model,
                $original,
                $dirty
            );
        });

        static::deleted(function ($model) {
            $action = $model->isForceDeleting() ? 'force_deleted' : 'deleted';
            ActivityLog::log(
                $action,
                class_basename($model) . " {$action}: " . ($model->getKey()),
                $model,
                $model->getAttributes(),
                null
            );
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                ActivityLog::log(
                    'restored',
                    class_basename($model) . ' restored: ' . ($model->getKey()),
                    $model,
                    null,
                    $model->getAttributes()
                );
            });
        }
    }
}
