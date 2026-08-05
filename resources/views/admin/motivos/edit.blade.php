<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Motivo</h2>
    </x-slot>

    <form method="POST" action="{{ route('motivos.update', $motivo) }}" class="bg-white rounded shadow p-4 space-y-4 max-w-lg">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" name="nombre" required value="{{ old('nombre', $motivo->nombre) }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Máx. horas (opcional)</label>
            <input type="number" name="max_horas" value="{{ old('max_horas', $motivo->max_horas) }}" class="w-full border rounded p-2">
        </div>
        <div class="flex items-center gap-2">
            <input type="hidden" name="requiere_documento" value="0">
            <input type="checkbox" name="requiere_documento" value="1" id="requiere_documento" @checked(old('requiere_documento', $motivo->requiere_documento))>
            <label for="requiere_documento" class="text-sm">Requiere documento sustentatorio</label>
        </div>
        <div class="flex items-center gap-2">
            <input type="hidden" name="goce_haber" value="0">
            <input type="checkbox" name="goce_haber" value="1" id="goce_haber" @checked(old('goce_haber', $motivo->goce_haber))>
            <label for="goce_haber" class="text-sm">Con goce de haber</label>
        </div>
        <div class="flex items-center gap-2">
            <input type="hidden" name="estado" value="0">
            <input type="checkbox" name="estado" value="1" id="estado" @checked(old('estado', $motivo->estado))>
            <label for="estado" class="text-sm">Activo</label>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Actualizar</button>
            <a href="{{ route('motivos.index') }}" class="text-gray-600 px-4 py-2">Cancelar</a>
        </div>
    </form>
</x-app-layout>
