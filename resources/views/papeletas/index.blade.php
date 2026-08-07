<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-3">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
                    @if(auth()->user()->esJefe()) Bandeja de Aprobación
                    @elseif(auth()->user()->esRrhh()) Bandeja RRHH
                    @else Mis Papeletas
                    @endif
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    @if(auth()->user()->esJefe()) Solicitudes de tu área.
                    @elseif(auth()->user()->esRrhh()) Solicitudes aprobadas por el jefe de área.
                    @else Historial de tus solicitudes de salida.
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-2">
                @if(auth()->user()->esRrhh())
                    <a href="{{ route('papeletas.exportar', array_merge($filtros, ['vista' => $vista])) }}"
                       class="btn-glass text-white shadow-glass !px-4 !py-2" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Exportar
                    </a>
                @endif

                @if(auth()->user()->esTrabajador() || auth()->user()->esJefe())
                    <a href="{{ route('papeletas.create') }}" class="btn-primary !px-4 !py-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Nueva
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    {{-- Toggle Pendientes / Todas — solo tiene sentido para Jefe/RRHH --}}
    @if(auth()->user()->esJefe() || auth()->user()->esRrhh())
        <div class="flex gap-1 mb-4 glass p-1 rounded-xl w-fit">
            <a href="{{ route('papeletas.index', array_merge($filtros, ['vista' => 'pendientes'])) }}"
               class="text-sm px-4 py-1.5 rounded-lg font-semibold transition-all duration-200 {{ $vista === 'pendientes' ? 'bg-white shadow-sm text-brand-700' : 'text-gray-500 hover:text-gray-700' }}">
                Pendientes
            </a>
            <a href="{{ route('papeletas.index', array_merge($filtros, ['vista' => 'todas'])) }}"
               class="text-sm px-4 py-1.5 rounded-lg font-semibold transition-all duration-200 {{ $vista === 'todas' ? 'bg-white shadow-sm text-brand-700' : 'text-gray-500 hover:text-gray-700' }}">
                Todas
            </a>
        </div>
    @endif

    {{-- Filtros rápidos de estado --}}
    @if($estados->isNotEmpty())
        <div class="flex flex-wrap gap-1.5 mb-3">
            <a href="{{ route('papeletas.index', array_merge($filtros, ['vista' => $vista, 'estado_id' => null])) }}"
               class="text-xs font-semibold px-3 py-1.5 rounded-full transition-all duration-200 {{ empty($filtros['estado_id']) ? 'bg-gray-700 text-white shadow-glass' : 'glass text-gray-600 hover:bg-white/70' }}">
                Todos
            </a>
            @foreach($estados as $estado)
                <a href="{{ route('papeletas.index', array_merge($filtros, ['vista' => $vista, 'estado_id' => $estado->id])) }}"
                   class="text-xs font-semibold px-3 py-1.5 rounded-full transition-all duration-200 flex items-center gap-1.5"
                   style="{{ ($filtros['estado_id'] ?? null) == $estado->id ? "background-color: {$estado->color}; color: white; box-shadow: 0 8px 20px -6px {$estado->color}88;" : '' }}"
                   @class(['glass text-gray-600 hover:bg-white/70' => ($filtros['estado_id'] ?? null) != $estado->id])>
                    @if(($filtros['estado_id'] ?? null) != $estado->id)
                        <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $estado->color }}"></span>
                    @endif
                    {{ $estado->nombre }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- Filtros rápidos de fecha — un clic, sin llenar el formulario --}}
    <div class="flex flex-wrap gap-1.5 mb-3" x-data="{}">
        @php
            $chips = [
                'hoy' => ['label' => 'Hoy', 'desde' => now()->toDateString(), 'hasta' => now()->toDateString()],
                'semana' => ['label' => 'Esta semana', 'desde' => now()->startOfWeek()->toDateString(), 'hasta' => now()->endOfWeek()->toDateString()],
                'mes' => ['label' => 'Este mes', 'desde' => now()->startOfMonth()->toDateString(), 'hasta' => now()->endOfMonth()->toDateString()],
            ];
            $chipActivo = collect($chips)->search(fn ($c) => ($filtros['desde'] ?? null) === $c['desde'] && ($filtros['hasta'] ?? null) === $c['hasta']);
        @endphp

        @foreach($chips as $key => $chip)
            <a href="{{ route('papeletas.index', array_merge($filtros, ['vista' => $vista, 'desde' => $chip['desde'], 'hasta' => $chip['hasta']])) }}"
               class="text-xs font-semibold px-3 py-1.5 rounded-full transition-all duration-200 {{ $chipActivo === $key ? 'bg-brand-500 text-white shadow-glass' : 'glass text-gray-600 hover:bg-white/70' }}">
                {{ $chip['label'] }}
            </a>
        @endforeach

        @if(($filtros['desde'] ?? null) || ($filtros['hasta'] ?? null))
            <a href="{{ route('papeletas.index', ['vista' => $vista]) }}"
               class="text-xs font-semibold px-3 py-1.5 rounded-full glass text-gray-500 hover:text-rose-600 hover:bg-white/70 transition-all duration-200 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                Quitar fecha
            </a>
        @endif
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('papeletas.index') }}" class="glass-card p-4 mb-4 animate-fade-in-up">
        <input type="hidden" name="vista" value="{{ $vista }}">
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div class="col-span-2 sm:col-span-1">
                <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Buscar código, destino..."
                       class="input-glass !py-2 text-sm">
            </div>
            <div>
                <select name="estado_id" class="input-glass !py-2 text-sm">
                    <option value="">Todos los estados</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->id }}" @selected(($filtros['estado_id'] ?? null) == $estado->id)>{{ $estado->nombre }}</option>
                    @endforeach
                </select>
            </div>
            @if($areas->isNotEmpty())
                <div>
                    <select name="area_id" class="input-glass !py-2 text-sm">
                        <option value="">Todas las áreas</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" @selected(($filtros['area_id'] ?? null) == $area->id)>{{ $area->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div>
                <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}"
                       class="input-glass !py-2 text-sm">
            </div>
            <div class="flex gap-2">
                <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}"
                       class="input-glass !py-2 text-sm">
                <button type="submit" class="shrink-0 btn-secondary !px-3 !py-2">
                    Filtrar
                </button>
            </div>
        </div>
    </form>

    <p class="text-xs text-gray-400 mb-2 px-1">
        {{ $papeletas->total() }} {{ Str::plural('resultado', $papeletas->total()) }}
    </p>

    <div class="space-y-3 stagger">
        @forelse ($papeletas as $papeleta)
            <a href="{{ route('papeletas.show', $papeleta) }}"
               class="glass-card block p-4 border-l-4"
               style="border-left-color: {{ $papeleta->estado->color }}">
                <div class="flex justify-between items-start gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-semibold text-sm text-gray-800">{{ $papeleta->codigo }}</p>
                            @if(auth()->user()->esJefe() || auth()->user()->esRrhh())
                                <span class="text-xs text-gray-400">· {{ $papeleta->trabajador->name }}</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mt-0.5 truncate">{{ $papeleta->destino }}</p>
                        <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            {{ $papeleta->fecha_salida->format('d/m/Y') }} · {{ $papeleta->motivo->nombre }}
                        </p>
                    </div>
                    <x-status-badge :estado="$papeleta->estado" />
                </div>
            </a>
        @empty
            <div class="glass-card p-12 text-center animate-fade-in-up">
                <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                </svg>
                <p class="text-gray-500 mt-3 text-sm">No hay papeletas que coincidan con estos filtros.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $papeletas->links() }}
    </div>
</x-app-layout>
