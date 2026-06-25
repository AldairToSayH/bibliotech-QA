@extends('layouts.app')

@section('title', 'BiblioTech - Morosidad')

@section('content')
    @php
        $casos = [
            ['codigo' => 'CP11', 'titulo' => 'Docente moroso', 'detalle' => 'Rol: docente | Devolucion: 2026-06-15 | Actual: 2026-06-25 | Resultado: S/ 50.00, MOROSO'],
            ['codigo' => 'CP12', 'titulo' => 'Estudiante moroso', 'detalle' => 'Rol: estudiante | Devolucion: 2026-06-15 | Actual: 2026-06-25 | Resultado: S/ 20.00, MOROSO'],
            ['codigo' => 'CP13', 'titulo' => 'Sin retraso', 'detalle' => 'Rol: estudiante | Devolucion: 2026-06-25 | Actual: 2026-06-25 | Resultado: S/ 0.00, AL_DIA'],
            ['codigo' => 'CP14', 'titulo' => 'Rol invalido', 'detalle' => 'Rol: invitado | Resultado: ERROR'],
            ['codigo' => 'CP15', 'titulo' => 'Pago detiene multa', 'detalle' => 'Docente | Devolucion: 2026-06-15 | Pago: 2026-06-25 | Actual: 2026-06-30 | Multa congelada: S/ 50.00'],
            ['codigo' => 'CP16', 'titulo' => 'Penalizacion activa', 'detalle' => 'Pago: 2026-06-25 | Actual: 2026-06-30 | Resultado: PENALIZADO, faltan 16 dias'],
            ['codigo' => 'CP17', 'titulo' => 'Usuario habilitado', 'detalle' => 'Pago: 2026-06-25 | Actual: 2026-07-16 | Resultado: HABILITADO, puede prestar'],
        ];

        $estadoClase = function (?string $estado): string {
            return match ($estado) {
                'AL_DIA', 'HABILITADO' => 'result-valid',
                'PAGADA' => 'result-info',
                'PENALIZADO' => 'result-warning',
                default => 'result-invalid',
            };
        };

        $badgeClase = function (?string $estado): string {
            return match ($estado) {
                'AL_DIA', 'HABILITADO' => 'badge-green',
                'PAGADA' => 'badge-blue',
                'PENALIZADO' => 'badge-warn',
                default => 'badge-red',
            };
        };
    @endphp

    <section class="panel">
        <h1 class="page-title">Morosidad, Pago y Penalizaci&oacute;n</h1>
        <p class="page-description">
            Pantalla academica para demostrar calculo de multas, corte de acumulacion por pago
            y penalizacion posterior usando <strong>MorosidadService</strong>.
        </p>
    </section>

    <section class="content-split">
        <div style="display: grid; gap: 18px;">
            <section class="panel">
                <h2 style="margin-top: 0;">A. C&aacute;lculo de multa acumulada</h2>
                <p class="page-description">Demuestra CP11, CP12, CP13 y CP14.</p>

                <form method="POST" action="{{ route('morosidad.calcular-multa') }}">
                    @csrf

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="multa_rol">Rol</label>
                            <select id="multa_rol" name="rol">
                                @foreach (['docente' => 'Docente', 'estudiante' => 'Estudiante', 'invitado' => 'Invitado'] as $valor => $texto)
                                    <option value="{{ $valor }}" @selected(old('rol', $datosMulta['rol'] ?? 'docente') === $valor)>
                                        {{ $texto }}
                                    </option>
                                @endforeach
                            </select>
                            @error('rol')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="multa_fecha_devolucion">Fecha de devoluci&oacute;n</label>
                            <input
                                id="multa_fecha_devolucion"
                                name="fecha_devolucion"
                                type="date"
                                value="{{ old('fecha_devolucion', $datosMulta['fecha_devolucion'] ?? '2026-06-15') }}"
                            >
                            @error('fecha_devolucion')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="multa_fecha_actual">Fecha actual</label>
                            <input
                                id="multa_fecha_actual"
                                name="fecha_actual"
                                type="date"
                                value="{{ old('fecha_actual', $datosMulta['fecha_actual'] ?? '2026-06-25') }}"
                            >
                            @error('fecha_actual')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="button" type="submit">Calcular multa</button>
                    </div>
                </form>

                @isset($resultadoMulta)
                    <div class="result-card {{ $estadoClase($resultadoMulta['estado']) }}">
                        <h2>
                            Resultado:
                            <span class="badge {{ $badgeClase($resultadoMulta['estado']) }}">{{ $resultadoMulta['estado'] }}</span>
                        </h2>
                        <p>Valido: <strong>{{ $resultadoMulta['valido'] ? 'si' : 'no' }}</strong></p>
                        <p>Rol: <strong>{{ $resultadoMulta['rol'] ?? 'N/A' }}</strong></p>
                        <p>Dias de retraso: <strong>{{ $resultadoMulta['dias_retraso'] }}</strong></p>
                        <p>Tarifa diaria: <strong>S/ {{ number_format($resultadoMulta['tarifa_diaria'], 2) }}</strong></p>
                        <p>Multa total: <strong>S/ {{ number_format($resultadoMulta['multa_total'], 2) }}</strong></p>
                        <p>{{ $resultadoMulta['mensaje'] }}</p>
                    </div>
                @endisset
            </section>

            <section class="panel">
                <h2 style="margin-top: 0;">B. Pago de multa y corte de acumulaci&oacute;n</h2>
                <p class="page-description">Demuestra CP15: la multa deja de acumularse al pagar.</p>

                <form method="POST" action="{{ route('morosidad.calcular-pago') }}">
                    @csrf

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="pago_rol">Rol</label>
                            <select id="pago_rol" name="rol">
                                @foreach (['docente' => 'Docente', 'estudiante' => 'Estudiante', 'invitado' => 'Invitado'] as $valor => $texto)
                                    <option value="{{ $valor }}" @selected(old('rol', $datosPago['rol'] ?? 'docente') === $valor)>
                                        {{ $texto }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="pago_fecha_devolucion">Fecha de devoluci&oacute;n</label>
                            <input
                                id="pago_fecha_devolucion"
                                name="fecha_devolucion"
                                type="date"
                                value="{{ old('fecha_devolucion', $datosPago['fecha_devolucion'] ?? '2026-06-15') }}"
                            >
                        </div>

                        <div class="form-field">
                            <label for="pago_fecha_pago">Fecha de pago</label>
                            <input
                                id="pago_fecha_pago"
                                name="fecha_pago"
                                type="date"
                                value="{{ old('fecha_pago', $datosPago['fecha_pago'] ?? '2026-06-25') }}"
                            >
                        </div>

                        <div class="form-field">
                            <label for="pago_fecha_actual">Fecha actual posterior</label>
                            <input
                                id="pago_fecha_actual"
                                name="fecha_actual"
                                type="date"
                                value="{{ old('fecha_actual', $datosPago['fecha_actual'] ?? '2026-06-30') }}"
                            >
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="button" type="submit">Calcular multa pagada</button>
                    </div>
                </form>

                @isset($resultadoPago)
                    <div class="result-card {{ $estadoClase($resultadoPago['estado']) }}">
                        <h2>
                            Resultado:
                            <span class="badge {{ $badgeClase($resultadoPago['estado']) }}">{{ $resultadoPago['estado'] }}</span>
                        </h2>
                        <p>Dias de retraso hasta pago: <strong>{{ $resultadoPago['dias_retraso_hasta_pago'] }}</strong></p>
                        <p>Multa pagada: <strong>S/ {{ number_format($resultadoPago['multa_pagada'], 2) }}</strong></p>
                        <p>Multa actual: <strong>S/ {{ number_format($resultadoPago['multa_actual'], 2) }}</strong></p>
                        <p>Sigue acumulando: <strong>{{ $resultadoPago['multa_sigue_acumulando'] ? 'si' : 'no' }}</strong></p>
                        <p>{{ $resultadoPago['mensaje'] }}</p>
                    </div>
                @endisset
            </section>

            <section class="panel">
                <h2 style="margin-top: 0;">C. Penalizaci&oacute;n posterior al pago</h2>
                <p class="page-description">Demuestra CP16 y CP17 con una penalizacion de 21 dias.</p>

                <form method="POST" action="{{ route('morosidad.calcular-penalizacion') }}">
                    @csrf

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="penalizacion_fecha_pago">Fecha de pago</label>
                            <input
                                id="penalizacion_fecha_pago"
                                name="fecha_pago"
                                type="date"
                                value="{{ old('fecha_pago', $datosPenalizacion['fecha_pago'] ?? '2026-06-25') }}"
                            >
                        </div>

                        <div class="form-field">
                            <label for="penalizacion_fecha_actual">Fecha actual</label>
                            <input
                                id="penalizacion_fecha_actual"
                                name="fecha_actual"
                                type="date"
                                value="{{ old('fecha_actual', $datosPenalizacion['fecha_actual'] ?? '2026-06-30') }}"
                            >
                        </div>

                        <div class="form-field">
                            <label for="dias_penalizacion">Dias de penalizacion</label>
                            <input
                                id="dias_penalizacion"
                                name="dias_penalizacion"
                                type="number"
                                min="1"
                                value="{{ old('dias_penalizacion', $datosPenalizacion['dias_penalizacion'] ?? 21) }}"
                            >
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="button" type="submit">Calcular penalizaci&oacute;n</button>
                    </div>
                </form>

                @isset($resultadoPenalizacion)
                    <div class="result-card {{ $estadoClase($resultadoPenalizacion['estado']) }}">
                        <h2>
                            Resultado:
                            <span class="badge {{ $badgeClase($resultadoPenalizacion['estado']) }}">{{ $resultadoPenalizacion['estado'] }}</span>
                        </h2>
                        <p>Fecha de habilitacion: <strong>{{ $resultadoPenalizacion['fecha_habilitacion'] }}</strong></p>
                        <p>Dias restantes: <strong>{{ $resultadoPenalizacion['dias_restantes'] }}</strong></p>
                        <p>Puede prestar: <strong>{{ $resultadoPenalizacion['puede_prestar'] ? 'si' : 'no' }}</strong></p>
                        <p>{{ $resultadoPenalizacion['mensaje'] }}</p>
                    </div>
                @endisset
            </section>
        </div>

        <aside class="panel">
            <h2 style="margin-top: 0;">Casos de prueba r&aacute;pidos</h2>
            <p class="page-description">Datos guia para demostrar CP11-CP17 desde esta interfaz.</p>

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
@endsection
