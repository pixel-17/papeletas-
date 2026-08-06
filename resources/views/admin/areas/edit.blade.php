<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Área</h2>
    </x-slot>

    <form method="POST" action="{{ route('areas.update', $area) }}" class="bg-white rounded-lg shadow-sm p-5 space-y-4 max-w-lg">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
            <input type="text" name="nombre" required value="{{ old('nombre', $area->nombre) }}" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Siglas</label>
            <input type="text" name="siglas" value="{{ old('siglas', $area->siglas) }}" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea name="descripcion" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('descripcion', $area->descripcion) }}</textarea>
        </div>
        <div class="flex items-center gap-2">
            <input type="hidden" name="estado" value="0">
            <input type="checkbox" name="estado" value="1" id="estado" class="rounded" @checked(old('estado', $area->estado))>
            <label for="estado" class="text-sm text-gray-700">Activa</label>
        </div>
        <div class="flex gap-3 pt-1">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition">Actualizar</button>
            <a href="{{ route('areas.index') }}" class="text-gray-500 hover:text-gray-700 px-4 py-2.5 text-sm">Cancelar</a>
        </div>
    </form>
</x-app-layout>
