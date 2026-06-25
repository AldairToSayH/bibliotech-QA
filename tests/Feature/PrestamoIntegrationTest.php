<?php

namespace Tests\Feature;

use App\Models\Libro;
use App\Models\Pago;
use App\Models\User;
use App\Services\PrestamoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrestamoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cp09_libro_disponible_puede_ser_prestado_y_cambia_a_prestado(): void
    {
        $usuario = User::create([
            'name' => 'Juan Estudiante',
            'email' => 'juan.estudiante@test.com',
            'password' => 'password',
            'rol' => 'estudiante',
        ]);

        $libro = Libro::create([
            'titulo' => 'Programacion en PHP',
            'estado' => 'DISPONIBLE',
        ]);

        $servicio = new PrestamoService();

        $resultado = $servicio->registrarPrestamo(
            userId: $usuario->id,
            libroId: $libro->id,
            rol: 'estudiante',
            fechaPrestamo: '2026-06-25',
        );

        $this->assertTrue($resultado['valido']);
        $this->assertSame('2026-07-02', $resultado['fecha_devolucion']);

        $this->assertDatabaseHas('prestamos', [
            'id' => $resultado['prestamo_id'],
            'user_id' => $usuario->id,
            'libro_id' => $libro->id,
            'fecha_prestamo' => '2026-06-25',
            'fecha_devolucion' => '2026-07-02',
            'estado' => 'ACTIVO',
        ]);

        $this->assertDatabaseHas('libros', [
            'id' => $libro->id,
            'titulo' => 'Programacion en PHP',
            'estado' => 'PRESTADO',
        ]);
    }

    public function test_cp10_libro_prestado_no_puede_volver_a_prestarse(): void
    {
        $usuario = User::create([
            'name' => 'Ana Estudiante',
            'email' => 'ana.estudiante@test.com',
            'password' => 'password',
            'rol' => 'estudiante',
        ]);

        $libro = Libro::create([
            'titulo' => 'Base de Datos con MySQL',
            'estado' => 'PRESTADO',
        ]);

        $servicio = new PrestamoService();

        $resultado = $servicio->registrarPrestamo(
            userId: $usuario->id,
            libroId: $libro->id,
            rol: 'estudiante',
            fechaPrestamo: '2026-06-25',
        );

        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('libro no esta disponible', $resultado['mensaje']);

        $this->assertDatabaseMissing('prestamos', [
            'user_id' => $usuario->id,
            'libro_id' => $libro->id,
        ]);

        $this->assertDatabaseHas('libros', [
            'id' => $libro->id,
            'titulo' => 'Base de Datos con MySQL',
            'estado' => 'PRESTADO',
        ]);
    }

    public function test_cp19_usuario_penalizado_no_puede_registrar_nuevo_prestamo(): void
    {
        $usuario = User::create([
            'name' => 'Carlos Docente',
            'email' => 'carlos.penalizado@test.com',
            'password' => 'password',
            'rol' => 'docente',
        ]);

        Pago::create([
            'user_id' => $usuario->id,
            'prestamo_id' => null,
            'monto' => 50.00,
            'fecha_pago' => '2026-06-25',
            'fecha_habilitacion' => '2026-07-16',
            'estado' => 'PENALIZADO',
            'pagado_en' => '2026-06-25',
        ]);

        $libro = Libro::create([
            'titulo' => 'Arquitectura de Software',
            'estado' => 'DISPONIBLE',
        ]);

        $servicio = new PrestamoService();

        $resultado = $servicio->registrarPrestamoValidandoPenalizacion(
            userId: $usuario->id,
            libroId: $libro->id,
            rol: 'docente',
            fechaPrestamo: '2026-06-30',
        );

        $this->assertFalse($resultado['valido']);
        $this->assertFalse($resultado['puede_prestar']);
        $this->assertStringContainsString('penalizado', $resultado['mensaje']);

        $this->assertDatabaseMissing('prestamos', [
            'user_id' => $usuario->id,
            'libro_id' => $libro->id,
        ]);

        $this->assertDatabaseHas('libros', [
            'id' => $libro->id,
            'titulo' => 'Arquitectura de Software',
            'estado' => 'DISPONIBLE',
        ]);
    }

    public function test_cp20_usuario_habilitado_despues_de_penalizacion_si_puede_registrar_prestamo(): void
    {
        $usuario = User::create([
            'name' => 'Carlos Docente',
            'email' => 'carlos.habilitado@test.com',
            'password' => 'password',
            'rol' => 'docente',
        ]);

        Pago::create([
            'user_id' => $usuario->id,
            'prestamo_id' => null,
            'monto' => 50.00,
            'fecha_pago' => '2026-06-25',
            'fecha_habilitacion' => '2026-07-16',
            'estado' => 'PENALIZADO',
            'pagado_en' => '2026-06-25',
        ]);

        $libro = Libro::create([
            'titulo' => 'Patrones de Diseno',
            'estado' => 'DISPONIBLE',
        ]);

        $servicio = new PrestamoService();

        $resultado = $servicio->registrarPrestamoValidandoPenalizacion(
            userId: $usuario->id,
            libroId: $libro->id,
            rol: 'docente',
            fechaPrestamo: '2026-07-16',
        );

        $this->assertTrue($resultado['valido']);
        $this->assertSame('2026-07-30', $resultado['fecha_devolucion']);
        $this->assertStringContainsString('Prestamo registrado correctamente', $resultado['mensaje']);

        $this->assertDatabaseHas('prestamos', [
            'id' => $resultado['prestamo_id'],
            'user_id' => $usuario->id,
            'libro_id' => $libro->id,
            'fecha_prestamo' => '2026-07-16',
            'fecha_devolucion' => '2026-07-30',
            'estado' => 'ACTIVO',
        ]);

        $this->assertDatabaseHas('libros', [
            'id' => $libro->id,
            'titulo' => 'Patrones de Diseno',
            'estado' => 'PRESTADO',
        ]);
    }
}
