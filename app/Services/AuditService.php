<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    /**
     * Log an action to the audit trail.
     */
    public static function log(
        string $action,
        string $module,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $note = null
    ): AuditLog {
        $user = Auth::user();

        return AuditLog::create([
            'user_id'        => $user?->id,
            'user_name'      => $user?->name ?? 'System',
            'user_role'      => $user?->role ?? 'system',
            'action'         => $action,
            'module'         => $module,
            'auditable_type' => $model ? get_class($model) : null,
            'auditable_id'   => $model?->getKey(),
            'old_values'     => $oldValues,
            'new_values'     => $newValues,
            'ip_address'     => Request::ip(),
            'user_agent'     => Request::userAgent(),
            'url'            => Request::fullUrl(),
            'note'           => $note,
        ]);
    }

    /**
     * Log a model event (created/updated/deleted).
     */
    public static function modelEvent(string $action, Model $model, ?array $old = null): void
    {
        $module = class_basename($model);
        $new = $action === 'deleted' ? null : $model->getDirty();

        static::log($action, $module, $model, $old, $new);
    }
}
