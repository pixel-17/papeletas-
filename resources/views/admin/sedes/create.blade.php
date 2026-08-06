<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva Sede</h2>
    </x-slot>

    <form method="POST" action="{{ route('sedes.store') }}" class="bg-white rounded-lg shadow-sm p-5 space-y-4 max-w-lg">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
            <input type="text" name="nombre" required value="{{ old('nombre') }}" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
            <input type="text" name="direccion" value="{{ old('direccion') }}" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Latitud</label>
                <input type="number" step="any" name="latitud" required value="{{ old('latitud') }}" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Longitud</label>
                <input type="number" step="any" name="longitud" required value="{{ old('longitud') }}" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Radio permitido (metros)</label>
            <input type="number" name="radio_permitido" required value="{{ old('radio_permitido', 100) }}" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            <p class="text-xs text-gray-400 mt-1">Tolerancia GPS para validar marcaciones de salida/retorno.</p>
        </div>
        <div class="flex gap-3 pt-1">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition">Guardar</button>
            <a href="{{ route('sedes.index') }}" class="text-gray-500 hover:text-gray-700 px-4 py-2.5 text-sm">Cancelar</a>
        </div>
    </form>
</x-app-layout>
