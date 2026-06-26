@extends('layouts.app')

@section('title', 'BiblioTech - Estado de Cuenta Alumno')
@section('section-title', 'Estado de cuenta')

@section('content')
    <section class="panel">
        <h1 class="page-title">Estado de cuenta</h1>
        <p class="page-description">Hola, {{ $usuarioPortal['name'] ?? 'Alumno' }}. Aqui se mostraran tus prestamos, vencimientos y pagos registrados.</p>
    </section>

    <section class="grid" aria-label="Resumen de cuenta">
        <article class="card card-accent-green">
            <h2>Prestamos activos</h2>
            <p>Operaciones vigentes del alumno.</p>
            <div class="card-footer"><span class="badge badge-green">0</span></div>
        </article>

        <article class="card">
            <h2>Deuda actual</h2>
            <p>Multas pendientes por retraso.</p>
            <div class="card-footer"><span class="badge badge-blue">S/ 0.00</span></div>
        </article>
    </section>
@endsection
