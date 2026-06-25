# BiblioTech

BiblioTech es un sistema academico de biblioteca desarrollado con PHP/Laravel y MySQL para el curso **Pruebas y Calidad de Software**.

El objetivo principal del proyecto no es construir una aplicacion grande, sino demostrar de forma ordenada distintos tipos de pruebas con PHPUnit:

- Pruebas unitarias.
- Pruebas de integracion.
- Pruebas de humo.
- Verificacion de regresion basica.

## Stack

- PHP 8.2 o superior.
- Laravel 12.
- PHPUnit.
- MySQL/MariaDB con XAMPP.
- Composer.
- Base principal: `bibliotech`.
- Base de pruebas: `bibliotech_test`.

## Manual de Ejecucion en Otro Dispositivo

Esta guia asume Windows con XAMPP instalado. Tambien puede ejecutarse en otro entorno si tiene PHP, Composer y MySQL/MariaDB disponibles.

### 1. Requisitos

Instalar:

- XAMPP actualizado, con Apache y MySQL/MariaDB.
- Composer.
- Git, si se clonara el repositorio desde control de versiones.

Verificar versiones:

```powershell
php -v
composer -V
```

Si Windows no reconoce `php`, agregar PHP de XAMPP al PATH temporal de la terminal:

```powershell
$env:Path = "C:\xampp\php;$env:Path"
```

### 2. Obtener el proyecto

Opcion A, clonar con Git:

```powershell
git clone <URL_DEL_REPOSITORIO> bibliotech
cd bibliotech
```

Opcion B, copiar carpeta:

Copiar la carpeta del proyecto al nuevo equipo y abrir una terminal dentro de esa carpeta.

### 3. Instalar dependencias PHP

```powershell
composer install
```

No copiar manualmente la carpeta `vendor`; Composer debe reconstruirla.

### 4. Crear archivo de entorno

Copiar el archivo de ejemplo:

```powershell
copy .env.example .env
```

Generar clave de aplicacion:

```powershell
php artisan key:generate
```

Configurar en `.env` la base principal:

```env
APP_NAME=BiblioTech
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bibliotech
DB_USERNAME=root
DB_PASSWORD=
```

En XAMPP, por defecto el usuario suele ser `root` sin password.

### 5. Iniciar XAMPP

Abrir el panel de XAMPP e iniciar:

- Apache.
- MySQL.

Si se trabajara solo con `php artisan serve`, Apache no es obligatorio, pero puede estar activo sin problema. MySQL si es necesario para migraciones, pruebas de integracion y pantallas con base de datos.

### 6. Crear bases de datos

Desde PowerShell:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS bibliotech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE DATABASE IF NOT EXISTS bibliotech_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Tambien puede hacerse desde phpMyAdmin creando:

- `bibliotech`
- `bibliotech_test`

### 7. Ejecutar migraciones

Migrar la base principal:

```powershell
php artisan migrate
```

Esto crea las tablas:

- `users`
- `libros`
- `prestamos`
- `pagos`

### 8. Limpiar cache de configuracion

```powershell
php artisan config:clear
php artisan cache:clear
```

### 9. Ejecutar pruebas

```powershell
php artisan test
```

Resultado esperado:

```text
Tests: 22 passed
Assertions: 106
```

### 10. Levantar la aplicacion web

```powershell
php artisan serve
```

Abrir:

```text
http://127.0.0.1:8000
```

La primera pantalla es el login de administrador.

Credenciales de demostracion:

```text
Correo: admin@bibliotech.test
Contrasena: admin123
```

El usuario administrador demo se crea automaticamente si no existe.

Rutas principales:

- `http://127.0.0.1:8000/` - Login administrador
- `http://127.0.0.1:8000/dashboard` - Dashboard protegido
- `http://127.0.0.1:8000/registro`
- `http://127.0.0.1:8000/libros`
- `http://127.0.0.1:8000/prestamos`
- `http://127.0.0.1:8000/morosidad`
- `http://127.0.0.1:8000/pagos`
- `http://127.0.0.1:8000/panel-pruebas`

### 11. Solucion de Problemas Comunes

Si aparece `php no se reconoce como comando`, usar:

```powershell
$env:Path = "C:\xampp\php;$env:Path"
```

Si aparece error de conexion MySQL:

