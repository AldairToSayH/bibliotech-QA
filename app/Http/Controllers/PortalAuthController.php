<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PortalAuthController extends Controller
{
    public function index()
    {
        return view('public.login');
    }

    public function login(Request $request)
    {
        $datos = $request->validate([
            'email' => ['required', 'email'],
            'codigo_institucional' => ['required', 'string'],
        ]);

        $usuario = User::where('email', $datos['email'])
            ->where('codigo_institucion', $datos['codigo_institucional'])
            ->whereIn('rol', ['estudiante', 'docente'])
            ->first();

        if (!$usuario) {
            return back()
                ->withErrors(['email' => 'No se encontro una cuenta de alumno/docente con esos datos.'])
                ->onlyInput('email');
        }

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
}
