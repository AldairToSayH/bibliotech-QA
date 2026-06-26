@extends('layouts.app')

@section('title', 'BiblioTech - Registro')
@section('section-title', 'Registro')

@section('content')
    @php
        $puedeEditar = request()->is('registro') || request()->is('admin/*') || in_array(Auth::user()?->rol, ['admin', 'editor'], true);
        $hoy = \Illuminate\Support\Carbon::today();

        $estadoUsuario = function ($usuario) use ($hoy): string {
            $pagoPenalizado = $usuario->pagos
                ->where('estado', 'PENALIZADO')
                ->sortByDesc('fecha_habilitacion')
                ->first();

            if ($pagoPenalizado && $pagoPenalizado->fecha_habilitacion && $hoy->lt(\Illuminate\Support\Carbon::parse($pagoPenalizado->fecha_habilitacion))) {
                return 'PENALIZADO';
            }

            if ($pagoPenalizado && $pagoPenalizado->fecha_habilitacion && $hoy->gte(\Illuminate\Support\Carbon::parse($pagoPenalizado->fecha_habilitacion))) {
                return 'HABILITADO';
            }

            $tieneVencidos = $usuario->prestamos
                ->whereIn('estado', ['ACTIVO', 'VENCIDO', 'PENALIZADO'])
                ->filter(fn ($prestamo) => $prestamo->fecha_devolucion && \Illuminate\Support\Carbon::parse($prestamo->fecha_devolucion)->lt($hoy))
                ->isNotEmpty();

            return $tieneVencidos ? 'MOROSO' : 'ACTIVO';
        };

        $estadoBadge = function (string $estado): string {
            return match ($estado) {
                'ACTIVO', 'HABILITADO', 'AL_DIA' => 'badge-green',
                'PENALIZADO' => 'badge-warn',
                'MOROSO' => 'badge-red',
                default => 'badge-blue',
            };
        };
    @endphp

    <section class="panel">
        <h1 class="page-title">Registro alumno/docente</h1>
        <p class="page-description">
            Registro publico para estudiantes y docentes con validacion institucional.
        </p>
    </section>

    @unless ($tablaDisponible)
        <section class="notice">
            La tabla de usuarios no esta disponible. Ejecute las migraciones antes de usar este modulo.
        </section>
    @endunless

    @isset($mensaje)
        <section class="result-card result-valid">
            <h2>{{ $mensaje }}</h2>
        </section>
    @endisset

    <section class="content-split">
        @if ($puedeEditar)
            <div class="panel">
                <h2 style="margin-top: 0;">Registrar usuario</h2>
                <p class="page-description">
                    El rol se detecta automaticamente desde el codigo institucional y el gafete fisico.
                </p>

                <form method="POST" action="{{ route('registro.validar') }}">
                    @csrf

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="nombres">Nombres</label>
                            <input id="nombres" name="nombres" type="text" value="{{ old('nombres', $datos['nombres'] ?? '') }}" placeholder="Ej. Juan Perez">
                            @error('nombres')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="email">Correo</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $datos['email'] ?? '') }}" placeholder="usuario@bibliotech.test">
                            @error('email')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="telefono">Telefono</label>
                            <input id="telefono" name="telefono" type="text" value="{{ old('telefono', $datos['telefono'] ?? '') }}" placeholder="Opcional">
                            @error('telefono')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="dni">DNI</label>
                            <input id="dni" name="dni" type="text" value="{{ old('dni', $datos['dni'] ?? '') }}" placeholder="8 digitos">
                            <span class="form-help">Debe contener 8 digitos.</span>
                            @error('dni')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="codigo_institucional">Codigo institucional</label>
                            <input id="codigo_institucional" name="codigo_institucional" type="text" value="{{ old('codigo_institucional', $datos['codigo_institucional'] ?? '') }}" placeholder="Ej. 474698202">
                            <span class="form-help">Prefijo 4 para estudiante, 7 para docente; los ultimos 8 digitos deben coincidir con el DNI.</span>
                            @error('codigo_institucional')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="codigo_gafete">Codigo de gafete</label>
                            <input id="codigo_gafete" name="codigo_gafete" type="text" value="{{ old('codigo_gafete', $datos['codigo_gafete'] ?? '') }}" placeholder="443 o 444">
                            <span class="form-help">444 para estudiante, 443 para docente.</span>
                            @error('codigo_gafete')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="button" type="submit" @disabled(!$tablaDisponible)>Registrar usuario</button>
                    </div>
                </form>

                @isset($resultado)
                    <div class="result-card result-invalid">
                        <h2>Registro rechazado</h2>
                        <p>{{ $resultado['mensaje'] }}</p>
                    </div>
                @endisset
            </div>
        @else
            <div class="panel">
                <h2 style="margin-top: 0;">Modo lectura</h2>
                <p class="page-description">Su rol permite consultar usuarios sin crear registros.</p>
            </div>
        @endif

        <aside class="panel">
            <h2 style="margin-top: 0;">Reglas institucionales</h2>
            <ul class="rules-list">
                <li>El codigo institucional tiene 9 digitos.</li>
                <li>Prefijo <span class="code-pill">4</span> identifica estudiantes.</li>
                <li>Prefijo <span class="code-pill">7</span> identifica docentes.</li>
                <li>El codigo debe terminar con el DNI del usuario.</li>
                <li>El gafete fisico debe corresponder al rol detectado.</li>
            </ul>
        </aside>
    </section>

@endsection
