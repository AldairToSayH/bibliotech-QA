@extends('layouts.app')

@section('title', 'BiblioTech - Editar Prestamo')
@section('section-title', 'Editar prestamo')

@section('content')
    <section class="panel">
        <h1 class="page-title">Editar prestamo</h1>
        <p class="page-description">Actualice usuario, libro, fechas o estado.</p>

        <form method="POST" action="{{ route('prestamos.update', $prestamo) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-field">
                    <label for="user_id">Usuario</label>
                    <select id="user_id" name="user_id">
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" @selected((int) old('user_id', $prestamo->user_id) === $usuario->id)>
                                {{ $usuario->name }} ({{ $usuario->rol ?? 'sin rol' }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="libro_id">Libro</label>
                    <select id="libro_id" name="libro_id">
                        @foreach ($libros as $libro)
                            <option value="{{ $libro->id }}" @selected((int) old('libro_id', $prestamo->libro_id) === $libro->id)>
                                {{ $libro->titulo }} ({{ $libro->estado }})
                            </option>
                        @endforeach
                    </select>
                    @error('libro_id') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="fecha_prestamo">Fecha prestamo</label>
                    <input id="fecha_prestamo" name="fecha_prestamo" type="date" value="{{ old('fecha_prestamo', $prestamo->fecha_prestamo) }}">
                    @error('fecha_prestamo') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="fecha_devolucion">Fecha devolucion</label>
                    <input id="fecha_devolucion" name="fecha_devolucion" type="date" value="{{ old('fecha_devolucion', $prestamo->fecha_devolucion) }}">
                    @error('fecha_devolucion') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        @foreach (['ACTIVO', 'VENCIDO', 'PENALIZADO', 'PAGADO', 'FINALIZADO'] as $estado)
                            <option value="{{ $estado }}" @selected(old('estado', $prestamo->estado) === $estado)>{{ $estado }}</option>
                        @endforeach
                    </select>
                    @error('estado') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-actions action-row">
                <button class="button" type="submit">Guardar cambios</button>
                <a class="button-secondary" href="{{ route('prestamos.index') }}">Cancelar</a>
            </div>
        </form>
    </section>
@endsection
