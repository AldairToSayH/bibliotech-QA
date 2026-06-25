@extends('layouts.app')

@section('title', 'BiblioTech - Registro')
@section('section-title', 'Registro')

@section('content')
    @php
        $puedeEditar = in_array(Auth::user()?->rol, ['admin', 'editor'], true);

        $casos = [
            [
                'codigo' => 'CP01',
                'nombre' => 'Estudiante valido',
                'dni' => '74698202',
                'codigo_institucional' => '474698202',
                'codigo_gafete' => '444',
            ],
            [
                'codigo' => 'CP02',
                'nombre' => 'Docente valido',
                'dni' => '25863008',
                'codigo_institucional' => '725863008',
                'codigo_gafete' => '443',
            ],
            [
                'codigo' => 'CP03',
                'nombre' => 'DNI no coincide',
                'dni' => '11111111',
                'codigo_institucional' => '474698202',
                'codigo_gafete' => '444',
            ],
            [
                'codigo' => 'CP04',
                'nombre' => 'Prefijo invalido',
                'dni' => '74698202',
                'codigo_institucional' => '974698202',
                'codigo_gafete' => '444',
            ],
            [
                'codigo' => 'CP05',
                'nombre' => 'Gafete no corresponde',
                'dni' => '74698202',
                'codigo_institucional' => '474698202',
                'codigo_gafete' => '443',
            ],
        ];
    @endphp

    <section class="panel">
        <h1 class="page-title">Registro y Validaci&oacute;n de Identidad</h1>
        <p class="page-description">
            Demuestra CP01-CP05 mediante validacion de identidad institucional usando
            <strong>RegistroService</strong>.
        </p>
    </section>

    <section class="content-split">
        @if ($puedeEditar)
        <div class="panel">
            <h2 style="margin-top: 0;">Datos del registro</h2>
            <p class="page-description">
                Complete los campos y ejecute la validacion. No se guarda informacion en base de datos;
                esta pantalla solo demuestra la regla de negocio.
            </p>

            <form method="POST" action="{{ route('registro.validar') }}">
                @csrf

                <div class="form-grid">
                    <div class="form-field">
                        <label for="nombres">Nombres</label>
                        <input
                            id="nombres"
                            name="nombres"
                            type="text"
                            value="{{ old('nombres', $datos['nombres'] ?? '') }}"
                            placeholder="Ej. Juan Estudiante"
                        >
                        @error('nombres')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="dni">DNI</label>
                        <input
                            id="dni"
                            name="dni"
                            type="text"
                            value="{{ old('dni', $datos['dni'] ?? '') }}"
                            placeholder="8 digitos"
                        >
                        <span class="form-help">Debe contener 8 digitos.</span>
                        @error('dni')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="codigo_institucional">C&oacute;digo institucional</label>
                        <input
                            id="codigo_institucional"
                            name="codigo_institucional"
                            type="text"
                            value="{{ old('codigo_institucional', $datos['codigo_institucional'] ?? '') }}"
                            placeholder="Ej. 474698202"
                        >
                        <span class="form-help">Inicia con 4 o 7 y termina con el DNI.</span>
                        @error('codigo_institucional')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="codigo_gafete">C&oacute;digo de gafete</label>
                        <input
                            id="codigo_gafete"
                            name="codigo_gafete"
                            type="text"
                            value="{{ old('codigo_gafete', $datos['codigo_gafete'] ?? '') }}"
                            placeholder="443 o 444"
                        >
                        <span class="form-help">444 = estudiante, 443 = docente.</span>
                        @error('codigo_gafete')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <ul class="rules-list">
                    <li>Prefijo <span class="code-pill">4</span> = estudiante.</li>
                    <li>Prefijo <span class="code-pill">7</span> = docente.</li>
                    <li>Gafete <span class="code-pill">444</span> = estudiante.</li>
                    <li>Gafete <span class="code-pill">443</span> = docente.</li>
                    <li>Los ultimos 8 digitos del codigo institucional deben coincidir con el DNI.</li>
                </ul>

                <div class="form-actions">
                    <button class="button" type="submit">Validar registro</button>
                </div>
            </form>

            @isset($resultado)
                @if ($resultado['valido'])
                    <div class="result-card result-valid">
                        <h2>Registro v&aacute;lido</h2>
                        <p>Rol detectado: <strong>{{ $resultado['rol'] }}</strong>.</p>
                    </div>
                @else
                    <div class="result-card result-invalid">
                        <h2>Registro rechazado</h2>
                        <p>{{ $resultado['mensaje'] }}</p>
                    </div>
                @endif
            @endisset
        </div>
        @else
            <div class="panel">
                <h2 style="margin-top: 0;">Modo lectura</h2>
                <p class="page-description">Su rol permite consultar el modulo sin ejecutar validaciones.</p>
            </div>
        @endif

        <aside class="panel">
            <h2 style="margin-top: 0;">Casos de prueba r&aacute;pidos</h2>
            <p class="page-description">
                Copie manualmente estos datos en el formulario para demostrar CP01-CP05.
            </p>

            <div class="case-grid">
                @foreach ($casos as $caso)
                    <article class="case-card">
                        <h3>{{ $caso['codigo'] }} - {{ $caso['nombre'] }}</h3>
                        <dl>
                            <dt>DNI</dt>
                            <dd><span class="code-pill">{{ $caso['dni'] }}</span></dd>
                            <dt>C&oacute;digo</dt>
                            <dd><span class="code-pill">{{ $caso['codigo_institucional'] }}</span></dd>
                            <dt>Gafete</dt>
                            <dd><span class="code-pill">{{ $caso['codigo_gafete'] }}</span></dd>
                        </dl>
                    </article>
                @endforeach
            </div>
        </aside>
    </section>
@endsection
