<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Sede</h2>
    </x-slot>

    <form method="POST" action="{{ route('sedes.update', $sede) }}" class="bg-white rounded shadow p-4 space-y-4 max-w-lg">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" name="nombre" required value="{{ old('nombre', $sede->nombre) }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Dirección</label>
            <input type="text" name="direccion" value="{{ old('direccion', $sede->direccion) }}" class="w-full border rounded p-2">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1">Latitud</label>
                <input type="number" step="any" name="latitud" required value="{{ old('latitud', $sede->latitud) }}" class="w-full border rounded p-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Longitud</label>
                <input type="number" step="any" name="longitud" required value="{{ old('longitud', $sede->longitud) }}" class="w-full border rounded p-2">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Radio permitido (metros)</label>
            <input type="number" name="radio_permitido" required value="{{ old('radio_permitido', $sede->radio_permitido) }}" class="w-full border rounded p-2">
        </div>
        <div class="flex items-center gap-2">
        <input type="hidden" name="estado" value="0">
            <input type="checkbox" name="estado" value="1" id="estado" @checked(old('estado', $sede->estado))>
            <label for="estado" class="text-sm">Activa</label>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Actualizar</button>
            <a href="{{ route('sedes.index') }}" class="text-gray-600 px-4 py-2">Cancelar</a>
        </div>
    </form>
</x-app-layout>
