@extends('layouts.app')

@section('title', 'BiblioTech - Administracion')
@section('section-title', 'Administracion')

@section('content')
    <section class="panel">
        <h1 class="page-title">Usuarios del panel</h1>
        <p class="page-description">Gestion de accesos administrativos.</p>
    </section>

    @isset($mensaje)
        <section class="result-card result-valid">
            <h2>{{ $mensaje }}</h2>
        </section>
    @endisset

    <section class="content-split">
        <article class="panel">
            <h2 style="margin-top: 0;">Nuevo usuario</h2>

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
                        <label for="rol">Rol</label>
                        <select id="rol" name="rol">
                            <option value="admin" @selected(old('rol') === 'admin')>Administrador</option>
                            <option value="editor" @selected(old('rol') === 'editor')>Editor</option>
                            <option value="visualizador" @selected(old('rol', 'visualizador') === 'visualizador')>Visualizador</option>
                        </select>
                        @error('rol') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button class="button" type="submit">Crear usuario</button>
                </div>
            </form>
        </article>

        <aside class="panel">
            <h2 style="margin-top: 0;">Permisos</h2>
            <div class="case-grid">
                <article class="case-card">
                    <h3>Administrador</h3>
                    <p class="page-description">Gestiona usuarios y contenido.</p>
                </article>
                <article class="case-card">
                    <h3>Editor</h3>
                    <p class="page-description">Puede crear, editar y eliminar contenido.</p>
                </article>
                <article class="case-card">
                    <h3>Visualizador</h3>
                    <p class="page-description">Solo puede consultar informacion.</p>
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
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td>{{ $usuario->name }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                <span class="badge {{ $usuario->rol === 'admin' ? 'badge-blue' : ($usuario->rol === 'editor' ? 'badge-warn' : 'badge-green') }}">
                                    {{ strtoupper($usuario->rol) }}
                                </span>
                            </td>
                            <td>
                                <div class="action-row">
                                    <a class="button-small" href="#editar-usuario-{{ $usuario->id }}">Editar</a>
                                    <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button-danger" type="submit">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    @foreach ($usuarios as $usuario)
        <section class="modal" id="editar-usuario-{{ $usuario->id }}" aria-label="Editar usuario {{ $usuario->id }}">
            <div class="modal-dialog">
                <div class="modal-header">
                    <div>
                        <h2 class="modal-title">Editar usuario</h2>
                        <p class="page-description">{{ $usuario->name }}</p>
                    </div>
                    <a class="modal-close" href="#usuarios">X</a>
                </div>

                <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="usuario_name_{{ $usuario->id }}">Nombre</label>
                            <input id="usuario_name_{{ $usuario->id }}" name="name" type="text" value="{{ old('name', $usuario->name) }}">
                        </div>

                        <div class="form-field">
                            <label for="usuario_email_{{ $usuario->id }}">Correo</label>
                            <input id="usuario_email_{{ $usuario->id }}" name="email" type="email" value="{{ old('email', $usuario->email) }}">
                        </div>

                        <div class="form-field">
                            <label for="usuario_password_{{ $usuario->id }}">Nueva contrasena</label>
                            <input id="usuario_password_{{ $usuario->id }}" name="password" type="password" placeholder="Dejar vacio para mantener">
                        </div>

                        <div class="form-field">
                            <label for="usuario_rol_{{ $usuario->id }}">Rol</label>
                            <select id="usuario_rol_{{ $usuario->id }}" name="rol">
                                <option value="admin" @selected(old('rol', $usuario->rol) === 'admin')>Administrador</option>
                                <option value="editor" @selected(old('rol', $usuario->rol) === 'editor')>Editor</option>
                                <option value="visualizador" @selected(old('rol', $usuario->rol) === 'visualizador')>Visualizador</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions action-row">
                        <button class="button" type="submit">Guardar cambios</button>
                        <a class="button-secondary" href="#usuarios">Cancelar</a>
                    </div>
                </form>
            </div>
        </section>
    @endforeach
@endsection
