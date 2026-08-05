<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Usuario</h2>
    </x-slot>

    <form method="POST" action="{{ route('users.update', $user) }}" class="bg-white rounded shadow p-4 space-y-4 max-w-lg">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">Nombre completo</label>
            <input type="text" name="name" required value="{{ old('name', $user->name) }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" required value="{{ old('email', $user->email) }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">DNI</label>
            <input type="text" name="dni" required maxlength="8" value="{{ old('dni', $user->dni) }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Teléfono</label>
            <input type="text" name="telefono" value="{{ old('telefono', $user->telefono) }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Nueva contraseña</label>
            <input type="password" name="password" class="w-full border rounded p-2">
            <p class="text-xs text-gray-400 mt-1">Déjalo en blanco para no cambiarla.</p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Rol</label>
            @php $rolActual = old('rol', $user->getRoleNames()->first()); @endphp
            <select name="rol" required class="w-full border rounded p-2">
                @foreach(\App\Enums\RolUsuario::cases() as $rol)
                    <option value="{{ $rol->value }}" @selected($rolActual === $rol->value)>{{ $rol->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Cargo</label>
            <select name="cargo_id" class="w-full border rounded p-2">
                <option value="">Sin asignar</option>
                @foreach($cargos as $cargo)
                    <option value="{{ $cargo->id }}" @selected(old('cargo_id', $user->cargo_id) == $cargo->id)>{{ $cargo->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Sede</label>
            <select name="sede_id" class="w-full border rounded p-2">
                <option value="">Sin asignar</option>
                @foreach($sedes as $sede)
                    <option value="{{ $sede->id }}" @selected(old('sede_id', $user->sede_id) == $sede->id)>{{ $sede->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Jefe inmediato</label>
            <select name="jefe_id" class="w-full border rounded p-2">
                <option value="">Sin asignar</option>
                @foreach($jefes as $jefe)
                    <option value="{{ $jefe->id }}" @selected(old('jefe_id', $user->jefe_id) == $jefe->id)>{{ $jefe->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-2">
            <input type="hidden" name="estado" value="0">
            <input type="checkbox" name="estado" value="1" id="estado" @checked(old('estado', $user->estado))>
            <label for="estado" class="text-sm">Activo</label>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Actualizar</button>
            <a href="{{ route('users.index') }}" class="text-gray-600 px-4 py-2">Cancelar</a>
        </div>
    </form>
</x-app-layout>
