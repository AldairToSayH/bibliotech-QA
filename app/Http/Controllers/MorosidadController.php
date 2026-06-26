<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Prestamo;
use App\Services\MorosidadService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class MorosidadController extends Controller
{
    public function index()
    {
        return view('morosidad.index', $this->datosVista());
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

        return view('morosidad.index', $this->datosVista([
            'datosMulta' => $datos,
            'resultadoMulta' => $resultado,
        ]));
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

        return view('morosidad.index', $this->datosVista([
            'datosPago' => $datos,
            'resultadoPago' => $resultado,
        ]));
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

        return view('morosidad.index', $this->datosVista([
            'datosPenalizacion' => array_merge($datos, [
                'dias_penalizacion' => $diasPenalizacion,
            ]),
            'resultadoPenalizacion' => $resultado,
        ]));
    }

    /**
     * @param array<string, mixed> $extra
     * @return array<string, mixed>
     */
    private function datosVista(array $extra = []): array
    {
        $tablasDisponibles = Schema::hasTable('users')
            && Schema::hasTable('libros')
            && Schema::hasTable('prestamos')
            && Schema::hasTable('pagos');

        if (!$tablasDisponibles) {
            return array_merge([
                'prestamosVencidos' => collect(),
                'pagos' => collect(),
                'hoy' => Carbon::today()->toDateString(),
                'tablasDisponibles' => false,
            ], $extra);
        }

        $hoy = Carbon::today()->toDateString();

        return array_merge([
            'prestamosVencidos' => Prestamo::with(['user', 'libro'])
                ->whereDate('fecha_devolucion', '<', $hoy)
                ->whereIn('estado', ['ACTIVO', 'VENCIDO', 'PENALIZADO'])
                ->orderBy('fecha_devolucion')
                ->get(),
            'pagos' => Pago::with(['user', 'prestamo'])->orderByDesc('id')->get(),
            'hoy' => $hoy,
            'tablasDisponibles' => true,
        ], $extra);
    }

}
