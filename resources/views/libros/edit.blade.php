@extends('layouts.app')

@section('title', 'BiblioTech - Editar Libro')
@section('section-title', 'Editar libro')

@section('content')
    <section class="panel">
        <h1 class="page-title">Editar libro</h1>
        <p class="page-description">Actualice los datos del registro seleccionado.</p>

        <form method="POST" action="{{ route('libros.update', $libro) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-field">
                    <label for="titulo">Titulo</label>
                    <input id="titulo" name="titulo" type="text" value="{{ old('titulo', $libro->titulo) }}">
                    @error('titulo') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="autor">Autor</label>
                    <input id="autor" name="autor" type="text" value="{{ old('autor', $libro->autor) }}">
                    @error('autor') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="isbn">ISBN o codigo interno</label>
                    <input id="isbn" name="isbn" type="text" value="{{ old('isbn', $libro->isbn) }}">
                    @error('isbn') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="DISPONIBLE" @selected(old('estado', $libro->estado) === 'DISPONIBLE')>DISPONIBLE</option>
                        <option value="PRESTADO" @selected(old('estado', $libro->estado) === 'PRESTADO')>PRESTADO</option>
                    </select>
                    @error('estado') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-actions action-row">
                <button class="button" type="submit">Guardar cambios</button>
                <a class="button-secondary" href="{{ route('libros.index') }}">Cancelar</a>
            </div>
        </form>
    </section>
@endsection
