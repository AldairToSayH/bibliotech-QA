<?php

namespace Tests\Feature;

use App\Models\Libro;
use App\Models\Pago;
use App\Models\Prestamo;
use App\Models\User;
use App\Services\MorosidadService;
use App\Services\PrestamoService;
use App\Services\RegistroService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_home_page_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('BiblioTech');
    }

    public function test_cp21_componentes_principales_del_sistema_estan_disponibles(): void
    {
        $this->assertTrue(class_exists(RegistroService::class));
        $this->assertTrue(class_exists(PrestamoService::class));
        $this->assertTrue(class_exists(MorosidadService::class));

        $this->assertTrue(class_exists(User::class));
        $this->assertTrue(class_exists(Libro::class));
        $this->assertTrue(class_exists(Prestamo::class));
        $this->assertTrue(class_exists(Pago::class));

        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('libros'));
        $this->assertTrue(Schema::hasTable('prestamos'));
        $this->assertTrue(Schema::hasTable('pagos'));
    }
}
