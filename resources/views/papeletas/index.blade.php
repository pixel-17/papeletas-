@extends('layouts.app')

@section('titulo', 'Mis Papeletas')

@section('contenido')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold">
            @if(auth()->user()->esJefe()) Bandeja de Aprobación
            @elseif(auth()->user()->esRrhh()) Bandeja RRHH
            @else Mis Papeletas
            @endif
        </h1>

        @if(auth()->user()->esTrabajador() || auth()->user()->esJefe())
            <a href="{{ route('papeletas.create') }}"
               class="bg-blue-600 text-white text-sm px-3 py-2 rounded">
                + Nueva
            </a>
        @endif
    </div>

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
@endsection
