<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * Inicializa o trait Auditable e registra os eventos do modelo.
     */
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->audit('created');
        });

        static::updated(function ($model) {
            $model->audit('updated');
        });

        static::deleted(function ($model) {
            $model->audit('deleted');
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->audit('restored');
            });
        }
    }

    /**
     * Registra um log de auditoria para o modelo.
     */
    protected function audit(string $event): void
    {
        $oldValues = [];
        $newValues = [];

        if ($event === 'updated') {
            $newValues = $this->getDirty();
            foreach ($newValues as $key => $value) {
                $oldValues[$key] = $this->getOriginal($key);
            }

            // Ignorar campos de timestamp se não houver outras mudanças significas
            unset($newValues['updated_at'], $oldValues['updated_at']);

            if (empty($newValues)) {
                return;
            }
        } elseif ($event === 'created') {
            $newValues = $this->getAttributes();
            unset($newValues['created_at'], $newValues['updated_at']);
        } elseif ($event === 'deleted') {
            $oldValues = $this->getAttributes();
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'event' => $event,
            'auditable_type' => get_class($this),
            'auditable_id' => $this->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Relacionamento polimórfico com os logs de auditoria.
     */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
