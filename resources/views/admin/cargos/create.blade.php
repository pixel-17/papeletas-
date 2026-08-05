<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo Cargo</h2>
    </x-slot>

    <form method="POST" action="{{ route('cargos.store') }}" class="bg-white rounded shadow p-4 space-y-4 max-w-lg">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Área</label>
            <select name="area_id" required class="w-full border rounded p-2">
                <option value="">Selecciona un área</option>
                @foreach($areas as $area)
                    <option value="{{ $area->id }}" @selected(old('area_id') == $area->id)>{{ $area->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" name="nombre" required value="{{ old('nombre') }}" class="w-full border rounded p-2">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
            <a href="{{ route('cargos.index') }}" class="text-gray-600 px-4 py-2">Cancelar</a>
        </div>
    </form>
</x-app-layout>
