@extends('layouts.app')

@section('title', 'BiblioTech - Pagos y Penalizacion')
@section('section-title', 'Pagos')

@section('content')
    @php
        $puedeEditar = request()->is('admin/*') || in_array(Auth::user()?->rol, ['admin', 'editor'], true);

        $estadoBadge = function (?string $estado): string {
            return match ($estado) {
                'PENALIZADO' => 'badge-warn',
                'HABILITADO', 'DISPONIBLE' => 'badge-green',
                'PAGADO', 'PAGADA', 'ACTIVO' => 'badge-blue',
                'PRESTADO', 'VENCIDO' => 'badge-warn',
                default => 'badge-red',
            };
        };

        $resultadoClase = function (?string $estado, bool $valido = true): string {
            if (!$valido) {
                return 'result-invalid';
            }

            return match ($estado) {
                'PENALIZADO' => 'result-warning',
                'HABILITADO' => 'result-valid',
                'PAGADO', 'PAGADA', 'ACTIVO' => 'result-info',
                default => 'result-valid',
            };
        };
    @endphp

    <section class="panel">
        <h1 class="page-title">Pagos y Penalizaci&oacute;n</h1>
        <p class="page-description">
            Gestion de pagos, penalizaciones y habilitaciones.
        </p>
    </section>

    @unless ($tablasDisponibles)
        <section class="notice">
            Las tablas necesarias no estan disponibles. Ejecute migraciones antes de usar esta pantalla.
        </section>
    @endunless

    @isset($mensaje)
        <section class="result-card result-valid">
            <h2>{{ $mensaje }}</h2>
        </section>
    @endisset

    @if ($puedeEditar)
    <section class="content-split">
        <div style="display: grid; gap: 18px;">
            <section class="panel">
                <h2 style="margin-top: 0;">A. Registrar pago de multa</h2>
                <p class="page-description">Registre el pago de una multa pendiente.</p>

                <form method="POST" action="{{ route('pagos.registrar-multa') }}">
                    @csrf

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="pago_user_id">Usuario</label>
                            <select id="pago_user_id" name="user_id" @disabled(!$tablasDisponibles || $usuarios->isEmpty())>
                                @forelse ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" @selected((string) old('user_id', $datosPagoMulta['user_id'] ?? '') === (string) $usuario->id)>
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
                            <label for="pago_prestamo_id">Pr&eacute;stamo vencido o penalizado</label>
                            <select id="pago_prestamo_id" name="prestamo_id" @disabled(!$tablasDisponibles || $prestamosVencidos->isEmpty())>
                                @forelse ($prestamosVencidos as $prestamo)
                                    <option value="{{ $prestamo->id }}" @selected((string) old('prestamo_id', $datosPagoMulta['prestamo_id'] ?? '') === (string) $prestamo->id)>
                                        #{{ $prestamo->id }} - {{ $prestamo->user?->name }} / {{ $prestamo->libro?->titulo }} ({{ $prestamo->estado }})
                                    </option>
                                @empty
                                    <option value="">No hay prestamos vencidos</option>
                                @endforelse
                            </select>
                            @error('prestamo_id')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="pago_rol">Rol</label>
                            <select id="pago_rol" name="rol">
                                @foreach (['docente' => 'Docente', 'estudiante' => 'Estudiante'] as $valor => $texto)
                                    <option value="{{ $valor }}" @selected(old('rol', $datosPagoMulta['rol'] ?? 'docente') === $valor)>
                                        {{ $texto }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="pago_fecha_pago">Fecha de pago</label>
                            <input
                                id="pago_fecha_pago"
                                name="fecha_pago"
                                type="date"
                                value="{{ old('fecha_pago', $datosPagoMulta['fecha_pago'] ?? '2026-06-25') }}"
                            >
                        </div>

                        <div class="form-field">
                            <label for="pago_fecha_actual">Fecha actual</label>
                            <input
                                id="pago_fecha_actual"
                                name="fecha_actual"
                                type="date"
                                value="{{ old('fecha_actual', $datosPagoMulta['fecha_actual'] ?? '2026-06-30') }}"
                            >
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="button" type="submit" @disabled(!$tablasDisponibles || $prestamosVencidos->isEmpty())>
                            Registrar pago de multa
                        </button>
                    </div>
                </form>

                @isset($resultadoPagoMulta)
                    <div class="result-card {{ $resultadoClase($resultadoPagoMulta['estado'], $resultadoPagoMulta['valido']) }}">
                        <h2>Resultado: <span class="badge {{ $estadoBadge($resultadoPagoMulta['estado']) }}">{{ $resultadoPagoMulta['estado'] }}</span></h2>
                        <p>Multa pagada: <strong>S/ {{ number_format($resultadoPagoMulta['multa_pagada'], 2) }}</strong></p>
                        <p>Fecha de pago: <strong>{{ $resultadoPagoMulta['fecha_pago'] ?? 'N/A' }}</strong></p>
                        <p>Fecha de habilitacion: <strong>{{ $resultadoPagoMulta['fecha_habilitacion'] ?? 'N/A' }}</strong></p>
                        <p>Puede prestar: <strong>{{ $resultadoPagoMulta['puede_prestar'] ? 'si' : 'no' }}</strong></p>
                        <p>{{ $resultadoPagoMulta['mensaje'] }}</p>
                    </div>
                @endisset
            </section>

            <section class="panel">
                <h2 style="margin-top: 0;">B. Intentar pr&eacute;stamo durante penalizaci&oacute;n</h2>
                <p class="page-description">Valide si un usuario puede registrar un nuevo prestamo.</p>

                <form method="POST" action="{{ route('pagos.intentar-prestamo') }}">
                    @csrf
                    <input type="hidden" name="escenario" value="penalizado">

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="penalizado_user_id">Usuario penalizado</label>
                            <select id="penalizado_user_id" name="user_id" @disabled(!$tablasDisponibles || $usuarios->isEmpty())>
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" @selected((string) old('user_id', $datosIntentoPrestamo['user_id'] ?? '') === (string) $usuario->id)>
                                        {{ $usuario->name }} ({{ $usuario->rol ?? 'sin rol' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="penalizado_libro_id">Libro disponible</label>
                            <select id="penalizado_libro_id" name="libro_id" @disabled(!$tablasDisponibles || $librosDisponibles->isEmpty())>
                                @forelse ($librosDisponibles as $libro)
                                    <option value="{{ $libro->id }}">{{ $libro->titulo }} (#{{ $libro->id }})</option>
                                @empty
                                    <option value="">No hay libros disponibles</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="penalizado_rol">Rol</label>
                            <select id="penalizado_rol" name="rol">
                                <option value="docente">Docente</option>
                                <option value="estudiante">Estudiante</option>
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="penalizado_fecha">Fecha de pr&eacute;stamo</label>
                            <input id="penalizado_fecha" name="fecha_prestamo" type="date" value="2026-06-30">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="button" type="submit" @disabled(!$tablasDisponibles || $librosDisponibles->isEmpty())>
                            Intentar pr&eacute;stamo
                        </button>
                    </div>
                </form>

                @if (($escenarioIntentoPrestamo ?? null) === 'penalizado' && isset($resultadoIntentoPrestamo))
                    <div class="result-card {{ $resultadoIntentoPrestamo['valido'] ? 'result-valid' : 'result-invalid' }}">
                        <h2>{{ $resultadoIntentoPrestamo['valido'] ? 'Prestamo aceptado' : 'Prestamo rechazado' }}</h2>
                        <p>Puede prestar: <strong>{{ ($resultadoIntentoPrestamo['puede_prestar'] ?? false) ? 'si' : 'no' }}</strong></p>
                        <p>{{ $resultadoIntentoPrestamo['mensaje'] }}</p>
                    </div>
                @endif
            </section>

            <section class="panel">
                <h2 style="margin-top: 0;">C. Intentar pr&eacute;stamo despu&eacute;s de penalizaci&oacute;n</h2>
                <p class="page-description">Registre una nueva operacion cuando el usuario ya esta habilitado.</p>

                <form method="POST" action="{{ route('pagos.intentar-prestamo') }}">
                    @csrf
                    <input type="hidden" name="escenario" value="habilitado">

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="habilitado_user_id">Usuario habilitado</label>
                            <select id="habilitado_user_id" name="user_id" @disabled(!$tablasDisponibles || $usuarios->isEmpty())>
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}">{{ $usuario->name }} ({{ $usuario->rol ?? 'sin rol' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="habilitado_libro_id">Libro disponible</label>
                            <select id="habilitado_libro_id" name="libro_id" @disabled(!$tablasDisponibles || $librosDisponibles->isEmpty())>
                                @forelse ($librosDisponibles as $libro)
                                    <option value="{{ $libro->id }}">{{ $libro->titulo }} (#{{ $libro->id }})</option>
                                @empty
                                    <option value="">No hay libros disponibles</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="habilitado_rol">Rol</label>
                            <select id="habilitado_rol" name="rol">
                                <option value="docente">Docente</option>
                                <option value="estudiante">Estudiante</option>
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="habilitado_fecha">Fecha de pr&eacute;stamo</label>
                            <input id="habilitado_fecha" name="fecha_prestamo" type="date" value="2026-07-16">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="button" type="submit" @disabled(!$tablasDisponibles || $librosDisponibles->isEmpty())>
                            Intentar pr&eacute;stamo habilitado
                        </button>
                    </div>
                </form>

                @if (($escenarioIntentoPrestamo ?? null) === 'habilitado' && isset($resultadoIntentoPrestamo))
                    <div class="result-card {{ $resultadoIntentoPrestamo['valido'] ? 'result-valid' : 'result-invalid' }}">
                        <h2>{{ $resultadoIntentoPrestamo['valido'] ? 'Prestamo aceptado' : 'Prestamo rechazado' }}</h2>
                        <p>Fecha de devolucion: <strong>{{ $resultadoIntentoPrestamo['fecha_devolucion'] ?? 'N/A' }}</strong></p>
                        <p>Estado del libro: <strong>{{ $resultadoIntentoPrestamo['libro_estado'] ?? 'N/A' }}</strong></p>
                        <p>{{ $resultadoIntentoPrestamo['mensaje'] }}</p>
                    </div>
                @endif
            </section>
        </div>

        <aside class="panel">
            <h2 style="margin-top: 0;">Estados</h2>
            <div class="case-grid">
                <article class="case-card">
                    <h3>Pago</h3>
                    <p><span class="badge badge-blue">PAGADO</span> <span class="badge badge-blue">PAGADA</span></p>
                </article>
                <article class="case-card">
                    <h3>Penalizacion</h3>
                    <p><span class="badge badge-warn">PENALIZADO</span></p>
                </article>
                <article class="case-card">
                    <h3>Habilitacion</h3>
                    <p><span class="badge badge-green">HABILITADO</span></p>
                </article>
            </div>
        </aside>
    </section>
    @else
        <section class="panel" style="margin-top: 18px;">
            <h2 style="margin-top: 0;">Modo lectura</h2>
            <p class="page-description">Su rol permite consultar pagos y penalizaciones sin modificar registros.</p>
        </section>
    @endif

    <section class="panel" id="pagos-registrados" style="margin-top: 18px;">
        <h2 style="margin-top: 0;">Pagos registrados</h2>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Prestamo</th>
                        <th>Monto</th>
                        <th>Fecha pago</th>
                        <th>Fecha habilitacion</th>
                        <th>Estado</th>
                        @if ($puedeEditar)
                            <th>Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pagos as $pago)
                        <tr>
                            <td>{{ $pago->id }}</td>
                            <td>{{ $pago->user?->name ?? 'N/A' }}</td>
                            <td>#{{ $pago->prestamo_id ?? 'N/A' }}</td>
                            <td>S/ {{ number_format((float) $pago->monto, 2) }}</td>
                            <td>{{ $pago->fecha_pago ?? 'N/A' }}</td>
                            <td>{{ $pago->fecha_habilitacion ?? 'N/A' }}</td>
                            <td><span class="badge {{ $estadoBadge($pago->estado) }}">{{ $pago->estado }}</span></td>
                            @if ($puedeEditar)
                                <td>
                                    <div class="action-row">
                                        <a class="button-small" href="#editar-pago-{{ $pago->id }}">Editar</a>
                                        <form method="POST" action="{{ route('pagos.destroy', $pago) }}">
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
                            <td colspan="{{ $puedeEditar ? 8 : 7 }}">No hay pagos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if ($puedeEditar)
        @foreach ($pagos as $pago)
            <section class="modal" id="editar-pago-{{ $pago->id }}" aria-label="Editar pago {{ $pago->id }}">
            <div class="modal-dialog">
                <div class="modal-header">
                    <div>
                        <h2 class="modal-title">Editar pago</h2>
                        <p class="page-description">Registro #{{ $pago->id }}</p>
                    </div>
                    <a class="modal-close" href="#pagos-registrados">X</a>
                </div>

                <form method="POST" action="{{ route('pagos.update', $pago) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="pago_user_{{ $pago->id }}">Usuario</label>
                            <select id="pago_user_{{ $pago->id }}" name="user_id">
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" @selected((int) old('user_id', $pago->user_id) === $usuario->id)>
                                        {{ $usuario->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="pago_prestamo_{{ $pago->id }}">Prestamo</label>
                            <select id="pago_prestamo_{{ $pago->id }}" name="prestamo_id">
                                <option value="">Sin prestamo</option>
                                @foreach ($prestamos as $prestamo)
                                    <option value="{{ $prestamo->id }}" @selected((int) old('prestamo_id', $pago->prestamo_id) === $prestamo->id)>
                                        #{{ $prestamo->id }} - {{ $prestamo->user?->name }} / {{ $prestamo->libro?->titulo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="pago_monto_{{ $pago->id }}">Monto</label>
                            <input id="pago_monto_{{ $pago->id }}" name="monto" type="number" step="0.01" min="0" value="{{ old('monto', $pago->monto) }}">
                        </div>

                        <div class="form-field">
                            <label for="pago_fecha_{{ $pago->id }}">Fecha pago</label>
                            <input id="pago_fecha_{{ $pago->id }}" name="fecha_pago" type="date" value="{{ old('fecha_pago', $pago->fecha_pago) }}">
                        </div>

                        <div class="form-field">
                            <label for="pago_habilitacion_{{ $pago->id }}">Fecha habilitacion</label>
                            <input id="pago_habilitacion_{{ $pago->id }}" name="fecha_habilitacion" type="date" value="{{ old('fecha_habilitacion', $pago->fecha_habilitacion) }}">
                        </div>

                        <div class="form-field">
                            <label for="pago_estado_{{ $pago->id }}">Estado</label>
                            <select id="pago_estado_{{ $pago->id }}" name="estado">
                                @foreach (['PENDIENTE', 'PAGADO', 'PAGADA', 'PENALIZADO', 'HABILITADO'] as $estado)
                                    <option value="{{ $estado }}" @selected(old('estado', $pago->estado) === $estado)>{{ $estado }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-actions action-row">
                        <button class="button" type="submit">Guardar cambios</button>
                        <a class="button-secondary" href="#pagos-registrados">Cancelar</a>
                    </div>
                </form>
            </div>
            </section>
        @endforeach
    @endif

    <section class="grid" id="pagos-prestamos" aria-label="Tablas de apoyo">
        <article class="panel">
            <h2 style="margin-top: 0;">Pr&eacute;stamos</h2>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Libro</th>
                            <th>Devolucion</th>
                            <th>Estado</th>
                            @if ($puedeEditar)
                                <th>Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prestamos as $prestamo)
                            <tr>
                                <td>{{ $prestamo->id }}</td>
                                <td>{{ $prestamo->user?->name ?? 'N/A' }}</td>
                                <td>{{ $prestamo->libro?->titulo ?? 'N/A' }}</td>
                                <td>{{ $prestamo->fecha_devolucion }}</td>
                                <td><span class="badge {{ $estadoBadge($prestamo->estado) }}">{{ $prestamo->estado }}</span></td>
                                @if ($puedeEditar)
                                    <td>
                                        <div class="action-row">
                                            <a class="button-small" href="#editar-prestamo-pago-{{ $prestamo->id }}">Editar</a>
                                            <form method="POST" action="{{ route('prestamos.destroy', $prestamo) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="button-danger" type="submit">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </article>

        <article class="panel">
            <h2 style="margin-top: 0;">Libros</h2>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titulo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($libros as $libro)
                            <tr>
                                <td>{{ $libro->id }}</td>
                                <td>{{ $libro->titulo }}</td>
                                <td><span class="badge {{ $estadoBadge($libro->estado) }}">{{ $libro->estado }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </article>
    </section>

    @if ($puedeEditar)
        @foreach ($prestamos as $prestamo)
            <section class="modal" id="editar-prestamo-pago-{{ $prestamo->id }}" aria-label="Editar prestamo {{ $prestamo->id }}">
            <div class="modal-dialog">
                <div class="modal-header">
                    <div>
                        <h2 class="modal-title">Editar prestamo</h2>
                        <p class="page-description">Registro #{{ $prestamo->id }}</p>
                    </div>
                    <a class="modal-close" href="#pagos-prestamos">X</a>
                </div>

                <form method="POST" action="{{ route('prestamos.update', $prestamo) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="pago_prestamo_user_{{ $prestamo->id }}">Usuario</label>
                            <select id="pago_prestamo_user_{{ $prestamo->id }}" name="user_id">
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" @selected((int) old('user_id', $prestamo->user_id) === $usuario->id)>
                                        {{ $usuario->name }} ({{ $usuario->rol ?? 'sin rol' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="pago_prestamo_libro_{{ $prestamo->id }}">Libro</label>
                            <select id="pago_prestamo_libro_{{ $prestamo->id }}" name="libro_id">
                                @foreach ($libros as $libro)
                                    <option value="{{ $libro->id }}" @selected((int) old('libro_id', $prestamo->libro_id) === $libro->id)>
                                        {{ $libro->titulo }} ({{ $libro->estado }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="pago_prestamo_fecha_{{ $prestamo->id }}">Fecha prestamo</label>
                            <input id="pago_prestamo_fecha_{{ $prestamo->id }}" name="fecha_prestamo" type="date" value="{{ old('fecha_prestamo', $prestamo->fecha_prestamo) }}">
                        </div>

                        <div class="form-field">
                            <label for="pago_devolucion_fecha_{{ $prestamo->id }}">Fecha devolucion</label>
                            <input id="pago_devolucion_fecha_{{ $prestamo->id }}" name="fecha_devolucion" type="date" value="{{ old('fecha_devolucion', $prestamo->fecha_devolucion) }}">
                        </div>

                        <div class="form-field">
                            <label for="pago_prestamo_estado_{{ $prestamo->id }}">Estado</label>
                            <select id="pago_prestamo_estado_{{ $prestamo->id }}" name="estado">
                                @foreach (['ACTIVO', 'VENCIDO', 'PENALIZADO', 'PAGADO', 'FINALIZADO'] as $estado)
                                    <option value="{{ $estado }}" @selected(old('estado', $prestamo->estado) === $estado)>{{ $estado }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-actions action-row">
                        <button class="button" type="submit">Guardar cambios</button>
                        <a class="button-secondary" href="#pagos-prestamos">Cancelar</a>
                    </div>
                </form>
            </div>
            </section>
        @endforeach
    @endif
@endsection
