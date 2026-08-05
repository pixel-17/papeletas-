<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo Usuario</h2>
    </x-slot>

    <form method="POST" action="{{ route('users.store') }}" class="bg-white rounded shadow p-4 space-y-4 max-w-lg">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Nombre completo</label>
            <input type="text" name="name" required value="{{ old('name') }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" required value="{{ old('email') }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">DNI</label>
            <input type="text" name="dni" required maxlength="8" value="{{ old('dni') }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Teléfono</label>
            <input type="text" name="telefono" value="{{ old('telefono') }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Contraseña</label>
            <input type="password" name="password" required class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Rol</label>
            <select name="rol" required class="w-full border rounded p-2">
                <option value="">Selecciona un rol</option>
                @foreach(\App\Enums\RolUsuario::cases() as $rol)
                    <option value="{{ $rol->value }}" @selected(old('rol') === $rol->value)>{{ $rol->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Cargo</label>
            <select name="cargo_id" class="w-full border rounded p-2">
                <option value="">Sin asignar</option>
                @foreach($cargos as $cargo)
                    <option value="{{ $cargo->id }}" @selected(old('cargo_id') == $cargo->id)>{{ $cargo->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Sede</label>
            <select name="sede_id" class="w-full border rounded p-2">
                <option value="">Sin asignar</option>
                @foreach($sedes as $sede)
                    <option value="{{ $sede->id }}" @selected(old('sede_id') == $sede->id)>{{ $sede->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Jefe inmediato</label>
            <select name="jefe_id" class="w-full border rounded p-2">
                <option value="">Sin asignar</option>
                @foreach($jefes as $jefe)
                    <option value="{{ $jefe->id }}" @selected(old('jefe_id') == $jefe->id)>{{ $jefe->name }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-400 mt-1">Solo aplica si el rol es Trabajador.</p>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
            <a href="{{ route('users.index') }}" class="text-gray-600 px-4 py-2">Cancelar</a>
        </div>
    </form>
</x-app-layout>
