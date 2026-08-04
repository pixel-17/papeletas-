<?php

namespace App\Http\Controllers;

use App\Actions\MarcarRetornoAction;
use App\Actions\MarcarSalidaAction;
use App\Http\Requests\MarcarGpsRequest;
use App\Models\Papeleta;
use Illuminate\Http\RedirectResponse;

class MarcacionController extends Controller
{
    public function salida(MarcarGpsRequest $request, Papeleta $papeleta, MarcarSalidaAction $action): RedirectResponse
    {
        $action->execute($papeleta, $request->user(), $request->validated());

        return back()->with('status', 'Salida marcada correctamente.');
    }

    public function retorno(MarcarGpsRequest $request, Papeleta $papeleta, MarcarRetornoAction $action): RedirectResponse
    {
        $action->execute($papeleta, $request->user(), $request->validated());

        return back()->with('status', 'Retorno marcado. Papeleta finalizada.');
    }
}
