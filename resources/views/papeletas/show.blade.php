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

    @php
        $esperaConfig = match($papeleta->estado->codigo) {
            \App\Enums\EstadoPapeleta::SOLICITADO->value => [
                'mensaje' => 'Esperando aprobación del jefe',
                'desde' => $papeleta->created_at,
            ],
            \App\Enums\EstadoPapeleta::APROBADO_JEFE->value => [
                'mensaje' => 'Esperando aprobación de RRHH',
                'desde' => optional(
                    $papeleta->flujoAprobaciones
                        ->where('rol', 'JEFE')
                        ->where('accion', \App\Enums\AccionFlujo::APROBADO->value)
                        ->last()
                )->created_at ?? $papeleta->created_at,
            ],
            \App\Enums\EstadoPapeleta::RETORNO_MARCADO->value => [
                'mensaje' => 'Esperando que tu jefe confirme el retorno',
                'desde' => optional(
                    $papeleta->marcaciones->firstWhere('tipo', \App\Enums\TipoMarcacion::RETORNO->value)
                )->created_at ?? $papeleta->updated_at,
            ],
            default => null,
        };
    @endphp

    @if($esperaConfig)
        <div class="bg-white rounded shadow p-4 mb-4 flex items-center gap-3">
            <span class="inline-block w-6 h-6 border-2 border-gray-300 border-t-blue-600 rounded-full animate-spin shrink-0"></span>
            <div>
                <p class="text-sm font-medium text-gray-700">{{ $esperaConfig['mensaje'] }}</p>
                <p class="text-xs text-gray-400" id="tiempo-espera"
                   data-desde="{{ $esperaConfig['desde']->toIso8601String() }}">
                    calculando…
                </p>
            </div>
        </div>

        <script>
            (function () {
                const el = document.getElementById('tiempo-espera');
                if (!el) return;

                const desde = new Date(el.dataset.desde);

                function actualizar() {
                    const diffMs = Math.max(0, Date.now() - desde.getTime());
                    const minutosTotales = Math.floor(diffMs / 60000);
                    const horas = Math.floor(minutosTotales / 60);
                    const minutos = minutosTotales % 60;

                    el.textContent = horas > 0
                        ? `Tienes ${horas} h ${minutos} min esperando`
                        : `Tienes ${minutos} min esperando`;
                }

                actualizar();
                setInterval(actualizar, 30000);
            })();
        </script>
    @endif

    @php
        $pideSustento = $papeleta->observaciones
            ->where('atendida', false)
            ->contains(fn ($o) => $o->tipo === \App\Enums\TipoObservacion::JUSTIFICACION);
    @endphp

    @if($papeleta->adjuntos->isNotEmpty() || $papeleta->motivo->requiere_documento || $pideSustento)
        <div class="bg-white rounded shadow p-4 mb-4">
            <h2 class="font-semibold text-sm mb-2">Documento sustentatorio</h2>
            <p class="text-xs text-gray-400 mb-3">
                @if($pideSustento)
                    Te observaron pidiendo sustento — adjunta un documento para responder.
                @elseif($papeleta->adjuntos->isEmpty())
                    Este motivo requiere adjuntar un documento (solo se admite uno).
                @endif
            </p>

            @foreach($papeleta->adjuntos as $adjunto)
                <div class="flex items-center justify-between text-sm border rounded p-2 mb-2">
                    <a href="{{ route('adjuntos.download', $adjunto) }}" target="_blank" class="text-blue-600 truncate">
                        📄 {{ $adjunto->nombre_original }}
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
            @endforeach

            @can('adjuntar', $papeleta)
                <form method="POST" action="{{ route('adjuntos.store', $papeleta) }}"
                      enctype="multipart/form-data" class="space-y-2">
                    @csrf
                    <input type="file" name="archivo" required accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full border rounded p-2 text-sm">
                    <p class="text-xs text-gray-400">PDF, JPG o PNG, máx. 5MB.</p>
                    <button class="bg-gray-800 text-white text-sm px-3 py-2 rounded w-full">
                        {{ $pideSustento ? 'Subir y responder observación' : 'Subir documento' }}
                    </button>
                </form>
            @else
                @if($papeleta->adjuntos->isEmpty())
                    <p class="text-sm text-gray-400">Aún no se adjunta ningún documento.</p>
                @endif
            @endcan
        </div>
    @endif

    @if($papeleta->observaciones->isNotEmpty())
        <div class="bg-white rounded shadow p-4 mb-4">
            <h2 class="font-semibold text-sm mb-3">Observaciones</h2>
            <div class="space-y-3">
                @foreach($papeleta->observaciones as $observacion)
                    <div class="border rounded p-3 text-sm {{ $observacion->atendida ? 'bg-gray-50' : 'bg-orange-50 border-orange-200' }}">
                        <div class="flex justify-between items-start gap-2">
                            <span class="text-xs font-medium text-gray-500">
                                {{ $observacion->tipo->label() }}
                            </span>
                            <span class="text-[11px] px-2 py-0.5 rounded whitespace-nowrap {{ $observacion->atendida ? 'bg-gray-200 text-gray-600' : 'bg-orange-200 text-orange-800' }}">
                                {{ $observacion->atendida ? 'Respondida' : 'Pendiente de respuesta' }}
                            </span>
                        </div>
                        <p class="mt-1">{{ $observacion->comentario }}</p>
                        <p class="text-[11px] text-gray-400 mt-1">
                            {{ $observacion->usuario?->name ?? 'Sistema' }} — {{ $observacion->created_at->diffForHumans() }}
                        </p>
                    </div>
                @endforeach
            </div>

            @can('responderObservacion', $papeleta)
                @if($pideSustento)
                    <p class="text-xs text-gray-400 mt-3">
                        Sube el documento de sustento arriba — al subirlo, esta observación queda respondida automáticamente.
                    </p>
                @else
                    <form method="POST" action="{{ route('papeletas.responder-observacion', $papeleta) }}" class="mt-3 space-y-2">
                        @csrf
                        <textarea name="respuesta" required placeholder="Escribe tu respuesta a la observación..."
                                  class="w-full border rounded p-2 text-sm" rows="3"></textarea>
                        <button class="bg-blue-600 text-white text-sm px-3 py-2 rounded w-full">
                            Enviar respuesta
                        </button>
                        <p class="text-xs text-gray-400">
                            Al responder, tu papeleta vuelve a revisión de quien la observó.
                        </p>
                    </form>
                @endif
            @endcan
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

    @can('confirmarRetorno', $papeleta)
        <div class="bg-white rounded shadow p-4 mb-4 space-y-3">
            <h2 class="font-semibold text-sm">Confirmar retorno</h2>
            <p class="text-xs text-gray-500">
                {{ $papeleta->trabajador->name }} marcó su retorno (GPS). Confirma para cerrar la papeleta;
                el trabajador recibirá un correo con la constancia completa.
            </p>
            <form method="POST" action="{{ route('papeletas.confirmar-retorno', $papeleta) }}" class="space-y-2">
                @csrf
                <textarea name="comentario" placeholder="Comentario (opcional)"
                          class="w-full border rounded p-2 text-sm" rows="2"></textarea>
                <button class="bg-cyan-600 text-white text-sm px-3 py-2 rounded w-full">Confirmar retorno</button>
            </form>
        </div>
    @endcan

    @if($papeleta->marcaciones->isNotEmpty())
        <div class="bg-white rounded shadow p-4 mb-4">
            <h2 class="font-semibold text-sm mb-3">Marcación GPS</h2>
            <div class="space-y-2">
                @foreach($papeleta->marcaciones as $marcacion)
                    <div class="border rounded p-2 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="font-medium">
                                {{ $marcacion->tipo === \App\Enums\TipoMarcacion::SALIDA ? 'Salida' : 'Retorno' }}
                            </span>

                            @if(is_null($marcacion->dentro_radio_permitido))
                                <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-500">
                                    Sede sin coordenadas configuradas
                                </span>
                            @elseif($marcacion->dentro_radio_permitido)
                                <span class="text-xs px-2 py-0.5 rounded bg-green-100 text-green-700">
                                    ✓ Dentro del radio
                                </span>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded bg-red-100 text-red-700">
                                    ⚠ Fuera del radio permitido
                                </span>
                            @endif
                        </div>

                        <p class="text-xs text-gray-500 mt-1">
                            {{ $marcacion->created_at->format('d/m/Y H:i') }}
                            @if($marcacion->direccion) — {{ $marcacion->direccion }} @endif
                        </p>

                        @if($marcacion->precision_gps)
                            <p class="text-[11px] mt-0.5 {{ $marcacion->precision_gps > 100 ? 'text-amber-600' : 'text-gray-400' }}">
                                Precisión del GPS: ±{{ round($marcacion->precision_gps) }} m
                                @if($marcacion->precision_gps > 100) (poco confiable) @endif
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

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
    @php
        // Se arma aparte (fuera del @json) porque pasarle un ternario con un
        // array literal directo a @json rompía el parser de Blade.
        $sedeParaJs = $papeleta->sede ? [
            'lat' => (float) $papeleta->sede->latitud,
            'lng' => (float) $papeleta->sede->longitud,
            'radio' => $papeleta->sede->radio_permitido,
            'nombre' => $papeleta->sede->nombre,
        ] : null;
    @endphp
    <script>
        const sede = @json($sedeParaJs);

        // Misma fórmula haversine que usa el backend (Sede::distanciaHaciaMetros),
        // para poder avisarle al trabajador ANTES de enviar, sin depender de
        // un viaje al servidor.
        function distanciaMetros(lat1, lng1, lat2, lng2) {
            const R = 6371000;
            const toRad = (deg) => deg * Math.PI / 180;
            const dLat = toRad(lat2 - lat1);
            const dLng = toRad(lng2 - lng1);
            const a = Math.sin(dLat / 2) ** 2
                + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng / 2) ** 2;
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        function marcar(tipo) {
            const status = document.getElementById('gps-status');
            status.textContent = 'Obteniendo ubicación...';

            if (!navigator.geolocation) {
                status.textContent = 'Tu navegador no soporta geolocalización.';
                return;
            }

            navigator.geolocation.getCurrentPosition(function (pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;

                // Aviso previo (no bloqueante): si hay coordenadas de sede y
                // el trabajador está claramente fuera del radio, se le
                // pregunta si quiere continuar igual. La marcación siempre
                // queda registrada con el resultado real, lo decida o no.
                if (sede && sede.lat && sede.lng) {
                    const distancia = Math.round(distanciaMetros(lat, lng, sede.lat, sede.lng));
                    if (distancia > sede.radio) {
                        const continuar = confirm(
                            `Estás a ${distancia} m de "${sede.nombre}" ` +
                            `(radio permitido: ${sede.radio} m). ` +
                            `¿Marcar de todas formas?`
                        );
                        if (!continuar) {
                            status.textContent = 'Marcación cancelada.';
                            return;
                        }
                    }
                }

                enviarMarcacion(tipo, lat, lng, pos.coords.accuracy);
            }, function (err) {
                status.textContent = 'No se pudo obtener tu ubicación: ' + err.message;
            }, { enableHighAccuracy: true, timeout: 10000 });
        }

        function enviarMarcacion(tipo, lat, lng, precision) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = tipo === 'salida'
                ? '{{ route('papeletas.marcar-salida', $papeleta) }}'
                : '{{ route('papeletas.marcar-retorno', $papeleta) }}';

            const campos = {
                _token: '{{ csrf_token() }}',
                latitud: lat,
                longitud: lng,
                precision_gps: precision,
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
        }
    </script>
    @endcan
</x-app-layout>
