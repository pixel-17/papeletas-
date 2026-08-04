<?php

namespace App\Http\Controllers;

use App\Actions\AprobarPapeletaAction;
use App\Actions\ObservarPapeletaAction;
use App\Actions\RechazarPapeletaAction;
use App\Enums\TipoObservacion;
use App\Http\Requests\ObservarPapeletaRequest;
use App\Http\Requests\RechazarPapeletaRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Papeleta;

class AprobacionController extends Controller
{
    public function aprobar(Request $request, Papeleta $papeleta, AprobarPapeletaAction $action): RedirectResponse
    {
        $this->authorize('decidir', $papeleta);

        $request->validate(['comentario' => ['nullable', 'string', 'max:1000']]);

        $action->execute($papeleta, $request->user(), $request->input('comentario'));

        return back()->with('status', "Papeleta {$papeleta->codigo} aprobada.");
    }

    public function rechazar(RechazarPapeletaRequest $request, Papeleta $papeleta, RechazarPapeletaAction $action): RedirectResponse
    {
        $action->execute($papeleta, $request->user(), $request->validated('comentario'));

        return back()->with('status', "Papeleta {$papeleta->codigo} rechazada.");
    }

    public function observar(ObservarPapeletaRequest $request, Papeleta $papeleta, ObservarPapeletaAction $action): RedirectResponse
    {
        $action->execute(
            $papeleta,
            $request->user(),
            $request->validated('comentario'),
            TipoObservacion::from($request->validated('tipo'))
        );

        return back()->with('status', "Papeleta {$papeleta->codigo} observada.");
    }
}
