<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    /**
     * Atributos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    /**
     * Conversão de tipos (Casting).
     * Crucial para que o Laravel trate os campos JSON como Arrays automaticamente.
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relacionamento Polimórfico (O "Coração" da Auditoria).
     * Permite que este log se associe a qualquer Model (Invoice, Student, User, etc.)
     * sem precisar de tabelas de ligação extras.
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relacionamento com o Utilizador.
     * Identifica o autor da ação (quem estava logado no momento).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Método Auxiliar (Opcional): Formata a visualização da ação.
     * Útil para exibir numa tabela de administração.
     */
    public function getEntityNameAttribute()
    {
        // Retorna apenas o nome da classe (ex: 'Invoice') em vez do caminho completo
        return class_basename($this->auditable_type);
    }
}