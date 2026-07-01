<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait HasAuditLog
{
    /**
     * O Laravel reconhece automaticamente métodos com o nome "boot[NomeDoTrait]"
     * e executa-os quando o Model arranca.
     */
    public static function bootHasAuditLog()
    {
        // Dispara quando um registo é acabado de criar
        static::created(function ($model) {
            static::logAuditAction($model, 'Criou');
        });

        // Dispara quando um registo é atualizado
        static::updated(function ($model) {
            static::logAuditAction($model, 'Atualizou');
        });

        // Dispara quando um registo é eliminado
        static::deleted(function ($model) {
            static::logAuditAction($model, 'Eliminou');
        });
    }

    /**
     * Lógica central para gravar o Log de Auditoria.
     */
    protected static function logAuditAction($model, $action)
    {
        // Define os valores antigos e novos com base na ação
        $oldValues = null;
        $newValues = null;

        if ($action === 'Criou') {
            $newValues = $model->getAttributes();
        } elseif ($action === 'Atualizou') {
            // getRawOriginal() apanha o estado antes de gravar
            // getChanges() apanha apenas o que foi alterado
            $oldValues = array_intersect_key($model->getRawOriginal(), $model->getChanges());
            $newValues = $model->getChanges();
        } elseif ($action === 'Eliminou') {
            $oldValues = $model->getAttributes();
        }

        // Cria o registo na tabela audit_logs usando o teu Model polimórfico
        AuditLog::create([
            'user_id'        => Auth::id(), // Regista quem está logado (ou null se for o sistema/CLI)
            'action'         => $action,
            'auditable_type' => get_class($model),
            'auditable_id'   => $model->id,
            'old_values'     => $oldValues,
            'new_values'     => $newValues,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }
}