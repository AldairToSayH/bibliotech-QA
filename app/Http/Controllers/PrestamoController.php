<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\User;
use App\Services\PrestamoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PrestamoController extends Controller
{
    public function index()
    {
        return view('prestamos.index', $this->datosVista());
    }

    public function calcularPlazo(Request $request, PrestamoService $prestamoService)
    {
        $datos = $request->validate([
            'rol' => ['required', 'string'],
            'fecha_prestamo' => ['required', 'date'],
        ]);

        $resultado = $prestamoService->calcularPlazoPrestamo(
            $datos['rol'],
            $datos['fecha_prestamo']
        );

        return view('prestamos.index', $this->datosVista([
            'resultadoPlazo' => $resultado,
            'datosPlazo' => $datos,
        ]));
    }

    public function registrar(Request $request, PrestamoService $prestamoService)
    {
        $datos = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'libro_id' => ['required', 'exists:libros,id'],
            'rol' => ['required', 'string'],
            'fecha_prestamo' => ['required', 'date'],
        ]);

        $resultado = $prestamoService->registrarPrestamo(
            (int) $datos['user_id'],
            (int) $datos['libro_id'],
            $datos['rol'],
            $datos['fecha_prestamo']
        );

        return view('prestamos.index', $this->datosVista([
            'resultadoRegistro' => $resultado,
            'datosRegistro' => $datos,
        ]));
    }

    public function edit(Prestamo $prestamo)
    {
        return view('prestamos.edit', [
            'prestamo' => $prestamo,
            'usuarios' => User::whereIn('rol', ['estudiante', 'docente'])->orderBy('name')->get(),
            'libros' => Libro::orderBy('titulo')->get(),
        ]);
    }

    public function update(Request $request, Prestamo $prestamo)
    {
        $datos = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'libro_id' => ['required', 'exists:libros,id'],
            'fecha_prestamo' => ['nullable', 'date'],
            'fecha_devolucion' => ['nullable', 'date'],
            'estado' => ['required', 'string', 'max:255'],
        ]);

        $prestamo->update($datos);

        return redirect()->route('prestamos.index')->with('mensaje', 'Prestamo actualizado correctamente.');
    }

    public function destroy(Prestamo $prestamo)
    {
        $libro = $prestamo->libro;
        $prestamo->delete();

        if ($libro && !$libro->prestamos()->whereIn('estado', ['ACTIVO', 'PRESTADO', 'VENCIDO', 'PENALIZADO'])->exists()) {
            $libro->update(['estado' => 'DISPONIBLE']);
        }

        return redirect()->route('prestamos.index')->with('mensaje', 'Prestamo eliminado correctamente.');
    }

    /**
     * @param array<string, mixed> $extra
     * @return array<string, mixed>
     */
    private function datosVista(array $extra = []): array
    {
        $tablasDisponibles = Schema::hasTable('users') && Schema::hasTable('libros');

        if (!$tablasDisponibles) {
            return array_merge([
                'usuarios' => collect(),
                'libros' => collect(),
                'tablasDisponibles' => false,
            ], $extra);
        }

        if (!User::where('rol', 'estudiante')->exists()) {
            User::create([
                'name' => 'Juan Estudiante',
                'email' => 'juan.demo@bibliotech.test',
                'password' => 'password',
                'rol' => 'estudiante',
            ]);
        }

        if (!User::where('rol', 'docente')->exists()) {
            User::create([
                'name' => 'Ana Docente',
                'email' => 'ana.demo@bibliotech.test',
                'password' => 'password',
                'rol' => 'docente',
            ]);
        }

        if (!Libro::where('estado', 'DISPONIBLE')->exists()) {
            Libro::create([
                'titulo' => 'Programacion en PHP',
                'estado' => 'DISPONIBLE',
            ]);
        }

        if (!Libro::where('estado', 'PRESTADO')->exists()) {
            Libro::create([
                'titulo' => 'Base de Datos con MySQL',
                'estado' => 'PRESTADO',
            ]);
        }

        return array_merge([
            'usuarios' => User::whereIn('rol', ['estudiante', 'docente'])->orderBy('name')->get(),
            'libros' => Libro::orderBy('id')->get(),
            'prestamos' => Prestamo::with(['user', 'libro'])->orderByDesc('id')->get(),
            'tablasDisponibles' => true,
            'mensaje' => session('mensaje'),
        ], $extra);
    }
}
