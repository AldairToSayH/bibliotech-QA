<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Pago;
use App\Models\Prestamo;
use App\Models\User;
use App\Services\MorosidadService;
use App\Services\PrestamoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PagoController extends Controller
{
    public function index()
    {
        return view('pagos.index', $this->datosVista());
    }

    public function registrarMulta(Request $request, MorosidadService $morosidadService)
    {
        $datos = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'prestamo_id' => ['required', 'exists:prestamos,id'],
            'rol' => ['required', 'string'],
            'fecha_pago' => ['required', 'date'],
            'fecha_actual' => ['required', 'date'],
        ]);

        $resultado = $morosidadService->registrarPagoDeMulta(
            (int) $datos['user_id'],
            (int) $datos['prestamo_id'],
            $datos['rol'],
            $datos['fecha_pago'],
            $datos['fecha_actual']
        );

        return view('pagos.index', $this->datosVista([
            'datosPagoMulta' => $datos,
            'resultadoPagoMulta' => $resultado,
        ]));
    }

    public function intentarPrestamo(Request $request, PrestamoService $prestamoService)
    {
        $datos = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'libro_id' => ['required', 'exists:libros,id'],
            'rol' => ['required', 'string'],
            'fecha_prestamo' => ['required', 'date'],
            'escenario' => ['nullable', 'string'],
        ]);

        $resultado = $prestamoService->registrarPrestamoValidandoPenalizacion(
            (int) $datos['user_id'],
            (int) $datos['libro_id'],
            $datos['rol'],
            $datos['fecha_prestamo']
        );

        return view('pagos.index', $this->datosVista([
            'datosIntentoPrestamo' => $datos,
            'resultadoIntentoPrestamo' => $resultado,
            'escenarioIntentoPrestamo' => $datos['escenario'] ?? 'penalizado',
        ]));
    }

    public function edit(Pago $pago)
    {
        return view('pagos.edit', [
            'pago' => $pago,
            'usuarios' => User::whereIn('rol', ['estudiante', 'docente'])->orderBy('name')->get(),
            'prestamos' => Prestamo::with(['user', 'libro'])->orderByDesc('id')->get(),
        ]);
    }

    public function update(Request $request, Pago $pago)
    {
        $datos = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'prestamo_id' => ['nullable', 'exists:prestamos,id'],
            'monto' => ['required', 'numeric', 'min:0'],
            'fecha_pago' => ['nullable', 'date'],
            'fecha_habilitacion' => ['nullable', 'date'],
            'estado' => ['required', 'string', 'max:255'],
        ]);

        $pago->update($datos);

        return redirect()->route('pagos.index')->with('mensaje', 'Pago actualizado correctamente.');
    }

    public function destroy(Pago $pago)
    {
        $pago->delete();

        return redirect()->route('pagos.index')->with('mensaje', 'Pago eliminado correctamente.');
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
                'usuarios' => collect(),
                'prestamos' => collect(),
                'prestamosVencidos' => collect(),
                'libros' => collect(),
                'librosDisponibles' => collect(),
                'pagos' => collect(),
                'tablasDisponibles' => false,
            ], $extra);
        }

        return array_merge([
            'usuarios' => User::whereIn('rol', ['estudiante', 'docente'])->orderBy('name')->get(),
            'prestamos' => Prestamo::with(['user', 'libro'])->orderByDesc('id')->get(),
            'prestamosVencidos' => Prestamo::with(['user', 'libro'])
                ->whereIn('estado', ['VENCIDO', 'PENALIZADO'])
                ->orderByDesc('id')
                ->get(),
            'libros' => Libro::orderBy('id')->get(),
            'librosDisponibles' => Libro::where('estado', 'DISPONIBLE')->orderBy('id')->get(),
            'pagos' => Pago::with(['user', 'prestamo'])->orderByDesc('id')->get(),
            'tablasDisponibles' => true,
            'mensaje' => session('mensaje'),
        ], $extra);
    }
}
