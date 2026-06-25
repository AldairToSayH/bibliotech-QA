@extends('layouts.app')

@section('title', 'BiblioTech - Prestamos')

@section('content')
    @php
        $casos = [
            ['codigo' => 'CP06', 'titulo' => 'Estudiante', 'detalle' => 'Rol: estudiante | Fecha: 2026-06-25 | Devuelve: 2026-07-02'],
            ['codigo' => 'CP07', 'titulo' => 'Docente', 'detalle' => 'Rol: docente | Fecha: 2026-06-25 | Devuelve: 2026-07-09'],
            ['codigo' => 'CP08', 'titulo' => 'Rol invalido', 'detalle' => 'Rol: invitado | Fecha: 2026-06-25 | Resultado: rechazado'],
            ['codigo' => 'CP09', 'titulo' => 'Libro disponible', 'detalle' => 'Libro DISPONIBLE | Resultado: cambia a PRESTADO'],
            ['codigo' => 'CP10', 'titulo' => 'Libro prestado', 'detalle' => 'Libro PRESTADO | Resultado: prestamo rechazado'],
        ];
    @endphp

    <section class="panel">
        <h1 class="page-title">Libros y Pr&eacute;stamos</h1>
        <p class="page-description">
            Pantalla academica para demostrar plazos de prestamo por rol y registro de prestamos
            usando la logica probada en <strong>PrestamoService</strong>.
        </p>
    </section>

    @unless ($tablasDisponibles)
        <section class="notice">
            Las tablas de usuarios y libros no estan disponibles. Ejecute las migraciones antes de usar esta pantalla.
        </section>
    @endunless

    <section class="content-split">
        <div class="panel">
            <h2 style="margin-top: 0;">A. Calculadora de plazo de pr&eacute;stamo</h2>
            <p class="page-description">
                Demuestra CP06, CP07 y CP08 sin registrar datos en base de datos.
            </p>

            <form method="POST" action="{{ route('prestamos.calcular-plazo') }}">
                @csrf

                <div class="form-grid">
                    <div class="form-field">
                        <label for="plazo_rol">Rol</label>
                        <select id="plazo_rol" name="rol">
                            @foreach (['estudiante' => 'Estudiante', 'docente' => 'Docente', 'invitado' => 'Invitado'] as $valor => $texto)
                                <option value="{{ $valor }}" @selected(old('rol', $datosPlazo['rol'] ?? 'estudiante') === $valor)>
                                    {{ $texto }}
                                </option>
                            @endforeach
                        </select>
                        @error('rol')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="plazo_fecha">Fecha de pr&eacute;stamo</label>
                        <input
                            id="plazo_fecha"
                            name="fecha_prestamo"
                            type="date"
                            value="{{ old('fecha_prestamo', $datosPlazo['fecha_prestamo'] ?? '2026-06-25') }}"
                        >
                        @error('fecha_prestamo')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button class="button" type="submit">Calcular plazo</button>
                </div>
            </form>

            @isset($resultadoPlazo)
                @if ($resultadoPlazo['valido'])
                    <div class="result-card result-valid">
                        <h2>Plazo calculado</h2>
                        <p>Rol: <strong>{{ $resultadoPlazo['rol'] }}</strong></p>
                        <p>Dias de prestamo: <strong>{{ $resultadoPlazo['dias_prestamo'] }}</strong></p>
                        <p>Fecha de devolucion: <strong>{{ $resultadoPlazo['fecha_devolucion'] }}</strong></p>
                    </div>
                @else
                    <div class="result-card result-invalid">
                        <h2>Prestamo rechazado</h2>
                        <p>{{ $resultadoPlazo['mensaje'] }}</p>
                    </div>
                @endif
            @endisset
        </div>

        <aside class="panel">
            <h2 style="margin-top: 0;">Casos de prueba r&aacute;pidos</h2>
            <p class="page-description">Datos guia para demostrar CP06-CP10 desde la interfaz.</p>
            <div class="case-grid">
                @foreach ($casos as $caso)
                    <article class="case-card">
                        <h3>{{ $caso['codigo'] }} - {{ $caso['titulo'] }}</h3>
                        <p style="margin: 0;">{{ $caso['detalle'] }}</p>
                    </article>
                @endforeach
            </div>
        </aside>
    </section>

    <section class="panel" style="margin-top: 18px;">
        <h2 style="margin-top: 0;">B. Registro de pr&eacute;stamo con libro real</h2>
        <p class="page-description">
            Demuestra CP09 y CP10 usando usuarios y libros reales de la base de datos.
        </p>

        <form method="POST" action="{{ route('prestamos.registrar') }}">
            @csrf

            <div class="form-grid">
                <div class="form-field">
                    <label for="registro_user_id">Usuario</label>
                    <select id="registro_user_id" name="user_id" @disabled(!$tablasDisponibles || $usuarios->isEmpty())>
                        @forelse ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" @selected((string) old('user_id', $datosRegistro['user_id'] ?? '') === (string) $usuario->id)>
                                {{ $usuario->name }} ({{ $usuario->rol ?? 'sin rol' }})
                            </option>
                        @empty
                            <option value="">No hay usuarios</option>
                        @endforelse
                    </select>
                    @error('user_id')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="registro_libro_id">Libro</label>
                    <select id="registro_libro_id" name="libro_id" @disabled(!$tablasDisponibles || $libros->isEmpty())>
                        @forelse ($libros as $libro)
                            <option value="{{ $libro->id }}" @selected((string) old('libro_id', $datosRegistro['libro_id'] ?? '') === (string) $libro->id)>
                                #{{ $libro->id }} - {{ $libro->titulo }} ({{ $libro->estado }})
                            </option>
                        @empty
                            <option value="">No hay libros</option>
                        @endforelse
                    </select>
                    @error('libro_id')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="registro_rol">Rol</label>
                    <select id="registro_rol" name="rol">
                        @foreach (['estudiante' => 'Estudiante', 'docente' => 'Docente', 'invitado' => 'Invitado'] as $valor => $texto)
                            <option value="{{ $valor }}" @selected(old('rol', $datosRegistro['rol'] ?? 'estudiante') === $valor)>
                                {{ $texto }}
                            </option>
                        @endforeach
                    </select>
                    @error('rol')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="registro_fecha">Fecha de pr&eacute;stamo</label>
                    <input
                        id="registro_fecha"
                        name="fecha_prestamo"
                        type="date"
                        value="{{ old('fecha_prestamo', $datosRegistro['fecha_prestamo'] ?? '2026-06-25') }}"
                    >
                    @error('fecha_prestamo')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button class="button" type="submit" @disabled(!$tablasDisponibles || $usuarios->isEmpty() || $libros->isEmpty())>
                    Registrar pr&eacute;stamo
                </button>
            </div>
        </form>

        @isset($resultadoRegistro)
            @if ($resultadoRegistro['valido'])
                <div class="result-card result-valid">
                    <h2>Pr&eacute;stamo registrado</h2>
                    <p>Fecha de devolucion: <strong>{{ $resultadoRegistro['fecha_devolucion'] }}</strong></p>
                    <p>Estado del libro: <strong>{{ $resultadoRegistro['libro_estado'] }}</strong></p>
                </div>
            @else
                <div class="result-card result-invalid">
                    <h2>Pr&eacute;stamo rechazado</h2>
                    <p>{{ $resultadoRegistro['mensaje'] }}</p>
                </div>
            @endif
        @endisset
    </section>

    <section class="panel" style="margin-top: 18px;">
        <h2 style="margin-top: 0;">Libros disponibles para demostraci&oacute;n</h2>
        <p class="page-description">Estados usados para probar CP09 y CP10.</p>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>T&iacute;tulo</th>
                        <th>Estado</th>
                        <th>Acci&oacute;n sugerida</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($libros as $libro)
                        <tr>
                            <td>{{ $libro->id }}</td>
                            <td>{{ $libro->titulo }}</td>
                            <td>
                                @if ($libro->estado === 'DISPONIBLE')
                                    <span class="badge badge-green">DISPONIBLE</span>
                                @else
                                    <span class="badge badge-warn">{{ $libro->estado }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($libro->estado === 'DISPONIBLE')
                                    Usar para CP09.
                                @else
                                    Usar para CP10.
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No hay libros registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
