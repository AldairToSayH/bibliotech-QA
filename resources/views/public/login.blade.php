@extends('layouts.app')

@section('title', 'BiblioTech - Login')

@section('content')
    <section class="content-split">
        <article class="panel">
            <h1 class="page-title">Login</h1>
            <p class="page-description">
                Ingresa como alumno o docente usando el correo y codigo institucional registrados.
            </p>

            <div class="grid" style="grid-template-columns: 1fr; margin-top: 18px;">
                <div class="result-card result-info">
                    <h2>Acceso institucional</h2>
                    <p>Este acceso es solo para alumnos y docentes. El administrador entra por Acceso Admin.</p>
                </div>
            </div>
        </article>

        <article class="panel">
            <h2 style="margin-top: 0;">Ingresar al portal</h2>

            <form method="POST" action="{{ route('portal.login.attempt') }}">
                @csrf

                <div class="form-field" style="margin-top: 18px;">
                    <label for="email">Correo</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" autofocus>
                    @error('email')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field" style="margin-top: 14px;">
                    <label for="codigo_institucional">Codigo institucional</label>
                    <input id="codigo_institucional" name="codigo_institucional" type="text" value="{{ old('codigo_institucional') }}">
                    @error('codigo_institucional')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-actions action-row">
                    <button class="button" type="submit">Ingresar</button>
                    <a class="button-secondary" href="{{ route('registro.index') }}">Registrarse</a>
                </div>
            </form>
        </article>
    </section>
@endsection
