<?php

namespace App\Models\Central;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CentralUser extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $connection = 'central';

    protected $table = 'users'; 

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    public function hasPermission(string $slug): bool
    {
        // Los usuarios centrales tienen todos los permisos
        return true;
    }

    public function isAdmin(): bool
    {
        // Los usuarios centrales son administradores del panel central
        return true;
    }

    public function getRoleAttribute()
    {
        // Simulamos un objeto de rol para evitar errores en vistas que esperan $user->role->nombre
        return (object) ['nombre' => 'Administrador Central'];
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\Auth\ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new \App\Notifications\Auth\VerifyEmailNotification());
    }
}
