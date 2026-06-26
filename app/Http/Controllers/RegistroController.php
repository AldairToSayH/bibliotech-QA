<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\RegistroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class RegistroController extends Controller
{
    public function index()
    {
        return view('registro.index', $this->datosVista());
    }

    public function validar(Request $request, RegistroService $registroService)
    {
        $datos = $request->validate([
            'nombres' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'dni' => ['required', 'string', 'unique:users,dni'],
            'codigo_institucional' => ['required', 'string', 'unique:users,codigo_institucion'],
            'codigo_gafete' => ['required', 'string'],
        ]);

        $resultado = $registroService->validarRegistro(
            $datos['dni'],
            $datos['codigo_institucional'],
            $datos['codigo_gafete']
        );

        if ($resultado['valido']) {
            $usuario = User::create([
                'name' => $datos['nombres'],
                'email' => $datos['email'],
                'telefono' => $datos['telefono'] ?? null,
                'dni' => $datos['dni'],
                'codigo_institucion' => $datos['codigo_institucional'],
                'rol' => $resultado['rol'],
                'password' => 'password',
            ]);

            $request->session()->put('portal_user', [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'dni' => $usuario->dni,
                'codigo_institucional' => $usuario->codigo_institucion,
                'rol' => $usuario->rol,
            ]);

            $request->session()->put('portal_rol', $usuario->rol);

            return redirect()->route($usuario->rol === 'docente' ? 'docente.dashboard' : 'alumno.dashboard');
        }

        return view('registro.index', $this->datosVista([
            'datos' => $datos,
            'resultado' => $resultado,
        ]));
    }

    /**
     * @param array<string, mixed> $extra
     * @return array<string, mixed>
     */
    private function datosVista(array $extra = []): array
    {
        if (!Schema::hasTable('users')) {
            return array_merge([
                'usuarios' => collect(),
                'tablaDisponible' => false,
                'mensaje' => session('mensaje'),
            ], $extra);
        }

        return array_merge([
            'usuarios' => User::with(['prestamos', 'pagos'])
                ->whereIn('rol', ['estudiante', 'docente'])
                ->orderByDesc('id')
                ->get(),
            'tablaDisponible' => true,
            'mensaje' => session('mensaje'),
        ], $extra);
    }
}
