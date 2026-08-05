<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cargos</h2>
            <a href="{{ route('cargos.create') }}" class="bg-blue-600 text-white text-sm px-3 py-2 rounded">+ Nuevo</a>
        </div>
    </x-slot>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="p-3">Nombre</th>
                    <th class="p-3">Área</th>
                    <th class="p-3">Estado</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($cargos as $cargo)
                    <tr class="border-t">
                        <td class="p-3">{{ $cargo->nombre }}</td>
                        <td class="p-3 text-gray-500">{{ $cargo->area->nombre }}</td>
                        <td class="p-3">
                            <span class="text-xs px-2 py-1 rounded {{ $cargo->estado ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $cargo->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="p-3 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('cargos.edit', $cargo) }}" class="text-blue-600">Editar</a>
                            @if($cargo->estado)
                                <form method="POST" action="{{ route('cargos.destroy', $cargo) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600" onclick="return confirm('¿Desactivar este cargo?')">Desactivar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-6 text-center text-gray-500">No hay cargos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $cargos->links() }}</div>
</x-app-layout>
