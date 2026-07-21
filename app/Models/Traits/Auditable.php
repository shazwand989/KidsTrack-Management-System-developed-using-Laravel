<?php

namespace App\Models\Traits;

use App\Services\AuditService;

/**
 * Add to Eloquent models to auto-log create/update/delete events.
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            AuditService::modelEvent('created', $model);
        });

        static::updated(function ($model) {
            $old = $model->getOriginal();
            $dirty = $model->getDirty();
            // Only log changed fields
            $oldChanged = array_intersect_key($old, $dirty);
            AuditService::modelEvent('updated', $model, $oldChanged);
        });

        static::deleted(function ($model) {
            AuditService::modelEvent('deleted', $model, $model->getOriginal());
        });
    }
}
