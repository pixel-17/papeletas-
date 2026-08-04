<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarcarGpsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('marcar', $this->route('papeleta'));
    }

    public function rules(): array
    {
        return [
            'latitud' => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
            'precision_gps' => ['nullable', 'numeric', 'min:0'],
            'direccion' => ['nullable', 'string', 'max:255'],
        ];
    }
}
