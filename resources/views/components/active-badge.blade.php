@props(['activo', 'textoActivo' => 'Activo', 'textoInactivo' => 'Inactivo'])

<span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full {{ $activo ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $activo ? 'bg-green-500' : 'bg-gray-400' }}"></span>
    {{ $activo ? $textoActivo : $textoInactivo }}
</span>
