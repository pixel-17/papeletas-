<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if(auth()->user()->esJefe()) Bandeja de Aprobación
            @elseif(auth()->user()->esRrhh()) Bandeja RRHH
            @else Mis Papeletas
            @endif
        </h2>
    </x-slot>

    @if(auth()->user()->esJefe() || auth()->user()->esRrhh())
        <div class="flex gap-2 mb-4">
            <a href="{{ route('papeletas.index', array_merge($filtros, ['vista' => 'pendientes'])) }}"
               class="text-sm px-3 py-1.5 rounded-full {{ $vista === 'pendientes' ? 'bg-gray-800 text-white' : 'bg-white text-gray-600 border' }}">
                Pendientes
            </a>
            <a href="{{ route('papeletas.index', array_merge($filtros, ['vista' => 'todas'])) }}"
               class="text-sm px-3 py-1.5 rounded-full {{ $vista === 'todas' ? 'bg-gray-800 text-white' : 'bg-white text-gray-600 border' }}">
                Historial completo
            </a>
        </div>
    @endif

    <div class="flex justify-end gap-2 mb-4">
        @if(auth()->user()->esRrhh())
            <a href="{{ route('papeletas.exportar', array_merge($filtros, ['vista' => $vista])) }}"
               class="bg-green-600 text-white text-sm px-3 py-2 rounded">
                Exportar CSV
            </a>
        @endif
        @if(auth()->user()->esTrabajador() || auth()->user()->esJefe())
            <a href="{{ route('papeletas.create') }}"
               class="bg-blue-600 text-white text-sm px-3 py-2 rounded">
                + Nueva
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('papeletas.index') }}"
          class="bg-white rounded shadow p-4 mb-4 grid grid-cols-1 md:grid-cols-5 gap-3">
        <input type="hidden" name="vista" value="{{ $vista }}">
        <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}"
               placeholder="Código, destino o trabajador"
               class="border-gray-300 rounded text-sm col-span-1 md:col-span-2">

        <select name="estado_id" class="border-gray-300 rounded text-sm">
            <option value="">Todos los estados</option>
            @foreach ($estados as $estado)
                <option value="{{ $estado->id }}" @selected(($filtros['estado_id'] ?? null) == $estado->id)>
                    {{ $estado->nombre }}
                </option>
            @endforeach
        </select>

        @if ($areas->isNotEmpty())
            <select name="area_id" class="border-gray-300 rounded text-sm">
                <option value="">Todas las áreas</option>
                @foreach ($areas as $area)
                    <option value="{{ $area->id }}" @selected(($filtros['area_id'] ?? null) == $area->id)>
                        {{ $area->nombre }}
                    </option>
                @endforeach
            </select>
        @endif

        <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}"
               class="border-gray-300 rounded text-sm">
        <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}"
               class="border-gray-300 rounded text-sm">

        <div class="col-span-1 md:col-span-5 flex justify-end gap-2">
            <a href="{{ route('papeletas.index') }}" class="text-sm text-gray-500 px-3 py-2">Limpiar</a>
            <button type="submit" class="bg-gray-800 text-white text-sm px-4 py-2 rounded">Filtrar</button>
        </div>
    </form>

    <div class="space-y-3">
        @forelse ($papeletas as $papeleta)
            <a href="{{ route('papeletas.show', $papeleta) }}"
               class="block bg-white rounded shadow p-4 hover:bg-gray-50">
                <div class="flex justify-between items-start gap-2">
                    <div>
                        <p class="font-semibold text-sm">{{ $papeleta->codigo }}</p>
                        <p class="text-sm text-gray-600">{{ $papeleta->destino }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $papeleta->fecha_salida->format('d/m/Y') }}
                            · {{ $papeleta->motivo->nombre }}
                        </p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded whitespace-nowrap"
                          style="background-color: {{ $papeleta->estado->color }}20; color: {{ $papeleta->estado->color }};">
                        {{ $papeleta->estado->nombre }}
                    </span>
                </div>
            </a>
        @empty
            <p class="text-center text-gray-500 py-10">No hay papeletas por aquí.</p>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $papeletas->links() }}
    </div>
</x-app-layout>
