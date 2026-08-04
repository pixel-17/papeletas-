<?php

namespace App\Models;

use App\Enums\TipoMarcacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Marcacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'papeleta_id', 'tipo', 'latitud', 'longitud', 'precision_gps',
        'direccion', 'dentro_radio_permitido', 'ip_origen', 'user_agent',
    ];

    protected $casts = [
        'tipo' => TipoMarcacion::class,
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
        'dentro_radio_permitido' => 'boolean',
    ];

    public function papeleta(): BelongsTo
    {
        return $this->belongsTo(Papeleta::class);
    }
}
