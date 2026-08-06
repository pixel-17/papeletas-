<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

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

    /**
     * `after_or_equal:today` en fecha_salida solo valida el día; si la fecha
     * es HOY, falta impedir una hora_salida_programada que ya pasó. Se hace
     * aparte porque Laravel no tiene una regla nativa que compare una hora
     * contra "ahora mismo" condicionada a otro campo de fecha.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->filled(['fecha_salida', 'hora_salida_programada'])) {
                return;
            }

            $fecha = Carbon::parse($this->input('fecha_salida'))->toDateString();

            if ($fecha !== now()->toDateString()) {
                return;
            }

            $horaSolicitada = Carbon::parse($fecha.' '.$this->input('hora_salida_programada'));

            if ($horaSolicitada->lessThan(now())) {
                $validator->errors()->add(
                    'hora_salida_programada',
                    'La hora de salida no puede ser anterior a la hora actual.'
                );
            }
        });
    }
}
