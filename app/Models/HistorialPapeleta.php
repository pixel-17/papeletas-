<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialPapeleta extends Model
{
    use HasFactory;

    protected $table = 'historial_papeletas';

    protected $fillable = [
        'papeleta_id', 'usuario_id', 'accion', 'estado_anterior', 'estado_nuevo', 'descripcion', 'ip',
    ];

    public function papeleta(): BelongsTo
    {
        return $this->belongsTo(Papeleta::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public static function registrar(
        Papeleta $papeleta,
        ?User $usuario,
        string $accion,
        ?string $estadoAnterior,
        ?string $estadoNuevo,
        ?string $descripcion = null,
    ): self {
        return static::create([
            'papeleta_id' => $papeleta->id,
            'usuario_id' => $usuario?->id,
            'accion' => $accion,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $estadoNuevo,
            'descripcion' => $descripcion,
            'ip' => request()->ip(),
        ]);
    }
}
