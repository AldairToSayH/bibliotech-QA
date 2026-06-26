@extends('layouts.app')

@section('title', 'BiblioTech - Morosidad')
@section('section-title', 'Morosidad')

@section('content')
    @php
        $puedeEditar = request()->is('admin/*') || in_array(Auth::user()?->rol, ['admin', 'editor'], true);

        $estadoClase = function (?string $estado): string {
            return match ($estado) {
                'AL_DIA', 'HABILITADO' => 'result-valid',
                'PAGADA', 'PAGADO' => 'result-info',
                'PENALIZADO' => 'result-warning',
                default => 'result-invalid',
            };
        };

        $badgeClase = function (?string $estado): string {
            return match ($estado) {
                'AL_DIA', 'HABILITADO' => 'badge-green',
                'PAGADA', 'PAGADO' => 'badge-blue',
                'PENALIZADO', 'PRESTADO' => 'badge-warn',
                default => 'badge-red',
            };
        };

        $multaPrestamo = function ($prestamo) use ($hoy): array {
            $rol = $prestamo->user?->rol;
            $tarifa = $rol === 'docente' ? 5.00 : 2.00;
            $dias = $prestamo->fecha_devolucion
                ? \Illuminate\Support\Carbon::parse($prestamo->fecha_devolucion)->diffInDays(\Illuminate\Support\Carbon::parse($hoy), false)
                : 0;
            $dias = max(0, (int) $dias);

            return [
                'dias' => $dias,
                'tarifa' => $tarifa,
                'monto' => $dias * $tarifa,
                'estado' => $dias > 0 ? 'MOROSO' : 'AL_DIA',
            ];
        };
    @endphp

    <section class="panel">
        <h1 class="page-title">Morosidad</h1>
        <p class="page-description">
            Seguimiento de prestamos vencidos, calculo de multas y estado de penalizacion.
        </p>
    </section>

    @unless ($tablasDisponibles)
        <section class="notice">
            Las tablas necesarias no estan disponibles. Ejecute las migraciones antes de usar este modulo.
        </section>
    @endunless

    <section class="grid" aria-label="Resumen de morosidad">
        <article class="card card-accent-red">
            <h2>Prestamos vencidos</h2>
            <p>Registros con fecha de devolucion anterior a hoy.</p>
            <div class="card-footer">
                <span class="badge badge-red">{{ $prestamosVencidos->count() }}</span>
            </div>
        </article>

        <article class="card">
            <h2>Pagos registrados</h2>
            <p>Multas canceladas o en penalizacion.</p>
            <div class="card-footer">
                <span class="badge badge-blue">{{ $pagos->count() }}</span>
            </div>
        </article>

        <article class="card card-accent-warn">
            <h2>Fecha de control</h2>
            <p>Referencia usada para calcular retrasos.</p>
            <div class="card-footer">
                <span class="badge badge-warn">{{ $hoy }}</span>
            </div>
        </article>
    </section>

    <section class="panel" style="margin-top: 18px;">
        <h2 style="margin-top: 0;">Prestamos vencidos</h2>
        <p class="page-description">Listado de usuarios con deuda acumulada por retraso.</p>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Libro</th>
                        <th>Devolucion</th>
                        <th>Dias</th>
                        <th>Tarifa</th>
                        <th>Multa</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($prestamosVencidos as $prestamo)
                        @php($multa = $multaPrestamo($prestamo))
                        <tr>
                            <td>{{ $prestamo->id }}</td>
                            <td><strong>{{ $prestamo->user?->name ?? 'N/A' }}</strong></td>
                            <td>{{ ucfirst($prestamo->user?->rol ?? 'sin rol') }}</td>
                            <td>{{ $prestamo->libro?->titulo ?? 'N/A' }}</td>
                            <td>{{ $prestamo->fecha_devolucion ?? 'N/A' }}</td>
                            <td>{{ $multa['dias'] }}</td>
                            <td>S/ {{ number_format($multa['tarifa'], 2) }}</td>
                            <td><strong>S/ {{ number_format($multa['monto'], 2) }}</strong></td>
                            <td><span class="badge {{ $badgeClase($multa['estado']) }}">{{ $multa['estado'] }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">No hay prestamos vencidos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if ($puedeEditar)
        <section class="content-split">
            <div style="display: grid; gap: 18px;">
                <section class="panel">
                    <h2 style="margin-top: 0;">Calcular multa acumulada</h2>
                    <p class="page-description">Ingrese rol y fechas para obtener dias de retraso y monto acumulado.</p>

                    <form method="POST" action="{{ route('morosidad.calcular-multa') }}">
                        @csrf

                        <div class="form-grid">
                            <div class="form-field">
                                <label for="multa_rol">Rol</label>
                                <select id="multa_rol" name="rol">
                                    @foreach (['docente' => 'Docente', 'estudiante' => 'Estudiante'] as $valor => $texto)
                                        <option value="{{ $valor }}" @selected(old('rol', $datosMulta['rol'] ?? 'docente') === $valor)>
                                            {{ $texto }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="multa_fecha_devolucion">Fecha de devolucion</label>
                                <input id="multa_fecha_devolucion" name="fecha_devolucion" type="date" value="{{ old('fecha_devolucion', $datosMulta['fecha_devolucion'] ?? now()->subDays(10)->toDateString()) }}">
                            </div>

                            <div class="form-field">
                                <label for="multa_fecha_actual">Fecha actual</label>
                                <input id="multa_fecha_actual" name="fecha_actual" type="date" value="{{ old('fecha_actual', $datosMulta['fecha_actual'] ?? now()->toDateString()) }}">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button class="button" type="submit">Calcular multa</button>
                        </div>
                    </form>

                    @isset($resultadoMulta)
                        <div class="result-card {{ $estadoClase($resultadoMulta['estado']) }}">
                            <h2>Estado: <span class="badge {{ $badgeClase($resultadoMulta['estado']) }}">{{ $resultadoMulta['estado'] }}</span></h2>
                            <p>Dias de retraso: <strong>{{ $resultadoMulta['dias_retraso'] }}</strong></p>
                            <p>Tarifa diaria: <strong>S/ {{ number_format($resultadoMulta['tarifa_diaria'], 2) }}</strong></p>
                            <p>Multa total: <strong>S/ {{ number_format($resultadoMulta['multa_total'], 2) }}</strong></p>
                            <p>{{ $resultadoMulta['mensaje'] }}</p>
                        </div>
                    @endisset
                </section>

                <section class="panel">
                    <h2 style="margin-top: 0;">Simular pago de multa</h2>
                    <p class="page-description">Calcule el monto que queda congelado al registrar un pago.</p>

                    <form method="POST" action="{{ route('morosidad.calcular-pago') }}">
                        @csrf

                        <div class="form-grid">
                            <div class="form-field">
                                <label for="pago_rol">Rol</label>
                                <select id="pago_rol" name="rol">
                                    @foreach (['docente' => 'Docente', 'estudiante' => 'Estudiante'] as $valor => $texto)
                                        <option value="{{ $valor }}" @selected(old('rol', $datosPago['rol'] ?? 'docente') === $valor)>
                                            {{ $texto }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="pago_fecha_devolucion">Fecha de devolucion</label>
                                <input id="pago_fecha_devolucion" name="fecha_devolucion" type="date" value="{{ old('fecha_devolucion', $datosPago['fecha_devolucion'] ?? now()->subDays(10)->toDateString()) }}">
                            </div>

                            <div class="form-field">
                                <label for="pago_fecha_pago">Fecha de pago</label>
                                <input id="pago_fecha_pago" name="fecha_pago" type="date" value="{{ old('fecha_pago', $datosPago['fecha_pago'] ?? now()->toDateString()) }}">
                            </div>

                            <div class="form-field">
                                <label for="pago_fecha_actual">Fecha actual posterior</label>
                                <input id="pago_fecha_actual" name="fecha_actual" type="date" value="{{ old('fecha_actual', $datosPago['fecha_actual'] ?? now()->addDays(5)->toDateString()) }}">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button class="button" type="submit">Calcular pago</button>
                        </div>
                    </form>

                    @isset($resultadoPago)
                        <div class="result-card {{ $estadoClase($resultadoPago['estado']) }}">
                            <h2>Estado: <span class="badge {{ $badgeClase($resultadoPago['estado']) }}">{{ $resultadoPago['estado'] }}</span></h2>
                            <p>Dias hasta pago: <strong>{{ $resultadoPago['dias_retraso_hasta_pago'] }}</strong></p>
                            <p>Multa congelada: <strong>S/ {{ number_format($resultadoPago['multa_pagada'], 2) }}</strong></p>
                            <p>Sigue acumulando: <strong>{{ $resultadoPago['multa_sigue_acumulando'] ? 'si' : 'no' }}</strong></p>
                            <p>{{ $resultadoPago['mensaje'] }}</p>
                        </div>
                    @endisset
                </section>
            </div>

            <aside class="panel">
                <h2 style="margin-top: 0;">Penalizacion posterior al pago</h2>
                <p class="page-description">Revise si el usuario puede volver a prestar libros tras el periodo definido.</p>

                <form method="POST" action="{{ route('morosidad.calcular-penalizacion') }}">
                    @csrf

                    <div class="form-grid" style="grid-template-columns: 1fr;">
                        <div class="form-field">
                            <label for="penalizacion_fecha_pago">Fecha de pago</label>
                            <input id="penalizacion_fecha_pago" name="fecha_pago" type="date" value="{{ old('fecha_pago', $datosPenalizacion['fecha_pago'] ?? now()->toDateString()) }}">
                        </div>

                        <div class="form-field">
                            <label for="penalizacion_fecha_actual">Fecha actual</label>
                            <input id="penalizacion_fecha_actual" name="fecha_actual" type="date" value="{{ old('fecha_actual', $datosPenalizacion['fecha_actual'] ?? now()->addDays(5)->toDateString()) }}">
                        </div>

                        <div class="form-field">
                            <label for="dias_penalizacion">Dias de penalizacion</label>
                            <input id="dias_penalizacion" name="dias_penalizacion" type="number" min="1" value="{{ old('dias_penalizacion', $datosPenalizacion['dias_penalizacion'] ?? 21) }}">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="button" type="submit">Calcular habilitacion</button>
                    </div>
                </form>

                @isset($resultadoPenalizacion)
                    <div class="result-card {{ $estadoClase($resultadoPenalizacion['estado']) }}">
                        <h2>Estado: <span class="badge {{ $badgeClase($resultadoPenalizacion['estado']) }}">{{ $resultadoPenalizacion['estado'] }}</span></h2>
                        <p>Fecha de habilitacion: <strong>{{ $resultadoPenalizacion['fecha_habilitacion'] }}</strong></p>
                        <p>Dias restantes: <strong>{{ $resultadoPenalizacion['dias_restantes'] }}</strong></p>
                        <p>Puede prestar: <strong>{{ $resultadoPenalizacion['puede_prestar'] ? 'si' : 'no' }}</strong></p>
                        <p>{{ $resultadoPenalizacion['mensaje'] }}</p>
                    </div>
                @endisset
            </aside>
        </section>
    @else
        <section class="panel" style="margin-top: 18px;">
            <h2 style="margin-top: 0;">Modo lectura</h2>
            <p class="page-description">Su rol permite consultar morosidad sin ejecutar calculos.</p>
        </section>
    @endif
@endsection
