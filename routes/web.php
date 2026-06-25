<?php

use App\Http\Controllers\RegistroController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\MorosidadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - BiblioTech
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/registro', [RegistroController::class, 'index'])->name('registro.index');
Route::post('/registro/validar', [RegistroController::class, 'validar'])->name('registro.validar');
Route::view('/libros', 'libros.index')->name('libros.index');
Route::get('/prestamos', [PrestamoController::class, 'index'])->name('prestamos.index');
Route::post('/prestamos/calcular-plazo', [PrestamoController::class, 'calcularPlazo'])->name('prestamos.calcular-plazo');
Route::post('/prestamos/registrar', [PrestamoController::class, 'registrar'])->name('prestamos.registrar');
Route::get('/morosidad', [MorosidadController::class, 'index'])->name('morosidad.index');
Route::post('/morosidad/calcular-multa', [MorosidadController::class, 'calcularMulta'])->name('morosidad.calcular-multa');
Route::post('/morosidad/calcular-pago', [MorosidadController::class, 'calcularPago'])->name('morosidad.calcular-pago');
Route::post('/morosidad/calcular-penalizacion', [MorosidadController::class, 'calcularPenalizacion'])->name('morosidad.calcular-penalizacion');
Route::view('/pagos', 'pagos.index')->name('pagos.index');
Route::view('/panel-pruebas', 'pruebas.index')->name('pruebas.index');
