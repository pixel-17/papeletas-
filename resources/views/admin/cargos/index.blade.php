<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cargos</h2>
            <a href="{{ route('cargos.create') }}"
               class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nuevo
            </a>
        </div>
    </x-slot>

    <x-live-refresh-banner tabla="cargos" :count="$cargos->total()" />

    <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 text-xs uppercase tracking-wide">
                <tr>
                    <th class="p-3">Nombre</th>
                    <th class="p-3">Área</th>
                    <th class="p-3">Estado</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($cargos as $cargo)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-3 font-medium text-gray-800">{{ $cargo->nombre }}</td>
                        <td class="p-3 text-gray-500">{{ $cargo->area->nombre }}</td>
                        <td class="p-3"><x-active-badge :activo="$cargo->estado" /></td>
                        <td class="p-3 text-right space-x-3 whitespace-nowrap">
                            <a href="{{ route('cargos.edit', $cargo) }}" class="text-blue-600 hover:text-blue-800 font-medium">Editar</a>
                            @if($cargo->estado)
                                <form method="POST" action="{{ route('cargos.destroy', $cargo) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700" onclick="return confirm('¿Desactivar este cargo?')">Desactivar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-10 text-center text-gray-400 text-sm">No hay cargos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $cargos->links() }}</div>
</x-app-layout>
