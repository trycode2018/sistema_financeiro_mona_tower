<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasAuditLog;

class Payment extends Model
{
    use HasFactory, HasAuditLog; // 2. Ativar o Trait dentro da classe

    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_date',
        'payment_method',
        'status',
        'reference',
        'notes'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}