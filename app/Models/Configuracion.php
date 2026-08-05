<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    // "Configuracion" pluraliza mal en inglés (Eloquent adivinaría
    // "configuracions"); la tabla real es "configuraciones".
    protected $table = 'configuraciones';

    protected $fillable = ['clave', 'valor', 'descripcion'];

    public static function get(string $clave, mixed $default = null): mixed
    {
        return static::where('clave', $clave)->value('valor') ?? $default;
    }

    public static function set(string $clave, mixed $valor, ?string $descripcion = null): void
    {
        static::updateOrCreate(
            ['clave' => $clave],
            ['valor' => $valor, 'descripcion' => $descripcion]
        );
    }
}
