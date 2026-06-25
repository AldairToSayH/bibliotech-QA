<?php

namespace Tests\Unit;

use App\Services\PrestamoService;
use PHPUnit\Framework\TestCase;

class PrestamoServiceTest extends TestCase
{
    public function test_cp06_estudiante_recibe_prestamo_por_7_dias(): void
    {
        $servicio = new PrestamoService();

        $resultado = $servicio->calcularPlazoPrestamo(
            rol: 'estudiante',
            fechaPrestamo: '2026-06-25',
        );

        $this->assertTrue($resultado['valido']);
        $this->assertSame(7, $resultado['dias_prestamo']);
        $this->assertSame('2026-07-02', $resultado['fecha_devolucion']);
        $this->assertSame('estudiante', $resultado['rol']);
    }

    public function test_cp07_docente_recibe_prestamo_por_14_dias(): void
    {
        $servicio = new PrestamoService();

        $resultado = $servicio->calcularPlazoPrestamo(
            rol: 'docente',
            fechaPrestamo: '2026-06-25',
        );

        $this->assertTrue($resultado['valido']);
        $this->assertSame(14, $resultado['dias_prestamo']);
        $this->assertSame('2026-07-09', $resultado['fecha_devolucion']);
        $this->assertSame('docente', $resultado['rol']);
    }

    public function test_cp08_rechaza_prestamo_para_rol_invalido(): void
    {
        $servicio = new PrestamoService();

        $resultado = $servicio->calcularPlazoPrestamo(
            rol: 'invitado',
            fechaPrestamo: '2026-06-25',
        );

        $this->assertFalse($resultado['valido']);
        $this->assertNull($resultado['rol']);
        $this->assertSame(0, $resultado['dias_prestamo']);
        $this->assertNull($resultado['fecha_devolucion']);
        $this->assertStringContainsString('Rol no valido', $resultado['mensaje']);
    }
}
