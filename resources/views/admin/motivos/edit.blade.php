<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Motivo</h2>
    </x-slot>

    <form method="POST" action="{{ route('motivos.update', $motivo) }}" class="bg-white rounded-lg shadow-sm p-5 space-y-4 max-w-lg">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
            <input type="text" name="nombre" required value="{{ old('nombre', $motivo->nombre) }}" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Máx. horas (opcional)</label>
            <input type="number" name="max_horas" value="{{ old('max_horas', $motivo->max_horas) }}" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="flex items-center gap-2">
            <input type="hidden" name="requiere_documento" value="0">
            <input type="checkbox" name="requiere_documento" value="1" id="requiere_documento" class="rounded" @checked(old('requiere_documento', $motivo->requiere_documento))>
            <label for="requiere_documento" class="text-sm text-gray-700">Requiere documento sustentatorio</label>
        </div>
        <div class="flex items-center gap-2">
            <input type="hidden" name="goce_haber" value="0">
            <input type="checkbox" name="goce_haber" value="1" id="goce_haber" class="rounded" @checked(old('goce_haber', $motivo->goce_haber))>
            <label for="goce_haber" class="text-sm text-gray-700">Con goce de haber</label>
        </div>
        <div class="flex items-center gap-2">
            <input type="hidden" name="estado" value="0">
            <input type="checkbox" name="estado" value="1" id="estado" class="rounded" @checked(old('estado', $motivo->estado))>
            <label for="estado" class="text-sm text-gray-700">Activo</label>
        </div>
        <div class="flex gap-3 pt-1">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium transition">Actualizar</button>
            <a href="{{ route('motivos.index') }}" class="text-gray-500 hover:text-gray-700 px-4 py-2.5 text-sm">Cancelar</a>
        </div>
    </form>
</x-app-layout>
