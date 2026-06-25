<?php

namespace App\Http\Controllers;

use App\Services\MorosidadService;
use Illuminate\Http\Request;

class MorosidadController extends Controller
{
    public function index()
    {
        return view('morosidad.index');
    }

    public function calcularMulta(Request $request, MorosidadService $morosidadService)
    {
        $datos = $request->validate([
            'rol' => ['required', 'string'],
            'fecha_devolucion' => ['required', 'date'],
            'fecha_actual' => ['required', 'date'],
        ]);

        $resultado = $morosidadService->calcularMulta(
            $datos['rol'],
            $datos['fecha_devolucion'],
            $datos['fecha_actual']
        );

        return view('morosidad.index', [
            'datosMulta' => $datos,
            'resultadoMulta' => $resultado,
        ]);
    }

    public function calcularPago(Request $request, MorosidadService $morosidadService)
    {
        $datos = $request->validate([
            'rol' => ['required', 'string'],
            'fecha_devolucion' => ['required', 'date'],
            'fecha_pago' => ['required', 'date'],
            'fecha_actual' => ['required', 'date'],
        ]);

        $resultado = $morosidadService->calcularMultaDespuesDePago(
            $datos['rol'],
            $datos['fecha_devolucion'],
            $datos['fecha_pago'],
            $datos['fecha_actual']
        );

        return view('morosidad.index', [
            'datosPago' => $datos,
            'resultadoPago' => $resultado,
        ]);
    }

    public function calcularPenalizacion(Request $request, MorosidadService $morosidadService)
    {
        $datos = $request->validate([
            'fecha_pago' => ['required', 'date'],
            'fecha_actual' => ['required', 'date'],
            'dias_penalizacion' => ['nullable', 'integer', 'min:1'],
        ]);

        $diasPenalizacion = (int) ($datos['dias_penalizacion'] ?? 21);

        $resultado = $morosidadService->calcularPenalizacionDespuesDePago(
            $datos['fecha_pago'],
            $datos['fecha_actual'],
            $diasPenalizacion
        );

        return view('morosidad.index', [
            'datosPenalizacion' => array_merge($datos, [
                'dias_penalizacion' => $diasPenalizacion,
            ]),
            'resultadoPenalizacion' => $resultado,
        ]);
    }
}
