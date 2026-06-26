<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class LibroController extends Controller
{
    public function index()
    {
        return view('libros.index', $this->datosVista());
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'autor' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:255', 'unique:libros,isbn'],
            'estado' => ['required', Rule::in(['DISPONIBLE', 'PRESTADO'])],
        ]);

        Libro::create($datos);

        return view('libros.index', $this->datosVista([
            'mensaje' => 'Libro registrado correctamente.',
        ]));
    }

    public function edit(Libro $libro)
    {
        return view('libros.edit', compact('libro'));
    }

    public function update(Request $request, Libro $libro)
    {
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'autor' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:255', Rule::unique('libros', 'isbn')->ignore($libro->id)],
            'estado' => ['required', Rule::in(['DISPONIBLE', 'PRESTADO'])],
        ]);

        $libro->update($datos);

        return redirect()->route('libros.index')->with('mensaje', 'Libro actualizado correctamente.');
    }

    public function destroy(Libro $libro)
    {
        if ($libro->prestamos()->exists()) {
            return redirect()
                ->route('libros.index')
                ->with('mensaje', 'No se puede eliminar un libro con historial de prestamos.');
        }

        $libro->delete();

        return redirect()->route('libros.index')->with('mensaje', 'Libro eliminado correctamente.');
    }

    /**
     * @param array<string, mixed> $extra
     * @return array<string, mixed>
     */
    private function datosVista(array $extra = []): array
    {
        if (!Schema::hasTable('libros')) {
            return array_merge([
                'libros' => collect(),
                'totalLibros' => 0,
                'disponibles' => 0,
                'prestados' => 0,
                'tablaDisponible' => false,
            ], $extra);
        }

        $libros = Libro::withCount('prestamos')->orderBy('id')->get();

        return array_merge([
            'libros' => $libros,
            'totalLibros' => $libros->count(),
            'disponibles' => $libros->where('estado', 'DISPONIBLE')->count(),
            'prestados' => $libros->where('estado', 'PRESTADO')->count(),
            'tablaDisponible' => true,
            'mensaje' => session('mensaje'),
        ], $extra);
    }
}
