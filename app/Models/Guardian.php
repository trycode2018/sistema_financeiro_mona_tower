<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasAuditLog;
use Illuminate\Notifications\Notifiable;

class Guardian extends Model
{
    use HasFactory, HasAuditLog, Notifiable; // 2. Ativar o Trait dentro da classe

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'relationship'
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}