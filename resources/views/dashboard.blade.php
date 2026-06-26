@extends('layouts.app')

@section('title', 'BiblioTech - Administracion')
@section('section-title', 'Panel de administracion')

@section('content')
    @php
        $estadoBadge = function (?string $estado): string {
            return match ($estado) {
                'DISPONIBLE', 'ACTIVO', 'HABILITADO', 'FINALIZADO' => 'badge-green',
                'PRESTADO', 'PENALIZADO', 'VENCIDO' => 'badge-warn',
                'PAGADO', 'PAGADA' => 'badge-blue',
                default => 'badge-red',
            };
        };

        $kpis = [
            ['titulo' => 'Usuarios', 'valor' => $metricas['usuarios'], 'detalle' => $metricas['estudiantes'] . ' estudiantes / ' . $metricas['docentes'] . ' docentes', 'clase' => 'card'],
            ['titulo' => 'Libros', 'valor' => $metricas['totalLibros'], 'detalle' => 'Catalogo activo', 'clase' => 'card'],
            ['titulo' => 'Disponibles', 'valor' => $metricas['librosDisponibles'], 'detalle' => $metricas['disponibilidad'] . '% de disponibilidad', 'clase' => 'card card-accent-green'],
            ['titulo' => 'Prestados', 'valor' => $metricas['librosPrestados'], 'detalle' => $metricas['ocupacion'] . '% de ocupacion', 'clase' => 'card card-accent-warn'],
            ['titulo' => 'Prestamos activos', 'valor' => $metricas['prestamosActivos'], 'detalle' => 'Operaciones vigentes', 'clase' => 'card card-accent-green'],
            ['titulo' => 'Vencidos', 'valor' => $metricas['prestamosVencidos'], 'detalle' => $metricas['usuariosMorosos'] . ' usuarios con seguimiento', 'clase' => 'card card-accent-red'],
            ['titulo' => 'Pagos', 'valor' => $metricas['pagosRegistrados'], 'detalle' => 'S/ ' . number_format($metricas['montoPagado'], 2) . ' registrados', 'clase' => 'card'],
            ['titulo' => 'Cuentas internas', 'valor' => $metricas['cuentasInternas'], 'detalle' => 'Administracion y soporte', 'clase' => 'card'],
        ];
    @endphp

    <style>
        .admin-hero {
            align-items: flex-start;
            display: flex;
            gap: 18px;
            justify-content: space-between;
        }

        .admin-hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-end;
        }

        .metric-value {
            font-size: 34px;
            font-weight: 800;
            letter-spacing: 0;
            line-height: 1;
            margin: 12px 0 8px;
        }

        .ops-grid {
            display: grid;
            gap: 18px;
            grid-template-columns: minmax(0, 1.15fr) minmax(320px, 0.85fr);
            margin-top: 22px;
        }

        .health-row {
            display: grid;
            gap: 8px;
            margin-top: 16px;
        }

        .health-label {
            align-items: center;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            font-size: 14px;
            font-weight: 700;
        }

        .health-bar {
            background: #e5e7eb;
            border-radius: 999px;
            height: 10px;
            overflow: hidden;
        }

        .health-bar span {
            background: #2563eb;
            display: block;
            height: 100%;
        }

        .health-bar.is-green span { background: #15803d; }
        .health-bar.is-warn span { background: #c2410c; }

        .empty-state {
            border: 1px dashed var(--line);
            border-radius: 8px;
            color: var(--muted);
            margin-top: 18px;
            padding: 18px;
        }

        @media (max-width: 980px) {
            .admin-hero,
            .ops-grid {
                display: grid;
                grid-template-columns: 1fr;
            }

            .admin-hero-actions {
                justify-content: flex-start;
            }
        }
    </style>

    <section class="panel admin-hero">
        <div>
            <h1 class="page-title">Panel de administracion</h1>
            <p class="page-description">
                Estado operativo de la biblioteca al {{ $hoy }}. Las metricas se calculan desde registros reales del sistema.
            </p>
        </div>

        <div class="admin-hero-actions">
            <a class="button" href="{{ route('libros.index') }}">Nuevo libro</a>
            <a class="button-secondary" href="{{ route('prestamos.index') }}">Registrar prestamo</a>
            <a class="button-secondary" href="{{ route('pagos.index') }}">Registrar pago</a>
        </div>
    </section>

    <section class="grid" aria-label="Indicadores operativos">
        @foreach ($kpis as $kpi)
            <article class="{{ $kpi['clase'] }}">
                <h2>{{ $kpi['titulo'] }}</h2>
                <div class="metric-value">{{ $kpi['valor'] }}</div>
                <p>{{ $kpi['detalle'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="ops-grid">
        <article class="panel">
            <h2 style="margin-top: 0;">Salud del catalogo</h2>
            <p class="page-description">Disponibilidad y uso del material registrado.</p>

            <div class="health-row">
                <div class="health-label">
                    <span>Disponibilidad</span>
                    <span>{{ $metricas['disponibilidad'] }}%</span>
                </div>
                <div class="health-bar is-green">
                    <span style="width: {{ $metricas['disponibilidad'] }}%;"></span>
                </div>
            </div>

            <div class="health-row">
                <div class="health-label">
                    <span>Ocupacion por prestamos</span>
                    <span>{{ $metricas['ocupacion'] }}%</span>
                </div>
                <div class="health-bar is-warn">
                    <span style="width: {{ $metricas['ocupacion'] }}%;"></span>
                </div>
            </div>

            <div class="grid" style="grid-template-columns: repeat(3, minmax(0, 1fr)); margin-top: 18px;">
                <a class="button-secondary" href="{{ route('admin.usuarios.index') }}">Usuarios</a>
                <a class="button-secondary" href="{{ route('morosidad.index') }}">Morosidad</a>
                <a class="button-secondary" href="{{ route('pagos.index') }}">Pagos</a>
            </div>
        </article>

        <article class="panel">
            <h2 style="margin-top: 0;">Atencion prioritaria</h2>
            <p class="page-description">Prestamos vencidos ordenados por mayor urgencia.</p>

            @if ($prestamosVencidos->isEmpty())
                <div class="empty-state">No hay prestamos vencidos pendientes.</div>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Libro</th>
                                <th>Devolucion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($prestamosVencidos as $prestamo)
                                <tr>
                                    <td>{{ $prestamo->user?->name ?? 'N/A' }}</td>
                                    <td>{{ $prestamo->libro?->titulo ?? 'N/A' }}</td>
                                    <td><span class="badge badge-red">{{ $prestamo->fecha_devolucion }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </article>
    </section>

    <section class="ops-grid">
        <article class="panel">
            <h2 style="margin-top: 0;">Prestamos recientes</h2>
            <p class="page-description">Ultimas operaciones registradas en circulacion.</p>

            @if ($prestamosRecientes->isEmpty())
                <div class="empty-state">Aun no hay prestamos registrados.</div>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Libro</th>
                                <th>Devolucion</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($prestamosRecientes as $prestamo)
                                <tr>
                                    <td>{{ $prestamo->id }}</td>
                                    <td>{{ $prestamo->user?->name ?? 'N/A' }}</td>
                                    <td>{{ $prestamo->libro?->titulo ?? 'N/A' }}</td>
                                    <td>{{ $prestamo->fecha_devolucion ?? 'N/A' }}</td>
                                    <td><span class="badge {{ $estadoBadge($prestamo->estado) }}">{{ $prestamo->estado }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </article>

        <article class="panel">
            <h2 style="margin-top: 0;">Devoluciones proximas</h2>
            <p class="page-description">Prestamos que vencen en los siguientes 7 dias.</p>

            @if ($devolucionesProximas->isEmpty())
                <div class="empty-state">No hay devoluciones programadas para los proximos dias.</div>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Libro</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($devolucionesProximas as $prestamo)
                                <tr>
                                    <td>{{ $prestamo->user?->name ?? 'N/A' }}</td>
                                    <td>{{ $prestamo->libro?->titulo ?? 'N/A' }}</td>
                                    <td><span class="badge badge-blue">{{ $prestamo->fecha_devolucion }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </article>
    </section>

    <section class="panel" style="margin-top: 22px;">
        <h2 style="margin-top: 0;">Pagos recientes</h2>
        <p class="page-description">Ultimos pagos de multa registrados en el sistema.</p>

        @if ($pagosRecientes->isEmpty())
            <div class="empty-state">Aun no hay pagos registrados.</div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Prestamo</th>
                            <th>Monto</th>
                            <th>Fecha pago</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pagosRecientes as $pago)
                            <tr>
                                <td>{{ $pago->id }}</td>
                                <td>{{ $pago->user?->name ?? 'N/A' }}</td>
                                <td>#{{ $pago->prestamo_id ?? 'N/A' }}</td>
                                <td>S/ {{ number_format((float) $pago->monto, 2) }}</td>
                                <td>{{ $pago->fecha_pago ?? 'N/A' }}</td>
                                <td><span class="badge {{ $estadoBadge($pago->estado) }}">{{ $pago->estado }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
