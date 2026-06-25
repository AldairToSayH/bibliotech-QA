<?php

namespace App\Http\Controllers;

use App\Models\Libro;
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
            'usuarios' => User::orderBy('name')->get(),
            'libros' => Libro::orderBy('id')->get(),
            'tablasDisponibles' => true,
        ], $extra);
    }
}
