<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo Motivo</h2>
    </x-slot>

    <form method="POST" action="{{ route('motivos.store') }}" class="bg-white rounded shadow p-4 space-y-4 max-w-lg">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" name="nombre" required value="{{ old('nombre') }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Máx. horas (opcional)</label>
            <input type="number" name="max_horas" value="{{ old('max_horas') }}" class="w-full border rounded p-2">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="requiere_documento" value="1" id="requiere_documento" @checked(old('requiere_documento'))>
            <label for="requiere_documento" class="text-sm">Requiere documento sustentatorio</label>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="goce_haber" value="1" id="goce_haber" @checked(old('goce_haber'))>
            <label for="goce_haber" class="text-sm">Con goce de haber</label>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
            <a href="{{ route('motivos.index') }}" class="text-gray-600 px-4 py-2">Cancelar</a>
        </div>
    </form>
</x-app-layout>
