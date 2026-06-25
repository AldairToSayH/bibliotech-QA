<?php

namespace App\Http\Controllers;

use App\Services\RegistroService;
use Illuminate\Http\Request;

class RegistroController extends Controller
{
    public function index()
    {
        return view('registro.index');
    }

    public function validar(Request $request, RegistroService $registroService)
    {
        $datos = $request->validate([
            'nombres' => ['required', 'string'],
            'dni' => ['required', 'string'],
            'codigo_institucional' => ['required', 'string'],
            'codigo_gafete' => ['required', 'string'],
        ]);

        $resultado = $registroService->validarRegistro(
            $datos['dni'],
            $datos['codigo_institucional'],
            $datos['codigo_gafete']
        );

        return view('registro.index', [
            'datos' => $datos,
            'resultado' => $resultado,
        ]);
    }
}
