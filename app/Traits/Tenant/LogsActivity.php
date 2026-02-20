<?php

namespace App\Traits\Tenant;

use App\Models\Tenant\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        foreach (static::getRecordEvents() as $event) {
            static::$event(function ($model) use ($event) {
                $model->logActivity($event);
            });
        }
    }

    protected static function getRecordEvents()
    {
        if (isset(static::$recordEvents)) {
            return static::$recordEvents;
        }

        return ['created', 'updated', 'deleted'];
    }

    public function logActivity($event)
    {
        $description = $this->activityDescription($event);
        
        // Evitaremos guardar contraseñas en los logs por seguridad
        $changes = $event === 'updated' ? $this->getActivityChanges() : ($event === 'created' ? $this->getCreatedData() : null);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $event,
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'description' => $description,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    protected function activityDescription($event)
    {
        $name = class_basename($this);
        return "{$name} ha sido {$event}";
    }

    protected function getActivityChanges()
    {
        $dirty = $this->getDirty();
        $original = $this->getOriginal();
        
        // Filtrar campos sensibles
        $sensitive = ['password', 'remember_token'];
        
        $before = array_intersect_key($original, $dirty);
        $after = $dirty;
        
        foreach ($sensitive as $field) {
            if (isset($before[$field])) $before[$field] = '[REDACTADO]';
            if (isset($after[$field])) $after[$field] = '[REDACTADO]';
        }

        return [
            'before' => $before,
            'after' => $after,
        ];
    }

    protected function getCreatedData()
    {
        $data = $this->toArray();
        $sensitive = ['password', 'remember_token'];
        
        foreach ($sensitive as $field) {
            if (isset($data[$field])) $data[$field] = '[REDACTADO]';
        }
        
        return $data;
    }
}
