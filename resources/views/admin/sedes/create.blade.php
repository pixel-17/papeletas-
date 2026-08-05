<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva Sede</h2>
    </x-slot>

    <form method="POST" action="{{ route('sedes.store') }}" class="bg-white rounded shadow p-4 space-y-4 max-w-lg">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" name="nombre" required value="{{ old('nombre') }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Dirección</label>
            <input type="text" name="direccion" value="{{ old('direccion') }}" class="w-full border rounded p-2">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1">Latitud</label>
                <input type="number" step="any" name="latitud" required value="{{ old('latitud') }}" class="w-full border rounded p-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Longitud</label>
                <input type="number" step="any" name="longitud" required value="{{ old('longitud') }}" class="w-full border rounded p-2">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Radio permitido (metros)</label>
            <input type="number" name="radio_permitido" required value="{{ old('radio_permitido', 100) }}" class="w-full border rounded p-2">
            <p class="text-xs text-gray-400 mt-1">Tolerancia GPS para validar marcaciones de salida/retorno.</p>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
            <a href="{{ route('sedes.index') }}" class="text-gray-600 px-4 py-2">Cancelar</a>
        </div>
    </form>
</x-app-layout>
