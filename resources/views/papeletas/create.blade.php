<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva Papeleta</h2>
    </x-slot>

    @if(auth()->user()->sede)
        <div class="bg-blue-50 border border-blue-100 text-sm text-blue-800 rounded p-3 mb-4">
            Tu sede asignada es <strong>{{ auth()->user()->sede->nombre }}</strong>
            @if(auth()->user()->sede->direccion)
                ({{ auth()->user()->sede->direccion }})
            @endif.
            Cuando marques tu salida/retorno por GPS, se compara contra este punto
            (radio permitido: {{ auth()->user()->sede->radio_permitido }} m).
        </div>
    @else
        <div class="bg-amber-50 border border-amber-100 text-sm text-amber-800 rounded p-3 mb-4">
            Todavía no tienes una sede asignada — consulta con RRHH antes de marcar tu GPS.
        </div>
    @endif

    <form method="POST" action="{{ route('papeletas.store') }}" class="bg-white rounded shadow p-4 space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Motivo</label>
            <select name="motivo_id" required class="w-full border rounded p-2">
                <option value="">Selecciona un motivo</option>
                @foreach(\App\Models\Motivo::activos()->orderBy('nombre')->get() as $motivo)
                    <option value="{{ $motivo->id }}" @selected(old('motivo_id') == $motivo->id)>
                        {{ $motivo->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Destino</label>
            <input type="text" name="destino" required value="{{ old('destino') }}"
                   class="w-full border rounded p-2" placeholder="Ej: Municipalidad Provincial">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Detalle (opcional)</label>
            <textarea name="motivo_detalle" rows="3" class="w-full border rounded p-2">{{ old('motivo_detalle') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1">Fecha</label>
                <input type="date" name="fecha_salida" required value="{{ old('fecha_salida') }}"
                       class="w-full border rounded p-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Hora salida</label>
                <input type="time" name="hora_salida_programada" required value="{{ old('hora_salida_programada') }}"
                       class="w-full border rounded p-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Hora retorno</label>
                <input type="time" name="hora_retorno_programada" value="{{ old('hora_retorno_programada') }}"
                       class="w-full border rounded p-2">
            </div>
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full sm:w-auto">
                Enviar solicitud
            </button>
            <a href="{{ route('papeletas.index') }}" class="text-gray-600 px-4 py-2">Cancelar</a>
        </div>
    </form>
</x-app-layout>
