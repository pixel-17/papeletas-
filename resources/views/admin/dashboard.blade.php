<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel de Administración</h2>
    </x-slot>

    {{-- Contadores básicos --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6">
        <div class="bg-white rounded shadow p-4 text-center">
            <p class="text-2xl font-bold">{{ $totalUsuarios }}</p>
            <p class="text-xs text-gray-500">Usuarios</p>
        </div>
        <div class="bg-white rounded shadow p-4 text-center">
            <p class="text-2xl font-bold">{{ $totalAreas }}</p>
            <p class="text-xs text-gray-500">Áreas</p>
        </div>
        <div class="bg-white rounded shadow p-4 text-center">
            <p class="text-2xl font-bold">{{ $totalSedes }}</p>
            <p class="text-xs text-gray-500">Sedes</p>
        </div>
    </div>

    {{-- Papeletas por estado --}}
    <div class="bg-white rounded shadow p-4 mb-6">
        <h3 class="font-semibold text-sm mb-3">Papeletas por estado</h3>
        @forelse($papeletasPorEstado as $fila)
            <div class="flex justify-between items-center py-1 text-sm">
                <span class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full inline-block" style="background-color: {{ $fila->color }}"></span>
                    {{ $fila->nombre }}
                </span>
                <span class="font-semibold">{{ $fila->total }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-500">Aún no hay papeletas registradas.</p>
        @endforelse
    </div>

    {{-- Accesos rápidos a catálogos --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('users.index') }}" class="bg-white rounded shadow p-4 text-center text-sm hover:bg-gray-50">
            👤 Usuarios
        </a>
        <a href="{{ route('areas.index') }}" class="bg-white rounded shadow p-4 text-center text-sm hover:bg-gray-50">
            🏢 Áreas
        </a>
        <a href="{{ route('cargos.index') }}" class="bg-white rounded shadow p-4 text-center text-sm hover:bg-gray-50">
            🧑‍💼 Cargos
        </a>
        <a href="{{ route('sedes.index') }}" class="bg-white rounded shadow p-4 text-center text-sm hover:bg-gray-50">
            📍 Sedes
        </a>
        <a href="{{ route('motivos.index') }}" class="bg-white rounded shadow p-4 text-center text-sm hover:bg-gray-50">
            📋 Motivos
        </a>
        <a href="{{ route('papeletas.index') }}" class="bg-white rounded shadow p-4 text-center text-sm hover:bg-gray-50">
            📄 Papeletas
        </a>
    </div>
</x-app-layout>
