<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Áreas</h2>
            <a href="{{ route('areas.create') }}" class="bg-blue-600 text-white text-sm px-3 py-2 rounded">+ Nueva</a>
        </div>
    </x-slot>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="p-3">Nombre</th>
                    <th class="p-3 hidden sm:table-cell">Siglas</th>
                    <th class="p-3 hidden sm:table-cell">Cargos</th>
                    <th class="p-3">Estado</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($areas as $area)
                    <tr class="border-t">
                        <td class="p-3">{{ $area->nombre }}</td>
                        <td class="p-3 hidden sm:table-cell text-gray-500">{{ $area->siglas ?? '—' }}</td>
                        <td class="p-3 hidden sm:table-cell text-gray-500">{{ $area->cargos_count }}</td>
                        <td class="p-3">
                            <span class="text-xs px-2 py-1 rounded {{ $area->estado ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $area->estado ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="p-3 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('areas.edit', $area) }}" class="text-blue-600">Editar</a>
                            @if($area->estado)
                                <form method="POST" action="{{ route('areas.destroy', $area) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600" onclick="return confirm('¿Desactivar esta área?')">Desactivar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-gray-500">No hay áreas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $areas->links() }}</div>
</x-app-layout>
