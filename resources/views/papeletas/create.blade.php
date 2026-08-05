<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva Papeleta</h2>
    </x-slot>

    <form x-data="{ enviando: false }" @submit="enviando = true"
          method="POST" action="{{ route('papeletas.store') }}"
          class="bg-white rounded-lg shadow-sm p-5 space-y-5 max-w-lg">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
            <select name="motivo_id" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="">Selecciona un motivo</option>
                @foreach(\App\Models\Motivo::activos()->orderBy('nombre')->get() as $motivo)
                    <option value="{{ $motivo->id }}" @selected(old('motivo_id') == $motivo->id)>
                        {{ $motivo->nombre }}{{ $motivo->requiere_documento ? ' (requiere documento)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Destino</label>
            <input type="text" name="destino" required value="{{ old('destino') }}"
                   class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Ej: Municipalidad Provincial">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Detalle (opcional)</label>
            <textarea name="motivo_detalle" rows="3"
                      class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('motivo_detalle') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                <input type="date" name="fecha_salida" required value="{{ old('fecha_salida') }}"
                       class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hora salida</label>
                <input type="time" name="hora_salida_programada" required value="{{ old('hora_salida_programada') }}"
                       class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hora retorno</label>
                <input type="time" name="hora_retorno_programada" value="{{ old('hora_retorno_programada') }}"
                       class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" :disabled="enviando"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white px-5 py-2.5 rounded-lg font-medium w-full sm:w-auto justify-center transition">
                <svg x-show="enviando" x-cloak class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-text="enviando ? 'Enviando...' : 'Enviar solicitud'"></span>
            </button>
            <a href="{{ route('papeletas.index') }}" class="text-gray-500 hover:text-gray-700 px-4 py-2.5 text-sm">Cancelar</a>
        </div>
    </form>
</x-app-layout>
