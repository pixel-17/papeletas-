<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Cargo</h2>
    </x-slot>

    <form method="POST" action="{{ route('cargos.update', $cargo) }}" class="bg-white rounded shadow p-4 space-y-4 max-w-lg">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium mb-1">Área</label>
            <select name="area_id" required class="w-full border rounded p-2">
                @foreach($areas as $area)
                    <option value="{{ $area->id }}" @selected(old('area_id', $cargo->area_id) == $area->id)>{{ $area->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" name="nombre" required value="{{ old('nombre', $cargo->nombre) }}" class="w-full border rounded p-2">
        </div>
        <div class="flex items-center gap-2">
        <input type="hidden" name="estado" value="0">
            <input type="checkbox" name="estado" value="1" id="estado" @checked(old('estado', $cargo->estado))>
            <label for="estado" class="text-sm">Activo</label>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Actualizar</button>
            <a href="{{ route('cargos.index') }}" class="text-gray-600 px-4 py-2">Cancelar</a>
        </div>
    </form>
</x-app-layout>
