<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cargo extends Model
{
    use HasFactory;

    // "Cargo" es palabra inglesa con plural irregular ("cargoes"); se fija
    // explícito para asegurar que apunte a la tabla real "cargos".
    protected $table = 'cargos';

    protected $fillable = ['area_id', 'nombre', 'estado'];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}
