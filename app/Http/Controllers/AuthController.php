<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        $this->crearUsuariosDemoSiNoExisten();

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credenciales)) {
            return back()
                ->withErrors(['email' => 'Credenciales invalidas.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        if (!in_array(Auth::user()?->rol, ['admin', 'editor', 'visualizador'], true)) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'El usuario no tiene permisos para ingresar al sistema.'])
                ->onlyInput('email');
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function crearUsuariosDemoSiNoExisten(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@bibliotech.test'],
            [
                'name' => 'Administrador BiblioTech',
                'password' => 'admin123',
                'rol' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'editor@bibliotech.test'],
            [
                'name' => 'Editor BiblioTech',
                'password' => 'editor123',
                'rol' => 'editor',
            ]
        );

        User::firstOrCreate(
            ['email' => 'visualizador@bibliotech.test'],
            [
                'name' => 'Visualizador BiblioTech',
                'password' => 'viewer123',
                'rol' => 'visualizador',
            ]
        );
    }
}
