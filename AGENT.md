# AGENT.md - BiblioTech

## Contexto del proyecto

BiblioTech es un proyecto academico para el curso "Pruebas y Calidad de Software".
El objetivo principal es demostrar pruebas unitarias, integracion, sistema, humo y regresion con Laravel, PHPUnit y MySQL usando XAMPP.

Este proyecto debe mantenerse simple, limpio y orientado a pruebas. No convertirlo en un sistema grande antes de completar los casos de prueba paso a paso.

## Stack actual

- PHP: 8.2 o superior.
- Framework: Laravel 12.
- Testing: PHPUnit mediante `php artisan test`.
- Base de datos: MySQL/MariaDB de XAMPP.
- Base principal: `bibliotech`.
- Base de testing: `bibliotech_test`.

En esta maquina, si `php` no esta en el PATH global, usar temporalmente:

```powershell
$env:Path = "C:\xampp\php;$env:Path"
```

## Configuracion de testing esperada

`phpunit.xml` y `.env.testing` deben apuntar a:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bibliotech_test
DB_USERNAME=root
DB_PASSWORD=
```

Antes de correr pruebas con base de datos, verificar que MySQL de XAMPP este activo. Si hace falta iniciarlo desde PowerShell:

```powershell
Start-Process -FilePath "C:\xampp\mysql\bin\mysqld.exe" -ArgumentList "--defaults-file=C:\xampp\mysql\bin\my.ini --standalone" -WorkingDirectory "C:\xampp\mysql\bin" -WindowStyle Hidden
```

Crear bases si no existen:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS bibliotech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE DATABASE IF NOT EXISTS bibliotech_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## Comandos frecuentes

Limpiar cache de configuracion:

```powershell
php artisan config:clear
```

Migrar base de testing desde cero:

```powershell
php artisan migrate:fresh --env=testing --force
```

Correr una prueba especifica:

```powershell
php artisan test --filter RegistroServiceTest
```

Correr toda la suite:

```powershell
php artisan test
```

## Estructura importante

- Servicios de logica de negocio: `app/Services`.
- Pruebas unitarias: `tests/Unit`.
- Pruebas de feature/sistema/humo: `tests/Feature`.
- Prueba de humo actual: `tests/Feature/SmokeTest.php`.
- Servicio actual de registro: `app/Services/RegistroService.php`.
- Servicio actual de prestamos: `app/Services/PrestamoService.php`.
- Servicio actual de morosidad: `app/Services/MorosidadService.php`.
- Pruebas actuales de registro: `tests/Unit/RegistroServiceTest.php`.
- Pruebas unitarias actuales de prestamos: `tests/Unit/PrestamoServiceTest.php`.
- Pruebas unitarias actuales de morosidad: `tests/Unit/MorosidadServiceTest.php`.
- Pruebas de integracion actuales de prestamos: `tests/Feature/PrestamoIntegrationTest.php`.
- Pruebas de integracion actuales de morosidad: `tests/Feature/MorosidadIntegrationTest.php`.

## Estado actual de casos de prueba

Implementados:

- CP01 - Registro valido de estudiante.
  - DNI: `74698202`
  - Codigo institucional: `474698202`
  - Gafete: `444`
  - Rol esperado: `estudiante`

- CP02 - Registro valido de docente.
  - DNI: `25863008`
  - Codigo institucional: `725863008`
  - Gafete: `443`
  - Rol esperado: `docente`

- CP03 - Registro invalido cuando el DNI no coincide con el codigo institucional.
- CP04 - Registro invalido por prefijo de rol no permitido.
- CP05 - Registro invalido cuando el gafete no corresponde al rol.
- CP06 - Estudiante recibe prestamo por 7 dias.
- CP07 - Docente recibe prestamo por 14 dias.
- CP08 - Rol invalido no puede recibir prestamo.
- CP09 - Libro disponible puede ser prestado y cambia a estado `PRESTADO`.
- CP10 - Libro ya prestado no puede volver a prestarse.
- CP11 - Calculo de multa acumulada para docente con 10 dias de retraso.
- CP12 - Calculo de multa acumulada para estudiante con 10 dias de retraso.
- CP13 - Usuario sin retraso queda `AL_DIA` y multa 0.
- CP14 - Rol invalido no puede calcular morosidad.
- CP15 - Pago de multa detiene la acumulacion de morosidad.
- CP16 - Despues del pago inicia penalizacion de 21 dias.
- CP17 - Usuario queda habilitado cuando termina la penalizacion.
- CP18 - Pago de multa registra fecha, congela deuda e inicia penalizacion.
- CP19 - Usuario penalizado no puede registrar un nuevo prestamo.
- CP20 - Usuario habilitado despues de penalizacion si puede registrar prestamo.
- CP21 - Smoke Test final del sistema BiblioTech.

Pendientes recomendados:

- Pasar a pruebas de sistema o regresion sobre los flujos ya cubiertos.

## Reglas para proximos cambios

- Avanzar un caso de prueba a la vez, salvo que el usuario pida otra cosa.
- Mantener `RegistroService` como logica de negocio aislada, sin base de datos, requests, controladores ni vistas.
- Mantener la logica de plazo de `PrestamoService` aislada, y usar base de datos solo en pruebas de integracion cuando el caso lo pida.
- Mantener `MorosidadService` como logica de negocio aislada hasta que se implementen pagos o penalizaciones.
- Las pruebas unitarias de `RegistroService` deben usar `PHPUnit\Framework\TestCase`.
- Las pruebas de integracion deben usar `Tests\TestCase` y `Illuminate\Foundation\Testing\RefreshDatabase`.
- No implementar OCR, Cypress, Laravel Dusk, pagos reales, APIs externas ni pasarelas de pago.
- No agregar modulos completos antes de cubrir los casos de prueba planeados.
- Mantener codigo simple, legible y facil de explicar en exposicion academica.
- Despues de cada cambio de prueba, ejecutar primero el filtro especifico y luego `php artisan test`.

## Resultado esperado actual

La suite debe pasar con:

```text
Tests: 22 passed
```

Incluye:

- CP01 estudiante valido.
- CP02 docente valido.
- CP03 a CP05 casos invalidos de registro.
- CP06 a CP08 reglas unitarias de plazo de prestamo.
- CP09 y CP10 integracion de prestamo con MySQL.
- CP11 a CP17 reglas unitarias de multa, pago y penalizacion.
- CP18 integracion de pago de multa con MySQL.
- CP19 integracion de penalizacion activa contra prestamos.
- CP20 integracion de habilitacion posterior a penalizacion.
- CP21 SmokeTest final de servicios, modelos, tablas y ruta base `/`.
