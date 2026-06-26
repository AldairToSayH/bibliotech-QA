@extends('layouts.app')

@section('title', 'BiblioTech - Docente')
@section('section-title', 'Dashboard docente')

@section('content')
    <section class="panel">
        <h1 class="page-title">Bienvenido a la Biblioteca Principal</h1>
        <p class="page-description">Hola, {{ $usuarioPortal['name'] ?? 'Docente' }}. Tu registro se completo con exito.</p>
    </section>

    <section class="grid" aria-label="Informacion del docente">
        <article class="card">
            <h2>Codigo institucional</h2>
            <p>{{ $usuarioPortal['codigo_institucional'] ?? 'No registrado' }}</p>
            <div class="card-footer">
                <span class="badge badge-blue">Docente</span>
            </div>
        </article>

        <article class="card card-accent-green">
            <h2>Rol</h2>
            <p>Docente</p>
            <div class="card-footer">
                <span class="badge badge-green">HABILITADO</span>
            </div>
        </article>

        <article class="card">
            <h2>Accesos</h2>
            <p>Consulta catalogo, estado de cuenta y pagos.</p>
            <div class="card-footer action-row">
                <a class="button-small" href="{{ route('docente.catalogo') }}">Catalogo</a>
                <a class="button-small" href="{{ route('docente.estado-cuenta') }}">Estado</a>
                <a class="button-small" href="{{ route('docente.pagar-deuda') }}">Pagar</a>
            </div>
        </article>
    </section>
@endsection
