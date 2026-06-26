<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\MorosidadController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PortalAuthController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\PruebaController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\UserAdminController;
use App\Models\Libro;
use App\Models\Pago;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - BiblioTech
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('public.home');
})->name('public.home');

Route::get('/registro', [RegistroController::class, 'index'])->name('registro.index');
Route::post('/registro/validar', [RegistroController::class, 'validar'])->name('registro.validar');
Route::get('/portal/login', [PortalAuthController::class, 'index'])->name('portal.login');
Route::post('/portal/login', [PortalAuthController::class, 'login'])->name('portal.login.attempt');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/portal/logout', function () {
    session()->forget(['portal_user', 'portal_rol']);

    return redirect()->route('public.home');
})->name('portal.logout');

Route::prefix('alumno')->name('alumno.')->group(function (): void {
    Route::get('/dashboard', function () {
        if (session('portal_user.rol') !== 'estudiante') {
            return redirect()->route('registro.index');
        }

        return view('alumno.dashboard', [
            'usuarioPortal' => session('portal_user'),
        ]);
    })->name('dashboard');

    Route::get('/catalogo', function () {
        if (session('portal_user.rol') !== 'estudiante') {
            return redirect()->route('registro.index');
        }

        return view('alumno.catalogo', [
            'libros' => Libro::orderBy('titulo')->get(),
        ]);
    })->name('catalogo');

    Route::get('/estado-cuenta', function () {
        if (session('portal_user.rol') !== 'estudiante') {
            return redirect()->route('registro.index');
        }

        return view('alumno.estado-cuenta', [
            'usuarioPortal' => session('portal_user'),
            'prestamos' => collect(),
            'pagos' => collect(),
        ]);
    })->name('estado-cuenta');
});

Route::prefix('docente')->name('docente.')->group(function (): void {
    Route::get('/dashboard', function () {
        if (session('portal_user.rol') !== 'docente') {
            return redirect()->route('registro.index');
        }

        return view('docente.dashboard', [
            'usuarioPortal' => session('portal_user'),
        ]);
    })->name('dashboard');

    Route::get('/catalogo', function () {
        if (session('portal_user.rol') !== 'docente') {
            return redirect()->route('registro.index');
        }

        return view('docente.catalogo', [
            'libros' => Libro::orderBy('titulo')->get(),
        ]);
    })->name('catalogo');

    Route::get('/estado-cuenta', function () {
        if (session('portal_user.rol') !== 'docente') {
            return redirect()->route('registro.index');
        }

        return view('docente.estado-cuenta', [
            'usuarioPortal' => session('portal_user'),
            'prestamos' => collect(),
            'pagos' => collect(),
        ]);
    })->name('estado-cuenta');

    Route::get('/pagar-deuda', function () {
        if (session('portal_user.rol') !== 'docente') {
            return redirect()->route('registro.index');
        }

        return view('docente.pagar-deuda', [
            'usuarioPortal' => session('portal_user'),
            'pagos' => Pago::orderByDesc('id')->limit(10)->get(),
        ]);
    })->name('pagar-deuda');
});

Route::prefix('admin')->middleware('auth')->group(function (): void {
    Route::get('/dashboard', AdminDashboardController::class)->name('admin.dashboard');

    Route::get('/usuarios', [UserAdminController::class, 'index'])->name('admin.usuarios.index');
    Route::post('/usuarios', [UserAdminController::class, 'store'])->name('admin.usuarios.store');
    Route::put('/usuarios/{usuario}', [UserAdminController::class, 'update'])->name('admin.usuarios.update');
    Route::delete('/usuarios/{usuario}', [UserAdminController::class, 'destroy'])->name('admin.usuarios.destroy');

    Route::get('/libros', [LibroController::class, 'index'])->name('libros.index');
    Route::post('/libros', [LibroController::class, 'store'])->name('libros.store');
    Route::get('/libros/{libro}/editar', [LibroController::class, 'edit'])->name('libros.edit');
    Route::put('/libros/{libro}', [LibroController::class, 'update'])->name('libros.update');
    Route::delete('/libros/{libro}', [LibroController::class, 'destroy'])->name('libros.destroy');

    Route::get('/prestamos', [PrestamoController::class, 'index'])->name('prestamos.index');
    Route::post('/prestamos/calcular-plazo', [PrestamoController::class, 'calcularPlazo'])->name('prestamos.calcular-plazo');
    Route::post('/prestamos/registrar', [PrestamoController::class, 'registrar'])->name('prestamos.registrar');
    Route::get('/prestamos/{prestamo}/editar', [PrestamoController::class, 'edit'])->name('prestamos.edit');
    Route::put('/prestamos/{prestamo}', [PrestamoController::class, 'update'])->name('prestamos.update');
    Route::delete('/prestamos/{prestamo}', [PrestamoController::class, 'destroy'])->name('prestamos.destroy');

    Route::get('/morosidad', [MorosidadController::class, 'index'])->name('morosidad.index');
    Route::post('/morosidad/calcular-multa', [MorosidadController::class, 'calcularMulta'])->name('morosidad.calcular-multa');
    Route::post('/morosidad/calcular-pago', [MorosidadController::class, 'calcularPago'])->name('morosidad.calcular-pago');
    Route::post('/morosidad/calcular-penalizacion', [MorosidadController::class, 'calcularPenalizacion'])->name('morosidad.calcular-penalizacion');

    Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
    Route::post('/pagos/registrar-multa', [PagoController::class, 'registrarMulta'])->name('pagos.registrar-multa');
    Route::post('/pagos/intentar-prestamo', [PagoController::class, 'intentarPrestamo'])->name('pagos.intentar-prestamo');
    Route::get('/pagos/{pago}/editar', [PagoController::class, 'edit'])->name('pagos.edit');
    Route::put('/pagos/{pago}', [PagoController::class, 'update'])->name('pagos.update');
    Route::delete('/pagos/{pago}', [PagoController::class, 'destroy'])->name('pagos.destroy');

    Route::get('/panel-qa', [PruebaController::class, 'index'])->name('pruebas.index');
});

Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))->name('dashboard');
Route::get('/libros', fn () => redirect()->route('libros.index'));
Route::get('/prestamos', fn () => redirect()->route('prestamos.index'));
Route::get('/morosidad', fn () => redirect()->route('morosidad.index'));
Route::get('/pagos', fn () => redirect()->route('pagos.index'));
Route::get('/panel-pruebas', fn () => redirect()->route('pruebas.index'));
Route::get('/administracion/usuarios', fn () => redirect()->route('admin.usuarios.index'));
