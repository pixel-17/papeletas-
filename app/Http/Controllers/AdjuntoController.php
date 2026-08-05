<?php

namespace App\Http\Controllers;

use App\Models\Adjunto;
use App\Models\Papeleta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class AdjuntoController extends Controller
{
    public function store(Request $request, Papeleta $papeleta): RedirectResponse
    {
        $this->authorize('adjuntar', $papeleta);

        $request->validate([
            'archivo' => ['required', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png'],
        ]);

        $archivo = $request->file('archivo');
        $ruta = $archivo->store("papeletas/{$papeleta->id}", 'local');

        Adjunto::create([
            'papeleta_id' => $papeleta->id,
            'nombre_original' => $archivo->getClientOriginalName(),
            'archivo' => $ruta,
            'extension' => $archivo->getClientOriginalExtension(),
            'peso' => $archivo->getSize(),
        ]);

        return back()->with('status', 'Documento adjuntado.');
    }

    public function download(Adjunto $adjunto): Response
    {
        $this->authorize('ver', $adjunto->papeleta);

        return Storage::disk('local')->download($adjunto->archivo, $adjunto->nombre_original);
    }

    public function destroy(Adjunto $adjunto): RedirectResponse
    {
        $this->authorize('ver', $adjunto->papeleta);

        $adjunto->delete();

        return back()->with('status', 'Documento eliminado.');
    }
}
