<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserAdminController extends Controller
{
    public function index()
    {
        return view('admin.usuarios.index', [
            'usuarios' => User::with(['prestamos', 'pagos'])
                ->orderByDesc('id')
                ->get(),
            'mensaje' => session('mensaje'),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'rol' => ['required', Rule::in(['admin', 'editor', 'visualizador'])],
        ]);

        User::create($datos);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('mensaje', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $usuario)
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuario->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'rol' => ['required', Rule::in(['admin', 'editor', 'visualizador'])],
        ]);

        if (Auth::id() === $usuario->id && $datos['rol'] !== 'admin') {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('mensaje', 'No puede quitarse su propio rol de administrador.');
        }

        if (empty($datos['password'])) {
            unset($datos['password']);
        }

        $usuario->update($datos);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('mensaje', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        if (Auth::id() === $usuario->id) {
            return redirect()
                ->route('admin.usuarios.index')
                ->with('mensaje', 'No puede eliminar su propio usuario administrador.');
        }

        $usuario->delete();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('mensaje', 'Usuario eliminado correctamente.');
    }
}
