<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePapeletaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('crear', \App\Models\Papeleta::class);
    }

    public function rules(): array
    {
        return [
            'motivo_id' => ['required', 'exists:motivos,id'],
            'destino' => ['required', 'string', 'max:255'],
            'motivo_detalle' => ['nullable', 'string'],
            'fecha_salida' => ['required', 'date', 'after_or_equal:today'],
            'hora_salida_programada' => ['required', 'date_format:H:i'],
            'hora_retorno_programada' => ['nullable', 'date_format:H:i', 'after:hora_salida_programada'],
        ];
    }
}
