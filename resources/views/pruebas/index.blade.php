@extends('layouts.app')

@section('title', 'BiblioTech - Panel QA')
@section('section-title', 'Panel QA')

@section('content')
    @php
        $tipoBadge = function (string $categoria): string {
            return match ($categoria) {
                'unitaria' => 'badge-blue',
                'integracion' => 'badge-warn',
                'humo' => 'badge-green',
                default => 'badge-red',
            };
        };
    @endphp

    <section class="panel">
        <h1 class="page-title">Panel QA BiblioTech</h1>
        <p class="page-description">
            Resumen visual de los CP01-CP21 implementados con PHPUnit para validar calidad de software.
        </p>

        <div class="status">
            <span>Estado general:</span>
            <span>{{ $resumen['estado'] }}</span>
        </div>
    </section>

    <section class="grid" aria-label="Resumen de calidad">
        <article class="card">
            <h2>Total de casos</h2>
            <p>Matriz completa documentada para el proyecto academico.</p>
            <div class="card-footer">
                <span class="badge badge-blue">{{ $resumen['total'] }} casos</span>
            </div>
        </article>

        <article class="card">
            <h2>Unitarias</h2>
            <p>Validan reglas de negocio aisladas en servicios de dominio.</p>
            <div class="card-footer">
                <span class="badge badge-blue">{{ $resumen['unitarias'] }} unitarias</span>
            </div>
        </article>

        <article class="card">
            <h2>Integracion</h2>
            <p>Validan servicios, modelos y persistencia con base de datos de testing.</p>
            <div class="card-footer">
                <span class="badge badge-warn">{{ $resumen['integracion'] }} integracion</span>
            </div>
        </article>

        <article class="card">
            <h2>Humo</h2>
            <p>Confirma componentes esenciales del sistema y entorno de pruebas.</p>
            <div class="card-footer">
                <span class="badge badge-green">{{ $resumen['humo'] }} humo</span>
            </div>
        </article>

        <article class="card">
            <h2>Estado general</h2>
            <p>La suite CP01-CP21 se mantiene como regresion basica del sistema.</p>
            <div class="card-footer">
                <span class="badge badge-green">{{ $resumen['estado'] }}</span>
            </div>
        </article>

        <article class="card">
            <h2>Cobertura funcional</h2>
            <p>Registro, prestamos, morosidad, pagos, penalizacion y smoke test.</p>
            <div class="card-footer">
                <span class="badge badge-blue">HU01-HU03 + Smoke</span>
            </div>
        </article>
    </section>

    <section class="panel" style="margin-top: 22px;">
        <h2 style="margin-top: 0;">Leyenda</h2>
        <p class="page-description">Clasificacion visual usada en la matriz de QA.</p>
        <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 16px;">
            <span class="badge badge-blue">Unitaria</span>
            <span class="badge badge-warn">Integracion</span>
            <span class="badge badge-green">Humo / PASSED</span>
        </div>
    </section>

    @foreach ($casosPorHistoria as $historia => $casos)
        <section class="panel" style="margin-top: 22px;">
            <h2 style="margin-top: 0;">{{ $historia }}</h2>
            <p class="page-description">
                {{ $casos->count() }} caso{{ $casos->count() === 1 ? '' : 's' }} documentado{{ $casos->count() === 1 ? '' : 's' }}.
            </p>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Caso de prueba</th>
                            <th>Tipo</th>
                            <th>Entrada</th>
                            <th>Resultado esperado</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($casos as $caso)
                            <tr>
                                <td><strong>{{ $caso['id'] }}</strong></td>
                                <td>
                                    <strong>{{ $caso['caso'] }}</strong>
                                    <div class="form-help">{{ $caso['modulo'] }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $tipoBadge($caso['categoria']) }}">{{ $caso['tipo'] }}</span>
                                </td>
                                <td>{{ $caso['entrada'] }}</td>
                                <td>{{ $caso['resultado'] }}</td>
                                <td><span class="badge badge-green">{{ $caso['estado'] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endforeach

    <section class="content-split">
        <article class="panel">
            <h2 style="margin-top: 0;">Comando de ejecucion</h2>
            <p class="page-description">
                Todas las pruebas del sistema se ejecutan desde PHPUnit mediante <strong>php artisan test</strong>.
            </p>

            <div class="notice">
                <span class="code-pill">php artisan test</span>
            </div>
        </article>

        <aside class="panel">
            <h2 style="margin-top: 0;">Evidencia para exposicion</h2>
            <div class="case-grid">
                <article class="case-card">
                    <h3>FOTO - Resultado general de php artisan test</h3>
                    <p class="page-description">Captura sugerida de la terminal con 22 pruebas pasando.</p>
                </article>
                <article class="case-card">
                    <h3>FOTO - Estructura de archivos tests/</h3>
                    <p class="page-description">Captura sugerida de tests/Unit y tests/Feature.</p>
                </article>
                <article class="case-card">
                    <h3>FOTO - Dashboard del sistema</h3>
                    <p class="page-description">Captura sugerida del panel principal de BiblioTech.</p>
                </article>
            </div>
        </aside>
    </section>
@endsection
