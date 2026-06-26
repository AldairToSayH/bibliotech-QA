<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
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
        $request->session()->forget(['portal_user', 'portal_rol']);

        if (!in_array(Auth::user()?->rol, ['admin', 'editor', 'visualizador'], true)) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'El usuario no tiene permisos para ingresar al sistema.'])
                ->onlyInput('email');
        }

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget(['portal_user', 'portal_rol']);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

}
