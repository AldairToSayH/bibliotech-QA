<?php

namespace Tests\Unit;

use App\Services\RegistroService;
use PHPUnit\Framework\TestCase;

class RegistroServiceTest extends TestCase
{
    public function test_cp01_registro_valido_de_estudiante(): void
    {
        $servicio = new RegistroService();

        $resultado = $servicio->validarRegistro(
            dni: '74698202',
            codigoInstitucion: '474698202',
            codigoGafete: '444',
        );

        $this->assertTrue($resultado['valido']);
        $this->assertSame('estudiante', $resultado['rol']);
    }

    public function test_cp02_registro_valido_de_docente(): void
    {
        $servicio = new RegistroService();

        $resultado = $servicio->validarRegistro(
            dni: '25863008',
            codigoInstitucion: '725863008',
            codigoGafete: '443',
        );

        $this->assertTrue($resultado['valido']);
        $this->assertSame('docente', $resultado['rol']);
    }

    public function test_cp03_rechaza_registro_cuando_dni_no_coincide_con_codigo_institucional(): void
    {
        $servicio = new RegistroService();

        $resultado = $servicio->validarRegistro(
            dni: '11111111',
            codigoInstitucion: '474698202',
            codigoGafete: '444',
        );

        $this->assertFalse($resultado['valido']);
        $this->assertNull($resultado['rol']);
        $this->assertStringContainsString('DNI no coincide', $resultado['mensaje']);
    }

    public function test_cp04_rechaza_registro_por_prefijo_de_rol_invalido(): void
    {
        $servicio = new RegistroService();

        $resultado = $servicio->validarRegistro(
            dni: '74698202',
            codigoInstitucion: '974698202',
            codigoGafete: '444',
        );

        $this->assertFalse($resultado['valido']);
        $this->assertNull($resultado['rol']);
        $this->assertStringContainsString('prefijo', $resultado['mensaje']);
    }

    public function test_cp05_rechaza_registro_cuando_gafete_no_corresponde_al_rol_institucional(): void
    {
        $servicio = new RegistroService();

        $resultado = $servicio->validarRegistro(
            dni: '74698202',
            codigoInstitucion: '474698202',
            codigoGafete: '443',
        );

        $this->assertFalse($resultado['valido']);
        $this->assertNull($resultado['rol']);
        $this->assertStringContainsString('gafete fisico no corresponde', $resultado['mensaje']);
    }
}
