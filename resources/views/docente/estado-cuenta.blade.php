@extends('layouts.app')

@section('title', 'BiblioTech - Estado de Cuenta Docente')
@section('section-title', 'Estado de cuenta')

@section('content')
    <section class="panel">
        <h1 class="page-title">Estado de cuenta</h1>
        <p class="page-description">Hola, {{ $usuarioPortal['name'] ?? 'Docente' }}. Aqui se mostraran tus prestamos, vencimientos, multas y pagos.</p>
    </section>

    <section class="grid" aria-label="Resumen de cuenta docente">
        <article class="card card-accent-green">
            <h2>Prestamos activos</h2>
            <p>Operaciones vigentes del docente.</p>
            <div class="card-footer"><span class="badge badge-green">0</span></div>
        </article>

        <article class="card card-accent-warn">
            <h2>Deuda actual</h2>
            <p>Multas pendientes por retraso.</p>
            <div class="card-footer"><span class="badge badge-warn">S/ 0.00</span></div>
        </article>
    </section>
@endsection
