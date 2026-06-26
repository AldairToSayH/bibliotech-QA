@extends('layouts.app')

@section('title', 'BiblioTech - Prestamos')
@section('section-title', 'Prestamos')

@section('content')
    @php
        $puedeEditar = request()->is('admin/*') || in_array(Auth::user()?->rol, ['admin', 'editor'], true);
    @endphp

    <section class="panel">
        <h1 class="page-title">Libros y Pr&eacute;stamos</h1>
        <p class="page-description">
            Gestion de solicitudes, fechas de devolucion y estado de prestamos.
        </p>
    </section>

    @unless ($tablasDisponibles)
        <section class="notice">
            Las tablas de usuarios y libros no estan disponibles. Ejecute las migraciones antes de usar esta pantalla.
        </section>
    @endunless

    @isset($mensaje)
        <section class="result-card result-valid">
            <h2>{{ $mensaje }}</h2>
        </section>
    @endisset

    @if ($puedeEditar)
        <section class="content-split">
        <div class="panel">
            <h2 style="margin-top: 0;">Calculadora de plazo de pr&eacute;stamo</h2>
            <p class="page-description">
                Consulte la fecha de devolucion antes de registrar una operacion.
            </p>

            <form method="POST" action="{{ route('prestamos.calcular-plazo') }}">
                @csrf

                <div class="form-grid">
                    <div class="form-field">
                        <label for="plazo_rol">Rol</label>
                        <select id="plazo_rol" name="rol">
                            @foreach (['estudiante' => 'Estudiante', 'docente' => 'Docente'] as $valor => $texto)
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
            <h2 style="margin-top: 0;">Estados</h2>
            <p class="page-description">Referencia rapida para la gestion de prestamos.</p>
            <div class="case-grid">
                <article class="case-card">
                    <h3>Operacion activa</h3>
                    <p><span class="badge badge-green">ACTIVO</span></p>
                </article>
                <article class="case-card">
                    <h3>Seguimiento</h3>
                    <p><span class="badge badge-warn">VENCIDO</span> <span class="badge badge-warn">PENALIZADO</span></p>
                </article>
            </div>
        </aside>
        </section>
    @else
        <section class="panel" style="margin-top: 18px;">
            <h2 style="margin-top: 0;">Modo lectura</h2>
            <p class="page-description">Su rol permite consultar prestamos sin registrar ni modificar operaciones.</p>
        </section>
    @endif

    @if ($puedeEditar)
    <section class="panel" id="prestamos-registrados" style="margin-top: 18px;">
        <h2 style="margin-top: 0;">Registrar pr&eacute;stamo</h2>
        <p class="page-description">
            Seleccione un usuario activo y un libro disponible. El plazo se calcula con el rol registrado del usuario.
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
                    <select id="registro_libro_id" name="libro_id" @disabled(!$tablasDisponibles || $librosDisponibles->isEmpty())>
                        @forelse ($librosDisponibles as $libro)
                            <option value="{{ $libro->id }}" @selected((string) old('libro_id', $datosRegistro['libro_id'] ?? '') === (string) $libro->id)>
                                #{{ $libro->id }} - {{ $libro->titulo }}
                            </option>
                        @empty
                            <option value="">No hay libros disponibles</option>
                        @endforelse
                    </select>
                    @error('libro_id')
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
                <button class="button" type="submit" @disabled(!$tablasDisponibles || $usuarios->isEmpty() || $librosDisponibles->isEmpty())>
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
    @endif

    <section class="panel" style="margin-top: 18px;">
        <h2 style="margin-top: 0;">Prestamos registrados</h2>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Libro</th>
                        <th>Prestamo</th>
                        <th>Devolucion</th>
                        <th>Estado</th>
                        @if ($puedeEditar)
                            <th>Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($prestamos as $prestamo)
                        <tr>
                            <td>{{ $prestamo->id }}</td>
                            <td>{{ $prestamo->user?->name ?? 'N/A' }}</td>
                            <td>{{ $prestamo->libro?->titulo ?? 'N/A' }}</td>
                            <td>{{ $prestamo->fecha_prestamo ?? 'N/A' }}</td>
                            <td>{{ $prestamo->fecha_devolucion ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ in_array($prestamo->estado, ['ACTIVO', 'HABILITADO', 'FINALIZADO'], true) ? 'badge-green' : 'badge-warn' }}">
                                    {{ $prestamo->estado }}
                                </span>
                            </td>
                            @if ($puedeEditar)
                                <td>
                                    <div class="action-row">
                                        <a class="button-small" href="#editar-prestamo-{{ $prestamo->id }}">Editar</a>
                                        <form method="POST" action="{{ route('prestamos.destroy', $prestamo) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="button-danger" type="submit">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $puedeEditar ? 7 : 6 }}">No hay prestamos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if ($puedeEditar)
        @foreach ($prestamos as $prestamo)
            <section class="modal" id="editar-prestamo-{{ $prestamo->id }}" aria-label="Editar prestamo {{ $prestamo->id }}">
            <div class="modal-dialog">
                <div class="modal-header">
                    <div>
                        <h2 class="modal-title">Editar prestamo</h2>
                        <p class="page-description">Registro #{{ $prestamo->id }}</p>
                    </div>
                    <a class="modal-close" href="#prestamos-registrados">X</a>
                </div>

                <form method="POST" action="{{ route('prestamos.update', $prestamo) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="prestamo_user_{{ $prestamo->id }}">Usuario</label>
                            <select id="prestamo_user_{{ $prestamo->id }}" name="user_id">
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" @selected((int) old('user_id', $prestamo->user_id) === $usuario->id)>
                                        {{ $usuario->name }} ({{ $usuario->rol ?? 'sin rol' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="prestamo_libro_{{ $prestamo->id }}">Libro</label>
                            <select id="prestamo_libro_{{ $prestamo->id }}" name="libro_id">
                                @foreach ($libros as $libro)
                                    <option value="{{ $libro->id }}" @selected((int) old('libro_id', $prestamo->libro_id) === $libro->id)>
                                        {{ $libro->titulo }} ({{ $libro->estado }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="prestamo_fecha_{{ $prestamo->id }}">Fecha prestamo</label>
                            <input id="prestamo_fecha_{{ $prestamo->id }}" name="fecha_prestamo" type="date" value="{{ old('fecha_prestamo', $prestamo->fecha_prestamo) }}">
                        </div>

                        <div class="form-field">
                            <label for="devolucion_fecha_{{ $prestamo->id }}">Fecha devolucion</label>
                            <input id="devolucion_fecha_{{ $prestamo->id }}" name="fecha_devolucion" type="date" value="{{ old('fecha_devolucion', $prestamo->fecha_devolucion) }}">
                        </div>

                        <div class="form-field">
                            <label for="prestamo_estado_{{ $prestamo->id }}">Estado</label>
                            <select id="prestamo_estado_{{ $prestamo->id }}" name="estado">
                                @foreach (['ACTIVO', 'VENCIDO', 'PENALIZADO', 'PAGADO', 'FINALIZADO'] as $estado)
                                    <option value="{{ $estado }}" @selected(old('estado', $prestamo->estado) === $estado)>{{ $estado }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-actions action-row">
                        <button class="button" type="submit">Guardar cambios</button>
                        <a class="button-secondary" href="#prestamos-registrados">Cancelar</a>
                    </div>
                </form>
            </div>
            </section>
        @endforeach
    @endif

    <section class="panel" style="margin-top: 18px;">
        <h2 style="margin-top: 0;">Disponibilidad del catalogo</h2>
        <p class="page-description">Catalogo disponible para nuevas operaciones.</p>

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
                                    Disponible
                                @else
                                    No disponible
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
