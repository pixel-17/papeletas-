<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Áreas</h2>
            <a href="{{ route('areas.create') }}"
               class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nueva
            </a>
        </div>
    </x-slot>

    <x-live-refresh-banner tabla="areas" :count="$areas->total()" />

    <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 text-xs uppercase tracking-wide">
                <tr>
                    <th class="p-3">Nombre</th>
                    <th class="p-3 hidden sm:table-cell">Siglas</th>
                    <th class="p-3 hidden sm:table-cell">Cargos</th>
                    <th class="p-3">Estado</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($areas as $area)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-3 font-medium text-gray-800">{{ $area->nombre }}</td>
                        <td class="p-3 hidden sm:table-cell text-gray-500">{{ $area->siglas ?? '—' }}</td>
                        <td class="p-3 hidden sm:table-cell text-gray-500">{{ $area->cargos_count }}</td>
                        <td class="p-3"><x-active-badge :activo="$area->estado" textoActivo="Activa" textoInactivo="Inactiva" /></td>
                        <td class="p-3 text-right space-x-3 whitespace-nowrap">
                            <a href="{{ route('areas.edit', $area) }}" class="text-blue-600 hover:text-blue-800 font-medium">Editar</a>
                            @if($area->estado)
                                <form method="POST" action="{{ route('areas.destroy', $area) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700" onclick="return confirm('¿Desactivar esta área?')">Desactivar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-10 text-center text-gray-400 text-sm">No hay áreas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $areas->links() }}</div>
</x-app-layout>
