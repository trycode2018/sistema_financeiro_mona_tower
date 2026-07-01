<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasAuditLog;
use Illuminate\Notifications\Notifiable;
use App\Models\Service;

class Student extends Model
{
    use HasFactory, HasAuditLog, Notifiable; // 2. Ativar o Trait dentro da classe dentro da classe

    public function services()
    {
        return $this->belongsToMany(Service::class)
            ->withPivot([
                'start_date',
                'end_date',
                'status'
            ])
            ->withTimestamps();
    }
    protected $fillable = [
        'student_code',
        'name',
        'email',
        'class',
        'academic_year',
        'guardian_id',
        'transport_required'
    ];

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function pendingInvoices()
    {
        return $this->invoices()->whereIn('status', ['pending', 'overdue']);
    }
}