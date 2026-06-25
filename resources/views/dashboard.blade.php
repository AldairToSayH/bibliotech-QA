@extends('layouts.app')

@section('title', 'BiblioTech - Dashboard')
@section('section-title', 'Dashboard')

@section('content')
    <section class="panel">
        <h1 class="page-title">Dashboard</h1>
        <p class="page-description">Resumen operativo del sistema.</p>
    </section>

    <section class="grid" aria-label="Indicadores principales">
        <article class="card card-accent-green">
            <h2>Libros</h2>
            <p>Total registrado en el catalogo.</p>
            <div class="card-footer">
                <span class="badge badge-blue">{{ $totalLibros }}</span>
            </div>
        </article>

        <article class="card card-accent-green">
            <h2>Disponibles</h2>
            <p>Libros listos para prestamo.</p>
            <div class="card-footer">
                <span class="badge badge-green">{{ $librosDisponibles }}</span>
            </div>
        </article>

        <article class="card card-accent-warn">
            <h2>Prestamos activos</h2>
            <p>Operaciones vigentes.</p>
            <div class="card-footer">
                <span class="badge badge-warn">{{ $prestamosActivos }}</span>
            </div>
        </article>

        <article class="card">
            <h2>Pagos</h2>
            <p>Pagos registrados.</p>
            <div class="card-footer">
                <span class="badge badge-blue">{{ $pagosRegistrados }}</span>
            </div>
        </article>

        <article class="card">
            <h2>Usuarios</h2>
            <p>Cuentas administradas.</p>
            <div class="card-footer">
                <span class="badge badge-blue">{{ $usuarios }}</span>
            </div>
        </article>

        <article class="card card-accent-green">
            <h2>Pruebas</h2>
            <p>Suite automatizada.</p>
            <div class="card-footer">
                <span class="badge badge-green">22 passed</span>
            </div>
        </article>
    </section>

    <section class="grid" aria-label="Accesos rapidos">
        <article class="card">
            <h2>Libros</h2>
            <p>Catalogo y disponibilidad.</p>
            <div class="card-footer">
                <a class="button" href="{{ route('libros.index') }}">Administrar</a>
            </div>
        </article>

        <article class="card card-accent-warn">
            <h2>Prestamos</h2>
            <p>Registro y control de prestamos.</p>
            <div class="card-footer">
                <a class="button" href="{{ route('prestamos.index') }}">Administrar</a>
            </div>
        </article>

        <article class="card">
            <h2>Pagos</h2>
            <p>Pagos y penalizaciones.</p>
            <div class="card-footer">
                <a class="button" href="{{ route('pagos.index') }}">Administrar</a>
            </div>
        </article>
    </section>
@endsection
