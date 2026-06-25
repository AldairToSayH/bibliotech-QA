<?php

namespace Tests\Feature;

use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\User;
use App\Services\MorosidadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MorosidadIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cp18_pago_de_multa_registra_fecha_congela_deuda_e_inicia_penalizacion(): void
    {
        $usuario = User::create([
            'name' => 'Carlos Docente',
            'email' => 'carlos.docente@test.com',
            'password' => 'password',
            'rol' => 'docente',
        ]);

        $libro = Libro::create([
            'titulo' => 'Ingenieria de Software',
            'estado' => 'PRESTADO',
        ]);

        $prestamo = Prestamo::create([
            'user_id' => $usuario->id,
            'libro_id' => $libro->id,
            'fecha_prestamo' => '2026-06-01',
            'fecha_devolucion' => '2026-06-15',
            'estado' => 'VENCIDO',
        ]);

        $servicio = new MorosidadService();

        $resultado = $servicio->registrarPagoDeMulta(
            userId: $usuario->id,
            prestamoId: $prestamo->id,
            rol: 'docente',
            fechaPago: '2026-06-25',
            fechaActual: '2026-06-30',
        );

        $this->assertTrue($resultado['valido']);
        $this->assertSame(50.00, $resultado['multa_pagada']);
        $this->assertSame('2026-07-16', $resultado['fecha_habilitacion']);
        $this->assertFalse($resultado['puede_prestar']);
        $this->assertSame('PENALIZADO', $resultado['estado']);

        $this->assertDatabaseHas('pagos', [
            'id' => $resultado['pago_id'],
            'user_id' => $usuario->id,
            'prestamo_id' => $prestamo->id,
            'monto' => 50.00,
            'fecha_pago' => '2026-06-25',
            'fecha_habilitacion' => '2026-07-16',
            'estado' => 'PENALIZADO',
        ]);

        $this->assertDatabaseHas('prestamos', [
            'id' => $prestamo->id,
            'estado' => 'PENALIZADO',
        ]);
    }
}
