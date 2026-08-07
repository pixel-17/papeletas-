<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 tracking-tight">{{ $papeleta->codigo }}</h2>
    </x-slot>

    <div class="glass-card p-5 mb-4 animate-fade-in-up">
        <div class="flex justify-between items-start mb-3">
            <h1 class="text-lg font-bold text-gray-800">{{ $papeleta->codigo }}</h1>
            <x-status-badge :estado="$papeleta->estado" />
        </div>

        <dl class="text-sm space-y-1.5 text-gray-700">
            <div class="flex justify-between"><dt class="text-gray-500">Trabajador</dt><dd class="font-medium">{{ $papeleta->trabajador->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Destino</dt><dd class="font-medium">{{ $papeleta->destino }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Motivo</dt><dd class="font-medium">{{ $papeleta->motivo->nombre }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Fecha</dt><dd class="font-medium">{{ $papeleta->fecha_salida->format('d/m/Y') }}</dd></div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Horario</dt>
                <dd class="font-medium">{{ $papeleta->hora_salida_programada }} - {{ $papeleta->hora_retorno_programada ?? '—' }}</dd>
            </div>
            @if($papeleta->motivo_detalle)
                <div class="pt-3 border-t border-white/60 mt-2">
                    <dt class="text-gray-500 mb-0.5">Detalle</dt>
                    <dd>{{ $papeleta->motivo_detalle }}</dd>
                </div>
            @endif
        </dl>
    </div>

    @php
        // Camino "feliz" de una papeleta normal. Si el estado actual no está
        // en esta lista (RECHAZADO, OBSERVADO, VENCIDA), el stepper no se
        // muestra — esos casos ya se explican solos con el badge de estado.
        $pasos = [
            \App\Enums\EstadoPapeleta::SOLICITADO->value => 'Solicitado',
            \App\Enums\EstadoPapeleta::APROBADO_JEFE->value => 'Jefe',
            \App\Enums\EstadoPapeleta::APROBADO_RRHH->value => 'RRHH',
            \App\Enums\EstadoPapeleta::EN_CURSO->value => 'En curso',
            \App\Enums\EstadoPapeleta::RETORNO_MARCADO->value => 'Retorno',
            \App\Enums\EstadoPapeleta::FINALIZADO->value => 'Finalizado',
        ];
        $codigoActual = $papeleta->estado->codigo;
        $indiceActual = array_search($codigoActual, array_keys($pasos));
    @endphp

    @if($indiceActual !== false)
        <div class="glass-card p-4 mb-4 animate-fade-in-up overflow-x-auto">
            <div class="flex items-center min-w-max">
                @foreach($pasos as $codigo => $etiqueta)
                    @php $i = $loop->index; @endphp
                    <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                        <div class="flex flex-col items-center gap-1 shrink-0">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold shrink-0 transition-all duration-300
                                        {{ $i < $indiceActual ? 'bg-emerald-500 text-white' : ($i === $indiceActual ? 'bg-brand-500 text-white ring-4 ring-brand-100' : 'bg-gray-200/70 text-gray-400') }}">
                                @if($i < $indiceActual)
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </div>
                            <span class="text-[10px] font-medium whitespace-nowrap {{ $i <= $indiceActual ? 'text-gray-700' : 'text-gray-400' }}">{{ $etiqueta }}</span>
                        </div>
                        @if(!$loop->last)
                            <div class="h-0.5 flex-1 min-w-[1.5rem] mx-1 rounded-full transition-all duration-300 {{ $i < $indiceActual ? 'bg-emerald-400' : 'bg-gray-200/70' }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

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
        <div class="glass-card p-4 mb-4 flex items-center gap-3 animate-fade-in-up">
            <span class="inline-block w-6 h-6 border-2 border-brand-200 border-t-brand-600 rounded-full animate-spin shrink-0"></span>
            <div>
                <p class="text-sm font-semibold text-gray-700">{{ $esperaConfig['mensaje'] }}</p>
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
        <div class="glass-card p-5 mb-4 animate-fade-in-up">
            <h2 class="font-semibold text-sm text-gray-700 mb-2">Documento sustentatorio</h2>
            <p class="text-xs text-gray-400 mb-3">
                @if($pideSustento)
                    Te observaron pidiendo sustento — adjunta un documento para responder.
                @elseif($papeleta->adjuntos->isEmpty())
                    Este motivo requiere adjuntar un documento (solo se admite uno).
                @endif
            </p>

            @foreach($papeleta->adjuntos as $adjunto)
                <div class="flex items-center justify-between text-sm rounded-xl bg-white/50 border border-white/60 p-2.5 mb-2">
                    <a href="{{ route('adjuntos.download', $adjunto) }}" target="_blank" class="text-brand-600 hover:text-brand-800 truncate flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                        {{ $adjunto->nombre_original }}
                    </a>
                    @if($papeleta->trabajador_id === auth()->id())
                        <form method="POST" action="{{ route('adjuntos.destroy', $adjunto) }}"
                              onsubmit="return confirm('¿Eliminar este documento?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-rose-500 hover:text-rose-700 text-xs font-medium">Quitar</button>
                        </form>
                    @endif
                </div>
            @endforeach

            @can('adjuntar', $papeleta)
                <form method="POST" action="{{ route('adjuntos.store', $papeleta) }}"
                      enctype="multipart/form-data" class="space-y-2">
                    @csrf
                    <input type="file" name="archivo" required accept=".pdf,.jpg,.jpeg,.png"
                           class="input-glass text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-brand-100 file:text-brand-700 file:font-medium file:text-xs">
                    <p class="text-xs text-gray-400">PDF, JPG o PNG, máx. 5MB.</p>
                    <button class="btn-secondary w-full justify-center">
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
        <div class="glass-card p-5 mb-4 animate-fade-in-up">
            <h2 class="font-semibold text-sm text-gray-700 mb-3">Observaciones</h2>
            <div class="space-y-3">
                @foreach($papeleta->observaciones as $observacion)
                    <div class="rounded-xl p-3 text-sm border {{ $observacion->atendida ? 'bg-white/40 border-white/60' : 'bg-amber-50/70 border-amber-200/70' }}">
                        <div class="flex justify-between items-start gap-2">
                            <span class="text-xs font-semibold text-gray-500">
                                {{ $observacion->tipo->label() }}
                            </span>
                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-full whitespace-nowrap {{ $observacion->atendida ? 'bg-gray-200/80 text-gray-600' : 'bg-amber-200/80 text-amber-800' }}">
                                {{ $observacion->atendida ? 'Respondida' : 'Pendiente de respuesta' }}
                            </span>
                        </div>
                        <p class="mt-1 text-gray-700">{{ $observacion->comentario }}</p>
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
                                  class="input-glass text-sm" rows="3"></textarea>
                        <button class="btn-primary w-full justify-center">
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
        <div class="glass-card p-5 mb-4 space-y-3 animate-fade-in-up">
            <h2 class="font-semibold text-sm text-gray-700">Acciones</h2>

            <form method="POST" action="{{ route('papeletas.aprobar', $papeleta) }}"
                  x-data="{ confirmado: false }"
                  @submit="if (!confirmado) { $event.preventDefault(); confirmado = true; setTimeout(() => $event.target.submit(), 550); }">
                @csrf
                <button type="submit"
                        class="btn-glass text-white shadow-glass w-full justify-center relative overflow-hidden"
                        style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);"
                        :disabled="confirmado">
                    <span x-show="!confirmado" class="flex items-center gap-2">Aprobar</span>
                    <span x-show="confirmado" x-cloak class="flex items-center gap-2">
                        <svg class="w-5 h-5 animate-scale-in" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        ¡Aprobado!
                    </span>
                </button>
            </form>

            <form method="POST" action="{{ route('papeletas.rechazar', $papeleta) }}" class="space-y-2">
                @csrf
                <textarea name="comentario" required placeholder="Motivo del rechazo"
                          class="input-glass text-sm" rows="2"></textarea>
                <button class="btn-danger w-full justify-center">Rechazar</button>
            </form>

            <form method="POST" action="{{ route('papeletas.observar', $papeleta) }}" class="space-y-2">
                @csrf
                <select name="tipo" required class="input-glass text-sm">
                    <option value="ADMINISTRATIVA">Observación administrativa</option>
                    <option value="JUSTIFICACION">Requiere justificación</option>
                </select>
                <textarea name="comentario" required placeholder="Detalle de la observación"
                          class="input-glass text-sm" rows="2"></textarea>
                <button class="btn-glass text-white shadow-glass w-full justify-center" style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);">Observar</button>
            </form>
        </div>
    @endcan

    @can('confirmarRetorno', $papeleta)
        <div class="glass-card p-5 mb-4 space-y-3 animate-fade-in-up">
            <h2 class="font-semibold text-sm text-gray-700">Confirmar retorno</h2>
            <p class="text-xs text-gray-500">
                {{ $papeleta->trabajador->name }} marcó su retorno (GPS). Confirma para cerrar la papeleta;
                el trabajador recibirá un correo con la constancia completa.
            </p>
            <form method="POST" action="{{ route('papeletas.confirmar-retorno', $papeleta) }}" class="space-y-2"
                  x-data="{ confirmado: false }"
                  @submit="if (!confirmado) { $event.preventDefault(); confirmado = true; setTimeout(() => $event.target.submit(), 550); }">
                @csrf
                <textarea name="comentario" placeholder="Comentario (opcional)"
                          class="input-glass text-sm" rows="2"></textarea>
                <button type="submit"
                        class="btn-glass text-white shadow-glass w-full justify-center"
                        style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);"
                        :disabled="confirmado">
                    <span x-show="!confirmado">Confirmar retorno</span>
                    <span x-show="confirmado" x-cloak class="flex items-center gap-2">
                        <svg class="w-5 h-5 animate-scale-in" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        ¡Confirmado!
                    </span>
                </button>
            </form>
        </div>
    @endcan

    @if($papeleta->marcaciones->isNotEmpty())
        <div class="glass-card p-5 mb-4 animate-fade-in-up">
            <h2 class="font-semibold text-sm text-gray-700 mb-3">Marcación GPS</h2>
            <div class="space-y-2">
                @foreach($papeleta->marcaciones as $marcacion)
                    <div class="rounded-xl bg-white/40 border border-white/60 p-3 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-700">
                                {{ $marcacion->tipo === \App\Enums\TipoMarcacion::SALIDA ? 'Salida' : 'Retorno' }}
                            </span>

                            @if(is_null($marcacion->dentro_radio_permitido))
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-gray-100/80 text-gray-500">
                                    Sede sin coordenadas configuradas
                                </span>
                            @elseif($marcacion->dentro_radio_permitido)
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-emerald-100/80 text-emerald-700">
                                    ✓ Dentro del radio
                                </span>
                            @else
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-rose-100/80 text-rose-700">
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
        <div class="glass-card p-5 mb-4 animate-fade-in-up">
            <h2 class="font-semibold text-sm text-gray-700 mb-3">Marcación</h2>

            @if(!$papeleta->yaMarcoSalida())
                <button onclick="marcar('salida')" class="btn-primary w-full justify-center">
                    Marcar Salida (GPS)
                </button>
            @elseif(!$papeleta->yaMarcoRetorno())
                <button onclick="marcar('retorno')" class="btn-primary w-full justify-center">
                    Marcar Retorno (GPS)
                </button>
            @else
                <p class="text-sm text-gray-500">Marcación completa.</p>
            @endif
            <p id="gps-status" class="text-xs text-gray-400 mt-2"></p>
        </div>
    @endcan

    <div class="glass-card p-5 animate-fade-in-up">
        <h2 class="font-semibold text-sm text-gray-700 mb-2">Historial</h2>
        <ul class="text-xs text-gray-600 space-y-2">
            @foreach($papeleta->historial as $item)
                <li class="border-b border-white/60 pb-2 last:border-0">
                    <span class="font-medium text-gray-700">{{ $item->accion }}</span>
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
