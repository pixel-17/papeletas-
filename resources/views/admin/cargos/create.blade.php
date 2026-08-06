<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo Cargo</h2>
    </x-slot>

    <form method="POST" action="{{ route('cargos.store') }}" class="bg-white rounded-lg shadow-sm p-5 space-y-4 max-w-lg">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Área</label>
            <select name="area_id" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="">Selecciona un área</option>
                @foreach($areas as $area)
                    <option value="{{ $area->id }}" @selected(old('area_id') == $area->id)>{{ $area->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
            <input type="text" name="nombre" required value="{{ old('nombre') }}" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="flex gap-3 pt-1">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition">Guardar</button>
            <a href="{{ route('cargos.index') }}" class="text-gray-500 hover:text-gray-700 px-4 py-2.5 text-sm">Cancelar</a>
        </div>
    </form>
</x-app-layout>
