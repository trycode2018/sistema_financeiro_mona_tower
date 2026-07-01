<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasAuditLog;
use App\Traits\InvoiceNotificationTrait;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasAuditLog, InvoiceNotificationTrait; // 2. Ativar o Trait dentro da classe

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSecretaria()
    {
        return $this->role === 'secretaria';
    }

    public function isFinanceiro()
    {
        return $this->role === 'financeiro';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}