<?php

namespace App\Http\Requests;

use App\Enums\RolUsuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ya protegido por middleware role:ADMINISTRADOR en la ruta
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        $reglasJefeId = ['nullable', 'exists:users,id'];
        if ($userId) {
            $reglasJefeId[] = Rule::notIn([$userId]);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'dni' => ['required', 'digits:8', Rule::unique('users', 'dni')->ignore($userId)],
            'telefono' => ['nullable', 'string', 'max:20'],
            'password' => [$userId ? 'nullable' : 'required', Password::defaults()],
            'cargo_id' => ['nullable', 'exists:cargos,id'],
            'sede_id' => ['nullable', 'exists:sedes,id'],
            'jefe_id' => $reglasJefeId,
            'rol' => ['required', new Enum(RolUsuario::class)],
            'estado' => ['sometimes', 'boolean'],
        ];
    }
}
