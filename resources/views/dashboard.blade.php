@extends('layouts.app')

@section('title', 'BiblioTech - Dashboard')

@section('content')
    <section class="panel">
        <h1 class="page-title">Panel principal BiblioTech</h1>
        <p class="page-description">
            Vista general de los modulos usados para demostrar reglas de negocio, pruebas unitarias,
            pruebas de integracion, humo y regresion basica con PHPUnit.
        </p>

        <div class="status">
            <span>Pruebas pasadas:</span>
            <span>CP01-CP21 validadas en la suite automatizada.</span>
        </div>
    </section>

    <section class="grid" aria-label="Modulos principales">
        <article class="card">
            <h2>Registro</h2>
            <p>Validacion de identidad institucional para estudiantes y docentes.</p>
            <div class="card-footer">
                <a class="button" href="{{ route('registro.index') }}">Abrir modulo</a>
            </div>
        </article>

        <article class="card">
            <h2>Libros y prestamos</h2>
            <p>Disponibilidad de libros, registro de prestamos y exclusividad.</p>
            <div class="card-footer">
                <a class="button" href="{{ route('libros.index') }}">Ver libros</a>
            </div>
        </article>

        <article class="card">
            <h2>Prestamos</h2>
            <p>Plazos por rol, control de estado y bloqueo por penalizacion activa.</p>
            <div class="card-footer">
                <a class="button" href="{{ route('prestamos.index') }}">Gestionar</a>
            </div>
        </article>

        <article class="card">
            <h2>Morosidad</h2>
            <p>Calculo de multa acumulada, estados de mora y reglas por rol.</p>
            <div class="card-footer">
                <a class="button" href="{{ route('morosidad.index') }}">Consultar</a>
            </div>
        </article>

        <article class="card">
            <h2>Pagos y penalizacion</h2>
            <p>Pago de multas, congelamiento de deuda y habilitacion posterior.</p>
            <div class="card-footer">
                <a class="button" href="{{ route('pagos.index') }}">Revisar</a>
            </div>
        </article>

        <article class="card">
            <h2>Panel de pruebas</h2>
            <p>Resumen visual del alcance de pruebas implementadas en el proyecto.</p>
            <div class="card-footer">
                <a class="button" href="{{ route('pruebas.index') }}">Ver pruebas</a>
            </div>
        </article>
    </section>

    <section class="panel" style="margin-top: 18px;">
        <p class="page-description">
            Sistema preparado para demostrar pruebas unitarias, integracion, humo y regresion basica con PHPUnit.
        </p>
    </section>
@endsection
