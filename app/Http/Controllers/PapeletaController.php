<?php

namespace App\Http\Controllers;

use App\Actions\CrearPapeletaAction;
use App\Enums\EstadoPapeleta;
use App\Enums\RolUsuario;
use App\Http\Requests\StorePapeletaRequest;
use App\Models\Area;
use App\Models\Estado;
use App\Models\HistorialPapeleta;
use App\Models\Papeleta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PapeletaController extends Controller
{
    /**
     * Bandeja según el rol autenticado:
     * - TRABAJADOR: siempre sus propias papeletas (todos los estados).
     * - JEFE / RRHH: por defecto solo lo pendiente de su decisión; con
     *   ?vista=todas ven también lo ya resuelto (rechazadas, observadas,
     *   finalizadas) — su bandeja histórica completa.
     *
     * Sobre cada bandeja se aplican los filtros opcionales de la barra de
     * búsqueda (texto, estado, área, rango de fechas) vía Papeleta::conFiltros().
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $filtros = $request->only(['buscar', 'estado_id', 'area_id', 'desde', 'hasta']);
        $vista = $request->get('vista', 'pendientes') === 'todas' ? 'todas' : 'pendientes';

        $papeletas = match (true) {
            $user->hasRole(RolUsuario::JEFE) => ($vista === 'todas'
                    ? Papeleta::deSuEquipo($user->id)
                    : Papeleta::pendientesDeJefe($user->id))
                ->conFiltros($filtros)
                ->with(['trabajador', 'motivo', 'estado'])
                ->latest('fecha_salida')
                ->paginate(15)
                ->withQueryString(),

            $user->hasRole(RolUsuario::RRHH) => ($vista === 'todas'
                    ? Papeleta::query()
                    : Papeleta::pendientesDeRrhh())
                ->conFiltros($filtros)
                ->with(['trabajador', 'jefe', 'motivo', 'estado'])
                ->latest('fecha_salida')
                ->paginate(15)
                ->withQueryString(),

            default => Papeleta::delTrabajador($user->id)
                ->conFiltros($filtros)
                ->with(['motivo', 'estado'])
                ->paginate(15)
                ->withQueryString(),
        };

        // El filtro por área solo tiene sentido para quien ve papeletas de
        // más de un trabajador (Jefe/RRHH); al trabajador no se le ofrece.
        $areas = ($user->esJefe() || $user->esRrhh()) ? Area::activas()->orderBy('nombre')->get() : collect();
        $estados = Estado::orderBy('orden')->get();

        return view('papeletas.index', compact('papeletas', 'filtros', 'areas', 'estados', 'vista'));
    }

    /**
     * Exportación a Excel (.xlsx) del historial de papeletas, con los mismos
     * filtros y vista (pendientes/todas) que la bandeja. Exclusivo para RRHH
     * (ver ruta en routes/web.php).
     *
     * Usa PhpSpreadsheet (agregado a composer.json). Como este sandbox no
     * tiene acceso a Packagist, no pude instalarlo aquí para probarlo —
     * corre `composer update` en tu entorno para descargarlo.
     */
    public function exportar(Request $request): StreamedResponse
    {
        $filtros = $request->only(['buscar', 'estado_id', 'area_id', 'desde', 'hasta']);
        $vista = $request->get('vista', 'todas') === 'pendientes' ? 'pendientes' : 'todas';

        $base = match (true) {
            $vista === 'pendientes' && $request->user()->hasRole(RolUsuario::RRHH) => Papeleta::pendientesDeRrhh(),
            default => Papeleta::query(),
        };

        $papeletas = $base
            ->conFiltros($filtros)
            ->with(['trabajador', 'jefe', 'area', 'sede', 'motivo', 'estado'])
            ->latest('fecha_salida')
            ->get();

        $encabezados = [
            'Código', 'Trabajador', 'Jefe', 'Área', 'Sede', 'Motivo',
            'Destino', 'Fecha salida', 'Hora salida', 'Hora retorno', 'Estado',
        ];

        $spreadsheet = new Spreadsheet;
        $hoja = $spreadsheet->getActiveSheet();
        $hoja->setTitle('Papeletas');
        $hoja->fromArray($encabezados, null, 'A1');
        $hoja->getStyle('A1:K1')->getFont()->setBold(true);

        $fila = 2;
        foreach ($papeletas as $papeleta) {
            $hoja->fromArray([
                $papeleta->codigo,
                $papeleta->trabajador->name,
                $papeleta->jefe?->name,
                $papeleta->area?->nombre,
                $papeleta->sede?->nombre,
                $papeleta->motivo->nombre,
                $papeleta->destino,
                $papeleta->fecha_salida->format('d/m/Y'),
                $papeleta->hora_salida_programada,
                $papeleta->hora_retorno_programada,
                $papeleta->estado->nombre,
            ], null, "A{$fila}");
            $fila++;
        }

        foreach (range('A', 'K') as $columna) {
            $hoja->getColumnDimension($columna)->setAutoSize(true);
        }

        $nombreArchivo = 'papeletas_'.now()->format('Y-m-d_His').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $nombreArchivo, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function create(): View
    {
        $this->authorize('crear', Papeleta::class);

        return view('papeletas.create');
    }

    public function store(StorePapeletaRequest $request, CrearPapeletaAction $action): RedirectResponse
    {
        $papeleta = $action->execute($request->user(), $request->validated());

        return redirect()
            ->route('papeletas.show', $papeleta)
            ->with('status', "Papeleta {$papeleta->codigo} creada y enviada a tu jefe.");
    }

    public function show(Papeleta $papeleta): View
    {
        $this->authorize('ver', $papeleta);

        $papeleta->load([
            'trabajador', 'jefe', 'area', 'sede', 'motivo', 'estado',
            'marcaciones', 'flujoAprobaciones.usuario', 'observaciones.usuario',
            'adjuntos', 'historial.usuario',
        ]);

        return view('papeletas.show', compact('papeleta'));
    }

    /**
     * Anulación: solo el propio trabajador y solo mientras sigue en SOLICITADO
     * (ver PapeletaPolicy::anular). Es soft-delete, nunca hard-delete.
     */
    public function anular(Request $request, Papeleta $papeleta): RedirectResponse
    {
        $this->authorize('anular', $papeleta);

        $estadoAnterior = $papeleta->estado->codigo;

        $papeleta->update(['estado_id' => Estado::porCodigo(EstadoPapeleta::RECHAZADO)->id]);
        $papeleta->delete();

        HistorialPapeleta::registrar(
            $papeleta, $request->user(), 'ANULADA', $estadoAnterior, EstadoPapeleta::RECHAZADO->value,
            'Anulada por el propio trabajador'
        );

        return redirect()->route('papeletas.index')->with('status', 'Papeleta anulada.');
    }
}