<?php

namespace App\Models;

use App\Enums\RolUsuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'dni', 'telefono',
        'cargo_id', 'sede_id', 'jefe_id', 'rol', 'estado',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'rol' => RolUsuario::class,
        'estado' => 'boolean',
    ];

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class);
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    public function jefe(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jefe_id');
    }

    public function subordinados(): HasMany
    {
        return $this->hasMany(User::class, 'jefe_id');
    }

    public function papeletas(): HasMany
    {
        return $this->hasMany(Papeleta::class, 'trabajador_id');
    }

    public function papeletasPorAprobar(): HasMany
    {
        return $this->hasMany(Papeleta::class, 'jefe_id');
    }

    public function flujoAprobaciones(): HasMany
    {
        return $this->hasMany(FlujoAprobacion::class, 'usuario_id');
    }

    public function notificaciones(): HasMany
    {
        return $this->hasMany(NotificacionSistema::class);
    }

    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function esJefe(): bool
    {
        return $this->rol === RolUsuario::JEFE;
    }

    public function esRrhh(): bool
    {
        return $this->rol === RolUsuario::RRHH;
    }

    public function esTrabajador(): bool
    {
        return $this->rol === RolUsuario::TRABAJADOR;
    }
}
