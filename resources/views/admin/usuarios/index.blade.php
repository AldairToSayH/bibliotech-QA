@extends('layouts.app')

@section('title', 'BiblioTech - Admin Usuarios')
@section('section-title', 'Usuarios')

@section('content')
    @php
        $hoy = \Illuminate\Support\Carbon::today();

        $estadoUsuario = function ($usuario) use ($hoy): string {
            $pagoPenalizado = $usuario->pagos
                ->where('estado', 'PENALIZADO')
                ->sortByDesc('fecha_habilitacion')
                ->first();

            if ($pagoPenalizado && $pagoPenalizado->fecha_habilitacion && $hoy->lt(\Illuminate\Support\Carbon::parse($pagoPenalizado->fecha_habilitacion))) {
                return 'PENALIZADO';
            }

            $tieneVencidos = $usuario->prestamos
                ->whereIn('estado', ['ACTIVO', 'VENCIDO', 'PENALIZADO'])
                ->filter(fn ($prestamo) => $prestamo->fecha_devolucion && \Illuminate\Support\Carbon::parse($prestamo->fecha_devolucion)->lt($hoy))
                ->isNotEmpty();

            return $tieneVencidos ? 'MOROSO' : 'ACTIVO';
        };

        $badgeEstado = fn (string $estado): string => match ($estado) {
            'ACTIVO', 'HABILITADO' => 'badge-green',
            'PENALIZADO' => 'badge-warn',
            'MOROSO' => 'badge-red',
            default => 'badge-blue',
        };

        $badgeRol = fn (?string $rol): string => match ($rol) {
            'admin' => 'badge-blue',
            'editor' => 'badge-warn',
            'visualizador' => 'badge-green',
            'docente' => 'badge-warn',
            'estudiante' => 'badge-green',
            default => 'badge-red',
        };
    @endphp

    <section class="panel">
        <h1 class="page-title">Usuarios</h1>
        <p class="page-description">Listado administrativo de alumnos, docentes y cuentas internas.</p>
    </section>

    @isset($mensaje)
        <section class="result-card result-valid">
            <h2>{{ $mensaje }}</h2>
        </section>
    @endisset

    <section class="content-split">
        <article class="panel">
            <h2 style="margin-top: 0;">Crear cuenta interna</h2>
            <p class="page-description">Alta exclusiva para administradores, editores o visualizadores del panel.</p>

            <form method="POST" action="{{ route('admin.usuarios.store') }}">
                @csrf

                <div class="form-grid">
                    <div class="form-field">
                        <label for="name">Nombre</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}">
                        @error('name') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-field">
                        <label for="email">Correo</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}">
                        @error('email') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-field">
                        <label for="password">Contrasena</label>
                        <input id="password" name="password" type="password">
                        @error('password') <span class="error-text">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-field">
                        <label for="rol">Rol interno</label>
                        <select id="rol" name="rol">
                            <option value="admin" @selected(old('rol') === 'admin')>Administrador</option>
                            <option value="editor" @selected(old('rol') === 'editor')>Editor</option>
                            <option value="visualizador" @selected(old('rol', 'visualizador') === 'visualizador')>Visualizador</option>
                        </select>
                        @error('rol') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button class="button" type="submit">Crear cuenta interna</button>
                </div>
            </form>
        </article>

        <aside class="panel">
            <h2 style="margin-top: 0;">Acciones futuras</h2>
            <div class="case-grid">
                <article class="case-card">
                    <h3>Ver perfil</h3>
                    <p class="page-description">Detalle de historial, prestamos y pagos por usuario.</p>
                </article>
                <article class="case-card">
                    <h3>Editar datos</h3>
                    <p class="page-description">Correccion administrativa de correo, telefono o estado.</p>
                </article>
                <article class="case-card">
                    <h3>Bloquear o habilitar</h3>
                    <p class="page-description">Control manual de acceso a prestamos en una fase posterior.</p>
                </article>
            </div>
        </aside>
    </section>

    <section class="panel" id="usuarios" style="margin-top: 18px;">
        <h2 style="margin-top: 0;">Usuarios registrados</h2>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Codigo institucional</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $usuario)
                        @php($estado = $estadoUsuario($usuario))
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td><strong>{{ $usuario->name }}</strong></td>
                            <td>{{ $usuario->email }}</td>
                            <td><span class="badge {{ $badgeRol($usuario->rol) }}">{{ strtoupper($usuario->rol ?? 'SIN ROL') }}</span></td>
                            <td>{{ $usuario->codigo_institucion ?? 'No aplica' }}</td>
                            <td><span class="badge {{ $badgeEstado($estado) }}">{{ $estado }}</span></td>
                            <td>
                                <div class="action-row">
                                    <span class="button-small">Ver</span>
                                    <span class="button-small">Editar</span>
                                    <span class="button-secondary">Bloquear/Habilitar</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No hay usuarios registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
