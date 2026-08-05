<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Usuarios</h2>
            <a href="{{ route('users.create') }}" class="bg-blue-600 text-white text-sm px-3 py-2 rounded">+ Nuevo</a>
        </div>
    </x-slot>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500">
                <tr>
                    <th class="p-3">Nombre</th>
                    <th class="p-3 hidden sm:table-cell">Email</th>
                    <th class="p-3">Rol</th>
                    <th class="p-3 hidden sm:table-cell">Jefe</th>
                    <th class="p-3">Estado</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="border-t">
                        <td class="p-3">{{ $user->name }}</td>
                        <td class="p-3 hidden sm:table-cell text-gray-500">{{ $user->email }}</td>
                        <td class="p-3">
                            <span class="text-xs px-2 py-1 rounded bg-blue-50 text-blue-700">
                                {{ $user->getRoleNames()->first() ?? '—' }}
                            </span>
                        </td>
                        <td class="p-3 hidden sm:table-cell text-gray-500">{{ $user->jefe?->name ?? '—' }}</td>
                        <td class="p-3">
                            <span class="text-xs px-2 py-1 rounded {{ $user->estado ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $user->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="p-3 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('users.edit', $user) }}" class="text-blue-600">Editar</a>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600" onclick="return confirm('¿Desactivar este usuario?')">Desactivar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-gray-500">No hay usuarios registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</x-app-layout>