- Verificar que MySQL este iniciado en XAMPP.
- Verificar que el puerto sea `3306`.
- Verificar usuario `root` y password vacio en `.env` y `phpunit.xml`.
- Ejecutar `php artisan config:clear`.

Si faltan tablas:

```powershell
php artisan migrate
```

Si las pruebas de integracion fallan por base inexistente:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS bibliotech_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Si se desea reiniciar la base principal desde cero durante una practica:

```powershell
php artisan migrate:fresh
```

Advertencia: `migrate:fresh` borra y recrea las tablas de la base configurada.

## Modulos Cubiertos

### HU01 - Registro y Validacion de Identidad

Servicio: `app/Services/RegistroService.php`

Casos cubiertos:

- CP01 - Registro valido de estudiante.
- CP02 - Registro valido de docente.
- CP03 - Rechazo por DNI que no coincide con codigo institucional.
- CP04 - Rechazo por prefijo de rol invalido.
- CP05 - Rechazo por gafete que no corresponde al rol institucional.

### HU02 - Gestion de Prestamos y Exclusividad

Servicio: `app/Services/PrestamoService.php`

Casos cubiertos:

- CP06 - Estudiante recibe prestamo por 7 dias.
- CP07 - Docente recibe prestamo por 14 dias.
- CP08 - Rol invalido no puede recibir prestamo.
- CP09 - Libro disponible puede ser prestado y cambia a `PRESTADO`.
- CP10 - Libro ya prestado no puede volver a prestarse.
- CP19 - Usuario penalizado no puede registrar un nuevo prestamo.
- CP20 - Usuario habilitado despues de penalizacion si puede registrar prestamo.

### HU03 - Morosidad, Pago y Penalizacion

Servicio: `app/Services/MorosidadService.php`

Casos cubiertos:

- CP11 - Docente con 10 dias de retraso genera multa S/ 50.00.
- CP12 - Estudiante con 10 dias de retraso genera multa S/ 20.00.
- CP13 - Usuario sin retraso queda `AL_DIA` y multa 0.00.
- CP14 - Rol invalido retorna `ERROR`.
- CP15 - Pago de multa detiene la acumulacion de morosidad.
- CP16 - Despues del pago inicia penalizacion de 21 dias.
- CP17 - Usuario queda habilitado cuando termina la penalizacion.
- CP18 - Pago de multa registra fecha, congela deuda e inicia penalizacion.

### Smoke Test

- CP21 - Verifica ruta base, servicios principales, modelos principales y tablas base.

## Estructura Relevante

```text
app/Services
  RegistroService.php
  PrestamoService.php
  MorosidadService.php

app/Models
  User.php
  Libro.php
  Prestamo.php
  Pago.php

tests/Unit
  RegistroServiceTest.php
  PrestamoServiceTest.php
  MorosidadServiceTest.php

tests/Feature
  SmokeTest.php
  PrestamoIntegrationTest.php
  MorosidadIntegrationTest.php
```

## Configuracion de Pruebas

La base de datos de testing esperada es:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bibliotech_test
DB_USERNAME=root
DB_PASSWORD=
```

Si `php` no esta en el PATH de Windows, puede agregarse temporalmente:

```powershell
$env:Path = "C:\xampp\php;$env:Path"
```

Para crear las bases:

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS bibliotech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE DATABASE IF NOT EXISTS bibliotech_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## Ejecutar Pruebas

Limpiar configuracion:

```powershell
php artisan config:clear
```

Ejecutar toda la suite:

```powershell
php artisan test
```

Ejecutar pruebas por modulo:

```powershell
php artisan test --filter RegistroServiceTest
php artisan test --filter PrestamoServiceTest
php artisan test --filter MorosidadServiceTest
php artisan test --filter PrestamoIntegrationTest
php artisan test --filter MorosidadIntegrationTest
php artisan test --filter SmokeTest
```

## Estado Actual

La suite completa pasa correctamente con:

```text
Tests: 22 passed
Assertions: 106
```

## Alcance Excluido

Por decision academica y de alcance, este proyecto no implementa:

- OCR.
- Cypress.
- Laravel Dusk.
- Pasarelas de pago reales.
- APIs externas.
- Autenticacion.
- Panel administrativo completo de produccion.

El foco esta en demostrar calidad de software mediante pruebas claras, repetibles y defendibles.
