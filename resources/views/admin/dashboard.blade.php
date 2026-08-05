<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel de Administración</h2>
    </x-slot>

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4">
        <div class="bg-white rounded-lg shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 text-lg shrink-0">👤</div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $totalUsuarios }}</p>
                <p class="text-xs text-gray-500">Usuarios</p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600 text-lg shrink-0">🏢</div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $totalAreas }}</p>
                <p class="text-xs text-gray-500">Áreas</p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center text-green-600 text-lg shrink-0">📍</div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $totalSedes }}</p>
                <p class="text-xs text-gray-500">Sedes</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-5 mb-4">
        <h3 class="font-semibold text-sm text-gray-800 mb-4">Papeletas por estado</h3>
        @forelse($papeletasPorEstado as $fila)
            <div class="flex items-center gap-3 py-2">
                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $fila->color }}"></span>
                <span class="text-sm text-gray-700 flex-1">{{ $fila->nombre }}</span>
                <span class="text-sm font-semibold text-gray-800">{{ $fila->total }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-400">Aún no hay papeletas registradas.</p>
        @endforelse
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        <a href="{{ route('users.index') }}" class="bg-white rounded-lg shadow-sm p-4 text-center hover:shadow-md transition">
            <div class="text-2xl mb-1">👤</div>
            <span class="text-sm text-gray-700 font-medium">Usuarios</span>
        </a>
        <a href="{{ route('areas.index') }}" class="bg-white rounded-lg shadow-sm p-4 text-center hover:shadow-md transition">
            <div class="text-2xl mb-1">🏢</div>
            <span class="text-sm text-gray-700 font-medium">Áreas</span>
        </a>
        <a href="{{ route('cargos.index') }}" class="bg-white rounded-lg shadow-sm p-4 text-center hover:shadow-md transition">
            <div class="text-2xl mb-1">🧑‍💼</div>
            <span class="text-sm text-gray-700 font-medium">Cargos</span>
        </a>
        <a href="{{ route('sedes.index') }}" class="bg-white rounded-lg shadow-sm p-4 text-center hover:shadow-md transition">
            <div class="text-2xl mb-1">📍</div>
            <span class="text-sm text-gray-700 font-medium">Sedes</span>
        </a>
        <a href="{{ route('motivos.index') }}" class="bg-white rounded-lg shadow-sm p-4 text-center hover:shadow-md transition">
            <div class="text-2xl mb-1">📋</div>
            <span class="text-sm text-gray-700 font-medium">Motivos</span>
        </a>
        <a href="{{ route('papeletas.index') }}" class="bg-white rounded-lg shadow-sm p-4 text-center hover:shadow-md transition">
            <div class="text-2xl mb-1">📄</div>
            <span class="text-sm text-gray-700 font-medium">Papeletas</span>
        </a>
    </div>
</x-app-layout>
