<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

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

    public function isOverdue()
    {
        return $this->due_date < now() && $this->status === 'pending';
    }

    // Atualizar o status automaticamente
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($invoice) {
            if ($invoice->isOverdue()) {
                $invoice->status = 'overdue';
            }
        });
    }
}