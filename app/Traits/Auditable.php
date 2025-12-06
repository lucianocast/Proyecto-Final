<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Auditable
{
    /**
     * Boot del trait - registra eventos del modelo
     */
    public static function bootAuditable(): void
    {
        // Registrar creación
        static::created(function ($model) {
            AuditLog::createLog(
                action: 'created',
                model: $model,
                newValues: $model->getAttributes()
            );
        });

        // Registrar actualización
        static::updated(function ($model) {
            // Solo auditar si realmente hubo cambios
            if ($model->isDirty()) {
                AuditLog::createLog(
                    action: 'updated',
                    model: $model,
                    oldValues: $model->getOriginal(),
                    newValues: $model->getChanges()
                );
            }
        });

        // Registrar eliminación
        static::deleted(function ($model) {
            AuditLog::createLog(
                action: 'deleted',
                model: $model,
                oldValues: $model->getOriginal()
            );
        });
    }

    /**
     * Relación polimórfica con los logs de auditoría
     */
    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable')->latest();
    }

    /**
     * Método helper para registrar acciones personalizadas con justificación
     */
    public function auditAction(string $action, ?string $justification = null, ?array $data = []): AuditLog
    {
        return AuditLog::createLog(
            action: $action,
            model: $this,
            newValues: $data,
            justification: $justification
        );
    }

    /**
     * Obtener el último log de auditoría
     */
    public function getLastAuditLog(): ?AuditLog
    {
        return $this->auditLogs()->first();
    }

    /**
     * Obtener historial de cambios
     */
    public function getAuditHistory(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->auditLogs()->with('user')->get();
    }
}
