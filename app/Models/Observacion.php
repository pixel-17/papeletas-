<?php

namespace App\Models;

use App\Enums\TipoObservacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Observacion extends Model
{
    use HasFactory;

    // Mismo caso que Marcacion/Configuracion: la tabla real es "observaciones".
    protected $table = 'observaciones';

    protected $fillable = ['papeleta_id', 'usuario_id', 'tipo', 'comentario', 'atendida'];

    protected $casts = [
        'tipo' => TipoObservacion::class,
        'atendida' => 'boolean',
    ];

    public function papeleta(): BelongsTo
    {
        return $this->belongsTo(Papeleta::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
