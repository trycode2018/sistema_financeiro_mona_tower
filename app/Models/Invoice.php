<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasAuditLog;

class Invoice extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'invoice_number',
        'student_id',
        'due_date',
        'issue_date',
        'status',
        'total_amount',
        'amount_paid',
        'description'
    ];

    protected $casts = [
        'due_date' => 'date',
        'issue_date' => 'date',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getBalanceAttribute()
    {
        return $this->total_amount - $this->amount_paid;
    }

    // Método para verificar se está vencida
    public function isOverdue()
    {
        return $this->due_date < now() && 
               $this->amount_paid < $this->total_amount &&
               !in_array($this->status, ['pago', 'aguardando_confirmacao']);
    }

    protected static function booted()
    {
        static::saving(function ($invoice) {
            // Atualizar status baseado em pagamentos
            $totalConfirmed = $invoice->payments()
                ->where('status', 'confirmed')
                ->sum('amount');
            
            $hasPending = $invoice->payments()
                ->where('status', 'pending')
                ->exists();
            
            if ($totalConfirmed >= $invoice->total_amount) {
                $invoice->status = 'pago';
            } elseif ($hasPending) {
                $invoice->status = 'aguardando_confirmacao';
            } elseif ($totalConfirmed > 0) {
                $invoice->status = 'parcial';
            } elseif ($invoice->due_date < now() && $totalConfirmed == 0) {
                $invoice->status = 'vencido'; // Mudado de 'overdue' para 'vencido'
            }
        });
    }
}