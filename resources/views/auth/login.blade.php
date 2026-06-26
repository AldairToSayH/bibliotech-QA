@extends('layouts.app')

@section('title', 'BiblioTech - Acceso')

@section('content')
    <section class="content-split">
        <article class="panel">
            <h1 class="page-title">BiblioTech</h1>
            <p class="page-description">Panel administrativo de biblioteca.</p>

            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 18px;">
                <span class="badge badge-blue">Laravel</span>
                <span class="badge badge-green">Biblioteca</span>
                <span class="badge badge-warn">MySQL</span>
            </div>
        </article>

        <article class="panel">
            <h2 style="margin-top: 0;">Iniciar sesion</h2>

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf

                <div class="form-field" style="margin-top: 18px;">
                    <label for="email">Correo</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        autofocus
                    >
                    @error('email')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field" style="margin-top: 14px;">
                    <label for="password">Contrasena</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                    >
                    @error('password')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-actions">
                    <button class="button" type="submit">Ingresar</button>
                </div>
            </form>
        </article>
    </section>
@endsection
