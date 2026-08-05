<?php

namespace App\Http\Controllers;

use App\Actions\CrearPapeletaAction;
use App\Enums\EstadoPapeleta;
use App\Enums\RolUsuario;
use App\Http\Requests\StorePapeletaRequest;
use App\Models\Estado;
use App\Models\HistorialPapeleta;
use App\Models\Papeleta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PapeletaController extends Controller
{
    /**
     * Bandeja según el rol autenticado:
     * - TRABAJADOR: sus propias papeletas.
     * - JEFE: las que tiene pendientes de decidir.
     * - RRHH: las aprobadas por jefe, pendientes de decisión final.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $papeletas = match (true) {
            $user->hasRole(RolUsuario::JEFE) => Papeleta::pendientesDeJefe($user->id)
                ->with(['trabajador', 'motivo', 'estado'])
                ->latest('fecha_salida')
                ->paginate(15),

            $user->hasRole(RolUsuario::RRHH) => Papeleta::pendientesDeRrhh()
                ->with(['trabajador', 'jefe', 'motivo', 'estado'])
                ->latest('fecha_salida')
                ->paginate(15),

            default => Papeleta::delTrabajador($user->id)
                ->with(['motivo', 'estado'])
                ->paginate(15),
        };

        return view('papeletas.index', compact('papeletas'));
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