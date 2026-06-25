<?php

namespace Tests\Unit;

use App\Services\MorosidadService;
use PHPUnit\Framework\TestCase;

class MorosidadServiceTest extends TestCase
{
    public function test_cp11_calcula_multa_acumulada_para_docente_con_10_dias_de_retraso(): void
    {
        $servicio = new MorosidadService();

        $resultado = $servicio->calcularMulta(
            rol: 'docente',
            fechaDevolucion: '2026-06-15',
            fechaActual: '2026-06-25',
        );

        $this->assertTrue($resultado['valido']);
        $this->assertSame(10, $resultado['dias_retraso']);
        $this->assertSame(5.00, $resultado['tarifa_diaria']);
        $this->assertSame(50.00, $resultado['multa_total']);
        $this->assertSame('MOROSO', $resultado['estado']);
    }

    public function test_cp12_calcula_multa_acumulada_para_estudiante_con_10_dias_de_retraso(): void
    {
        $servicio = new MorosidadService();

        $resultado = $servicio->calcularMulta(
            rol: 'estudiante',
            fechaDevolucion: '2026-06-15',
            fechaActual: '2026-06-25',
        );

        $this->assertTrue($resultado['valido']);
        $this->assertSame('estudiante', $resultado['rol']);
        $this->assertSame(10, $resultado['dias_retraso']);
        $this->assertSame(2.00, $resultado['tarifa_diaria']);
        $this->assertSame(20.00, $resultado['multa_total']);
        $this->assertSame('MOROSO', $resultado['estado']);
    }

    public function test_cp13_usuario_sin_retraso_queda_al_dia_y_multa_cero(): void
    {
        $servicio = new MorosidadService();

        $resultado = $servicio->calcularMulta(
            rol: 'estudiante',
            fechaDevolucion: '2026-06-25',
            fechaActual: '2026-06-25',
        );

        $this->assertTrue($resultado['valido']);
        $this->assertSame('estudiante', $resultado['rol']);
        $this->assertSame(0, $resultado['dias_retraso']);
        $this->assertSame(2.00, $resultado['tarifa_diaria']);
        $this->assertSame(0.00, $resultado['multa_total']);
        $this->assertSame('AL_DIA', $resultado['estado']);
    }

    public function test_cp14_rol_invalido_no_puede_calcular_morosidad(): void
    {
        $servicio = new MorosidadService();

        $resultado = $servicio->calcularMulta(
            rol: 'invitado',
            fechaDevolucion: '2026-06-15',
            fechaActual: '2026-06-25',
        );

        $this->assertFalse($resultado['valido']);
        $this->assertNull($resultado['rol']);
        $this->assertSame(0, $resultado['dias_retraso']);
        $this->assertSame(0.00, $resultado['tarifa_diaria']);
        $this->assertSame(0.00, $resultado['multa_total']);
        $this->assertSame('ERROR', $resultado['estado']);
        $this->assertStringContainsString('Rol no valido', $resultado['mensaje']);
    }

    public function test_cp15_pago_de_multa_detiene_acumulacion_de_morosidad(): void
    {
        $servicio = new MorosidadService();

        $resultado = $servicio->calcularMultaDespuesDePago(
            rol: 'docente',
            fechaDevolucion: '2026-06-15',
            fechaPago: '2026-06-25',
            fechaActual: '2026-06-30',
        );

        $this->assertTrue($resultado['valido']);
        $this->assertSame(10, $resultado['dias_retraso_hasta_pago']);
        $this->assertSame(50.00, $resultado['multa_pagada']);
        $this->assertSame(50.00, $resultado['multa_actual']);
        $this->assertFalse($resultado['multa_sigue_acumulando']);
        $this->assertSame('PAGADA', $resultado['estado']);
    }

    public function test_cp16_despues_del_pago_inicia_penalizacion_de_21_dias(): void
    {
        $servicio = new MorosidadService();

        $resultado = $servicio->calcularPenalizacionDespuesDePago(
            fechaPago: '2026-06-25',
            fechaActual: '2026-06-30',
        );

        $this->assertTrue($resultado['valido']);
        $this->assertSame(21, $resultado['dias_penalizacion']);
        $this->assertSame('2026-07-16', $resultado['fecha_habilitacion']);
        $this->assertSame(16, $resultado['dias_restantes']);
        $this->assertFalse($resultado['puede_prestar']);
        $this->assertSame('PENALIZADO', $resultado['estado']);
    }

    public function test_cp17_usuario_queda_habilitado_cuando_termina_la_penalizacion(): void
    {
        $servicio = new MorosidadService();

        $resultado = $servicio->calcularPenalizacionDespuesDePago(
            fechaPago: '2026-06-25',
            fechaActual: '2026-07-16',
        );

        $this->assertTrue($resultado['valido']);
        $this->assertSame(21, $resultado['dias_penalizacion']);
        $this->assertSame('2026-07-16', $resultado['fecha_habilitacion']);
        $this->assertSame(0, $resultado['dias_restantes']);
        $this->assertTrue($resultado['puede_prestar']);
        $this->assertSame('HABILITADO', $resultado['estado']);
    }
}
