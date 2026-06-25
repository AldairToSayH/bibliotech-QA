<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\MorosidadController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PruebaController;
use App\Http\Controllers\UserAdminController;
use App\Models\Libro;
use App\Models\Pago;
use App\Models\Prestamo;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - BiblioTech
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function (): void {
    Route::get('/dashboard', function () {
        return view('dashboard', [
            'totalLibros' => Libro::count(),
            'librosDisponibles' => Libro::where('estado', 'DISPONIBLE')->count(),
            'prestamosActivos' => Prestamo::whereIn('estado', ['ACTIVO', 'PRESTADO'])->count(),
            'pagosRegistrados' => Pago::count(),
            'usuarios' => User::count(),
        ]);
    })->name('dashboard');

    Route::get('/registro', [RegistroController::class, 'index'])->name('registro.index');
    Route::get('/libros', [LibroController::class, 'index'])->name('libros.index');
    Route::get('/prestamos', [PrestamoController::class, 'index'])->name('prestamos.index');
    Route::get('/morosidad', [MorosidadController::class, 'index'])->name('morosidad.index');
    Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
    Route::get('/panel-pruebas', [PruebaController::class, 'index'])->name('pruebas.index');

    Route::middleware(['editor'])->group(function (): void {
        Route::post('/registro/validar', [RegistroController::class, 'validar'])->name('registro.validar');
        Route::post('/libros', [LibroController::class, 'store'])->name('libros.store');
        Route::get('/libros/{libro}/editar', [LibroController::class, 'edit'])->name('libros.edit');
        Route::put('/libros/{libro}', [LibroController::class, 'update'])->name('libros.update');
        Route::delete('/libros/{libro}', [LibroController::class, 'destroy'])->name('libros.destroy');
        Route::post('/prestamos/calcular-plazo', [PrestamoController::class, 'calcularPlazo'])->name('prestamos.calcular-plazo');
        Route::post('/prestamos/registrar', [PrestamoController::class, 'registrar'])->name('prestamos.registrar');
        Route::get('/prestamos/{prestamo}/editar', [PrestamoController::class, 'edit'])->name('prestamos.edit');
        Route::put('/prestamos/{prestamo}', [PrestamoController::class, 'update'])->name('prestamos.update');
        Route::delete('/prestamos/{prestamo}', [PrestamoController::class, 'destroy'])->name('prestamos.destroy');
        Route::post('/morosidad/calcular-multa', [MorosidadController::class, 'calcularMulta'])->name('morosidad.calcular-multa');
        Route::post('/morosidad/calcular-pago', [MorosidadController::class, 'calcularPago'])->name('morosidad.calcular-pago');
        Route::post('/morosidad/calcular-penalizacion', [MorosidadController::class, 'calcularPenalizacion'])->name('morosidad.calcular-penalizacion');
        Route::post('/pagos/registrar-multa', [PagoController::class, 'registrarMulta'])->name('pagos.registrar-multa');
        Route::post('/pagos/intentar-prestamo', [PagoController::class, 'intentarPrestamo'])->name('pagos.intentar-prestamo');
        Route::get('/pagos/{pago}/editar', [PagoController::class, 'edit'])->name('pagos.edit');
        Route::put('/pagos/{pago}', [PagoController::class, 'update'])->name('pagos.update');
        Route::delete('/pagos/{pago}', [PagoController::class, 'destroy'])->name('pagos.destroy');
    });

    Route::middleware(['admin'])->group(function (): void {
        Route::get('/administracion/usuarios', [UserAdminController::class, 'index'])->name('admin.usuarios.index');
        Route::post('/administracion/usuarios', [UserAdminController::class, 'store'])->name('admin.usuarios.store');
        Route::put('/administracion/usuarios/{usuario}', [UserAdminController::class, 'update'])->name('admin.usuarios.update');
        Route::delete('/administracion/usuarios/{usuario}', [UserAdminController::class, 'destroy'])->name('admin.usuarios.destroy');
    });
});
