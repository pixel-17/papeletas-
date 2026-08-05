<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Área</h2>
    </x-slot>

    <form method="POST" action="{{ route('areas.update', $area) }}" class="bg-white rounded shadow p-4 space-y-4 max-w-lg">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" name="nombre" required value="{{ old('nombre', $area->nombre) }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Siglas</label>
            <input type="text" name="siglas" value="{{ old('siglas', $area->siglas) }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Descripción</label>
            <textarea name="descripcion" rows="3" class="w-full border rounded p-2">{{ old('descripcion', $area->descripcion) }}</textarea>
        </div>
        <div class="flex items-center gap-2">
        <input type="hidden" name="estado" value="0">
            <input type="checkbox" name="estado" value="1" id="estado" @checked(old('estado', $area->estado))>
            <label for="estado" class="text-sm">Activa</label>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Actualizar</button>
            <a href="{{ route('areas.index') }}" class="text-gray-600 px-4 py-2">Cancelar</a>
        </div>
    </form>
</x-app-layout>
