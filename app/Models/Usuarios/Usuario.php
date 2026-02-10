<?php

namespace App\Models\Usuarios;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Roles\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Role|null $role
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Usuario extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
    ];

    /**
     * Obtener el rol asociado al usuario.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Verificar si el usuario tiene un permiso específico.
     */
    public function hasPermission(string $slug): bool
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->permissions()->where('slug', $slug)->exists();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
