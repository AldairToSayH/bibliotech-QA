@extends('layouts.app')

@section('title', 'BiblioTech - Libros')
@section('section-title', 'Libros')

@section('content')
    @php
        $puedeEditar = in_array(Auth::user()?->rol, ['admin', 'editor'], true);

        $estadoBadge = function (?string $estado): string {
            return match ($estado) {
                'DISPONIBLE' => 'badge-green',
                'PRESTADO' => 'badge-warn',
                default => 'badge-red',
            };
        };
    @endphp

    <section class="panel">
        <h1 class="page-title">Libros</h1>
        <p class="page-description">
            Catalogo y disponibilidad.
        </p>
    </section>

    @unless ($tablaDisponible)
        <section class="notice">
            La tabla libros no esta disponible. Ejecute las migraciones antes de usar este modulo.
        </section>
    @endunless

    @isset($mensaje)
        <section class="result-card result-valid">
            <h2>{{ $mensaje }}</h2>
            <p>El catalogo fue actualizado correctamente.</p>
        </section>
    @endisset

    <section class="grid" aria-label="Resumen de libros">
        <article class="card">
            <h2>Total de libros</h2>
            <p>Registros visibles en el catalogo academico.</p>
            <div class="card-footer">
                <span class="badge badge-blue">{{ $totalLibros }} libros</span>
            </div>
        </article>

        <article class="card">
            <h2>Disponibles</h2>
            <p>Libros listos para prestamo.</p>
            <div class="card-footer">
                <span class="badge badge-green">{{ $disponibles }} disponibles</span>
            </div>
        </article>

        <article class="card">
            <h2>Prestados</h2>
            <p>Libros actualmente no disponibles.</p>
            <div class="card-footer">
                <span class="badge badge-warn">{{ $prestados }} prestados</span>
            </div>
        </article>
    </section>

    <section class="content-split">
        @if ($puedeEditar)
            <article class="panel">
                <h2 style="margin-top: 0;">Registrar libro</h2>
                <p class="page-description">Agregue un nuevo registro al catalogo.</p>

                <form method="POST" action="{{ route('libros.store') }}">
                    @csrf

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="titulo">Titulo</label>
                            <input id="titulo" name="titulo" type="text" value="{{ old('titulo') }}" placeholder="Ej. Algoritmos basicos">
                            @error('titulo')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="autor">Autor</label>
                            <input id="autor" name="autor" type="text" value="{{ old('autor') }}" placeholder="Ej. Equipo BiblioTech">
                            @error('autor')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="isbn">ISBN o codigo interno</label>
                            <input id="isbn" name="isbn" type="text" value="{{ old('isbn') }}" placeholder="Ej. BT-ALG-001">
                            @error('isbn')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="estado">Estado</label>
                            <select id="estado" name="estado">
                                <option value="DISPONIBLE" @selected(old('estado', 'DISPONIBLE') === 'DISPONIBLE')>DISPONIBLE</option>
                                <option value="PRESTADO" @selected(old('estado') === 'PRESTADO')>PRESTADO</option>
                            </select>
                            @error('estado')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-actions">
                        <button class="button" type="submit" @disabled(!$tablaDisponible)>Guardar libro</button>
                    </div>
                </form>
            </article>
        @else
            <article class="panel">
                <h2 style="margin-top: 0;">Modo lectura</h2>
                <p class="page-description">Su rol permite consultar el catalogo sin modificar registros.</p>
            </article>
        @endif

        <aside class="panel">
            <h2 style="margin-top: 0;">Estados</h2>
            <div class="case-grid">
                <article class="case-card">
                    <h3>Disponibilidad</h3>
                    <p><span class="badge badge-green">DISPONIBLE</span> <span class="badge badge-warn">PRESTADO</span></p>
                </article>
            </div>
        </aside>
    </section>

    <section class="panel" id="catalogo" style="margin-top: 18px;">
        <h2 style="margin-top: 0;">Catalogo de libros</h2>
        <p class="page-description">Listado general del catalogo.</p>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titulo</th>
                        <th>Autor</th>
                        <th>ISBN</th>
                        <th>Estado</th>
                        <th>Prestamos</th>
                        @if ($puedeEditar)
                            <th>Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($libros as $libro)
                        <tr>
                            <td>{{ $libro->id }}</td>
                            <td><strong>{{ $libro->titulo }}</strong></td>
                            <td>{{ $libro->autor ?? 'No registrado' }}</td>
                            <td>{{ $libro->isbn ?? 'Sin codigo' }}</td>
                            <td><span class="badge {{ $estadoBadge($libro->estado) }}">{{ $libro->estado }}</span></td>
                            <td>{{ $libro->prestamos_count }}</td>
                            @if ($puedeEditar)
                                <td>
                                    <div class="action-row">
                                        <a class="button-small" href="#editar-libro-{{ $libro->id }}">Editar</a>
                                        <form method="POST" action="{{ route('libros.destroy', $libro) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="button-danger" type="submit">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $puedeEditar ? 7 : 6 }}">No hay libros registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if ($puedeEditar)
        @foreach ($libros as $libro)
            <section class="modal" id="editar-libro-{{ $libro->id }}" aria-label="Editar libro {{ $libro->id }}">
            <div class="modal-dialog">
                <div class="modal-header">
                    <div>
                        <h2 class="modal-title">Editar libro</h2>
                        <p class="page-description">{{ $libro->titulo }}</p>
                    </div>
                    <a class="modal-close" href="#catalogo">X</a>
                </div>

                <form method="POST" action="{{ route('libros.update', $libro) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="titulo_{{ $libro->id }}">Titulo</label>
                            <input id="titulo_{{ $libro->id }}" name="titulo" type="text" value="{{ old('titulo', $libro->titulo) }}">
                        </div>

                        <div class="form-field">
                            <label for="autor_{{ $libro->id }}">Autor</label>
                            <input id="autor_{{ $libro->id }}" name="autor" type="text" value="{{ old('autor', $libro->autor) }}">
                        </div>

                        <div class="form-field">
                            <label for="isbn_{{ $libro->id }}">ISBN o codigo</label>
                            <input id="isbn_{{ $libro->id }}" name="isbn" type="text" value="{{ old('isbn', $libro->isbn) }}">
                        </div>

                        <div class="form-field">
                            <label for="estado_{{ $libro->id }}">Estado</label>
                            <select id="estado_{{ $libro->id }}" name="estado">
                                <option value="DISPONIBLE" @selected(old('estado', $libro->estado) === 'DISPONIBLE')>DISPONIBLE</option>
                                <option value="PRESTADO" @selected(old('estado', $libro->estado) === 'PRESTADO')>PRESTADO</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions action-row">
                        <button class="button" type="submit">Guardar cambios</button>
                        <a class="button-secondary" href="#catalogo">Cancelar</a>
                    </div>
                </form>
            </div>
            </section>
        @endforeach
    @endif
@endsection
