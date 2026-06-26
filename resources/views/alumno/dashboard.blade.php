@extends('layouts.app')

@section('title', 'BiblioTech - Alumno')
@section('section-title', 'Dashboard alumno')

@section('content')
    <section class="panel">
        <h1 class="page-title">Bienvenido a la Biblioteca Principal</h1>
        <p class="page-description">Hola, {{ $usuarioPortal['name'] ?? 'Alumno' }}. Tu registro se completo con exito.</p>
    </section>

    <section class="grid" aria-label="Informacion del alumno">
        <article class="card">
            <h2>Codigo institucional</h2>
            <p>{{ $usuarioPortal['codigo_institucional'] ?? 'No registrado' }}</p>
            <div class="card-footer">
                <span class="badge badge-blue">Alumno</span>
            </div>
        </article>

        <article class="card card-accent-green">
            <h2>Rol</h2>
            <p>Estudiante</p>
            <div class="card-footer">
                <span class="badge badge-green">HABILITADO</span>
            </div>
        </article>

        <article class="card">
            <h2>Accesos</h2>
            <p>Consulta libros y revisa tu estado de cuenta.</p>
            <div class="card-footer action-row">
                <a class="button-small" href="{{ route('alumno.catalogo') }}">Catalogo</a>
                <a class="button-small" href="{{ route('alumno.estado-cuenta') }}">Estado</a>
            </div>
        </article>
    </section>
@endsection
