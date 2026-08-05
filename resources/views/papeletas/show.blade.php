<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $papeleta->codigo }}</h2>
    </x-slot>

    <div class="bg-white rounded shadow p-4 mb-4">
        <div class="flex justify-between items-start mb-3">
            <h1 class="text-lg font-bold">{{ $papeleta->codigo }}</h1>
            <span class="text-xs px-2 py-1 rounded whitespace-nowrap"
                  style="background-color: {{ $papeleta->estado->color }}20; color: {{ $papeleta->estado->color }};">
                {{ $papeleta->estado->nombre }}
            </span>
        </div>

        <dl class="text-sm space-y-1 text-gray-700">
            <div class="flex justify-between"><dt>Trabajador</dt><dd>{{ $papeleta->trabajador->name }}</dd></div>
            <div class="flex justify-between"><dt>Destino</dt><dd>{{ $papeleta->destino }}</dd></div>
            <div class="flex justify-between"><dt>Motivo</dt><dd>{{ $papeleta->motivo->nombre }}</dd></div>
            <div class="flex justify-between"><dt>Fecha</dt><dd>{{ $papeleta->fecha_salida->format('d/m/Y') }}</dd></div>
            <div class="flex justify-between">
                <dt>Horario</dt>
                <dd>{{ $papeleta->hora_salida_programada }} - {{ $papeleta->hora_retorno_programada ?? '—' }}</dd>
            </div>
            @if($papeleta->motivo_detalle)
                <div class="pt-2 border-t mt-2">
                    <dt class="text-gray-500">Detalle</dt>
                    <dd>{{ $papeleta->motivo_detalle }}</dd>
                </div>
            @endif
        </dl>
    </div>

    @if($papeleta->motivo->requiere_documento)
        <div class="bg-white rounded shadow p-4 mb-4">
            <h2 class="font-semibold text-sm mb-2">Documento sustentatorio</h2>
            <p class="text-xs text-gray-400 mb-3">Este motivo requiere adjuntar un documento (solo se admite uno).</p>

            @forelse($papeleta->adjuntos as $adjunto)
                <div class="flex items-center justify-between text-sm border rounded p-2">
                    <a href="{{ route('adjuntos.download', $adjunto) }}" class="text-blue-600 truncate">
                        {{ $adjunto->nombre_original }}
                    </a>
                    @if($papeleta->trabajador_id === auth()->id())
                        <form method="POST" action="{{ route('adjuntos.destroy', $adjunto) }}"
                              onsubmit="return confirm('¿Eliminar este documento?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 text-xs">Quitar</button>
                        </form>
                    @endif
                </div>
            @empty
                @can('adjuntar', $papeleta)
                    <form method="POST" action="{{ route('adjuntos.store', $papeleta) }}"
                          enctype="multipart/form-data" class="space-y-2">
                        @csrf
                        <input type="file" name="archivo" required accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full border rounded p-2 text-sm">
                        <p class="text-xs text-gray-400">PDF, JPG o PNG, máx. 5MB.</p>
                        <button class="bg-gray-800 text-white text-sm px-3 py-2 rounded w-full">
                            Subir documento
                        </button>
                    </form>
                @else
                    <p class="text-sm text-gray-400">Aún no se adjunta ningún documento.</p>
                @endcan
            @endforelse
        </div>
    @endif

    @can('decidir', $papeleta)
        <div class="bg-white rounded shadow p-4 mb-4 space-y-3">
            <h2 class="font-semibold text-sm">Acciones</h2>

            <form method="POST" action="{{ route('papeletas.aprobar', $papeleta) }}">
                @csrf
                <button class="bg-green-600 text-white text-sm px-3 py-2 rounded w-full">Aprobar</button>
            </form>

            <form method="POST" action="{{ route('papeletas.rechazar', $papeleta) }}" class="space-y-2">
                @csrf
                <textarea name="comentario" required placeholder="Motivo del rechazo"
                          class="w-full border rounded p-2 text-sm" rows="2"></textarea>
                <button class="bg-red-600 text-white text-sm px-3 py-2 rounded w-full">Rechazar</button>
            </form>

            <form method="POST" action="{{ route('papeletas.observar', $papeleta) }}" class="space-y-2">
                @csrf
                <select name="tipo" required class="w-full border rounded p-2 text-sm">
                    <option value="ADMINISTRATIVA">Observación administrativa</option>
                    <option value="JUSTIFICACION">Requiere justificación</option>
                </select>
                <textarea name="comentario" required placeholder="Detalle de la observación"
                          class="w-full border rounded p-2 text-sm" rows="2"></textarea>
                <button class="bg-orange-500 text-white text-sm px-3 py-2 rounded w-full">Observar</button>
            </form>
        </div>
    @endcan

    @can('marcar', $papeleta)
        <div class="bg-white rounded shadow p-4 mb-4">
            <h2 class="font-semibold text-sm mb-3">Marcación</h2>

            @if(!$papeleta->yaMarcoSalida())
                <button onclick="marcar('salida')" class="bg-blue-600 text-white text-sm px-3 py-2 rounded w-full">
                    Marcar Salida (GPS)
                </button>
            @elseif(!$papeleta->yaMarcoRetorno())
                <button onclick="marcar('retorno')" class="bg-blue-600 text-white text-sm px-3 py-2 rounded w-full">
                    Marcar Retorno (GPS)
                </button>
            @else
                <p class="text-sm text-gray-500">Marcación completa.</p>
            @endif
            <p id="gps-status" class="text-xs text-gray-400 mt-2"></p>
        </div>
    @endcan

    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold text-sm mb-2">Historial</h2>
        <ul class="text-xs text-gray-600 space-y-2">
            @foreach($papeleta->historial as $item)
                <li class="border-b pb-2 last:border-0">
                    <span class="font-medium">{{ $item->accion }}</span>
                    — {{ $item->usuario?->name ?? 'Sistema' }}
                    <span class="text-gray-400">({{ $item->created_at->diffForHumans() }})</span>
                </li>
            @endforeach
        </ul>
    </div>

    @can('marcar', $papeleta)
    <script>
        function marcar(tipo) {
            const status = document.getElementById('gps-status');
            status.textContent = 'Obteniendo ubicación...';

            if (!navigator.geolocation) {
                status.textContent = 'Tu navegador no soporta geolocalización.';
                return;
            }

            navigator.geolocation.getCurrentPosition(function (pos) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = tipo === 'salida'
                    ? '{{ route('papeletas.marcar-salida', $papeleta) }}'
                    : '{{ route('papeletas.marcar-retorno', $papeleta) }}';

                const campos = {
                    _token: '{{ csrf_token() }}',
                    latitud: pos.coords.latitude,
                    longitud: pos.coords.longitude,
                    precision_gps: pos.coords.accuracy,
                };

                for (const [nombre, valor] of Object.entries(campos)) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = nombre;
                    input.value = valor;
                    form.appendChild(input);
                }

                document.body.appendChild(form);
                form.submit();
            }, function (err) {
                status.textContent = 'No se pudo obtener tu ubicación: ' + err.message;
            }, { enableHighAccuracy: true, timeout: 10000 });
        }
    </script>
    @endcan
</x-app-layout>
