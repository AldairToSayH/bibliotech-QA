<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Pago;
use App\Models\Prestamo;
use App\Models\User;
use Illuminate\Support\Carbon;

class AdminDashboardController extends Controller
{
    public function __invoke()
    {
        $hoy = Carbon::today();
        $hoyTexto = $hoy->toDateString();

        $totalLibros = Libro::count();
        $librosDisponibles = Libro::where('estado', 'DISPONIBLE')->count();
        $librosPrestados = Libro::where('estado', 'PRESTADO')->count();
        $prestamosActivos = Prestamo::whereIn('estado', ['ACTIVO', 'PRESTADO'])->count();

        $prestamosVencidosQuery = Prestamo::with(['user', 'libro'])
            ->whereDate('fecha_devolucion', '<', $hoyTexto)
            ->whereIn('estado', ['ACTIVO', 'VENCIDO', 'PENALIZADO']);

        $prestamosVencidos = (clone $prestamosVencidosQuery)->count();
        $usuariosMorosos = (clone $prestamosVencidosQuery)->distinct('user_id')->count('user_id');

        $devolucionesProximas = Prestamo::with(['user', 'libro'])
            ->whereIn('estado', ['ACTIVO', 'PRESTADO'])
            ->whereBetween('fecha_devolucion', [$hoyTexto, $hoy->copy()->addDays(7)->toDateString()])
            ->orderBy('fecha_devolucion')
            ->limit(6)
            ->get();

        return view('dashboard', [
            'metricas' => [
                'usuarios' => User::whereIn('rol', ['estudiante', 'docente'])->count(),
                'estudiantes' => User::where('rol', 'estudiante')->count(),
                'docentes' => User::where('rol', 'docente')->count(),
                'cuentasInternas' => User::whereIn('rol', ['admin', 'editor', 'visualizador'])->count(),
                'totalLibros' => $totalLibros,
                'librosDisponibles' => $librosDisponibles,
                'librosPrestados' => $librosPrestados,
                'prestamosActivos' => $prestamosActivos,
                'prestamosVencidos' => $prestamosVencidos,
                'usuariosMorosos' => $usuariosMorosos,
                'pagosRegistrados' => Pago::count(),
                'montoPagado' => (float) Pago::sum('monto'),
                'disponibilidad' => $totalLibros > 0 ? round(($librosDisponibles / $totalLibros) * 100) : 0,
                'ocupacion' => $totalLibros > 0 ? round(($librosPrestados / $totalLibros) * 100) : 0,
            ],
            'prestamosRecientes' => Prestamo::with(['user', 'libro'])->orderByDesc('id')->limit(6)->get(),
            'prestamosVencidos' => (clone $prestamosVencidosQuery)->orderBy('fecha_devolucion')->limit(6)->get(),
            'pagosRecientes' => Pago::with(['user', 'prestamo'])->orderByDesc('id')->limit(6)->get(),
            'devolucionesProximas' => $devolucionesProximas,
            'hoy' => $hoyTexto,
        ]);
    }
}
