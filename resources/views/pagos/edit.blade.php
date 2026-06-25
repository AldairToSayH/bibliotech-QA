@extends('layouts.app')

@section('title', 'BiblioTech - Editar Pago')
@section('section-title', 'Editar pago')

@section('content')
    <section class="panel">
        <h1 class="page-title">Editar pago</h1>
        <p class="page-description">Actualice monto, fechas o estado del pago.</p>

        <form method="POST" action="{{ route('pagos.update', $pago) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-field">
                    <label for="user_id">Usuario</label>
                    <select id="user_id" name="user_id">
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" @selected((int) old('user_id', $pago->user_id) === $usuario->id)>
                                {{ $usuario->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="prestamo_id">Prestamo</label>
                    <select id="prestamo_id" name="prestamo_id">
                        <option value="">Sin prestamo</option>
                        @foreach ($prestamos as $prestamo)
                            <option value="{{ $prestamo->id }}" @selected((int) old('prestamo_id', $pago->prestamo_id) === $prestamo->id)>
                                #{{ $prestamo->id }} - {{ $prestamo->user?->name }} / {{ $prestamo->libro?->titulo }}
                            </option>
                        @endforeach
                    </select>
                    @error('prestamo_id') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="monto">Monto</label>
                    <input id="monto" name="monto" type="number" step="0.01" min="0" value="{{ old('monto', $pago->monto) }}">
                    @error('monto') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="fecha_pago">Fecha pago</label>
                    <input id="fecha_pago" name="fecha_pago" type="date" value="{{ old('fecha_pago', $pago->fecha_pago) }}">
                    @error('fecha_pago') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="fecha_habilitacion">Fecha habilitacion</label>
                    <input id="fecha_habilitacion" name="fecha_habilitacion" type="date" value="{{ old('fecha_habilitacion', $pago->fecha_habilitacion) }}">
                    @error('fecha_habilitacion') <span class="error-text">{{ $message }}</span> @enderror
                </div>

                <div class="form-field">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        @foreach (['PENDIENTE', 'PAGADO', 'PAGADA', 'PENALIZADO', 'HABILITADO'] as $estado)
                            <option value="{{ $estado }}" @selected(old('estado', $pago->estado) === $estado)>{{ $estado }}</option>
                        @endforeach
                    </select>
                    @error('estado') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-actions action-row">
                <button class="button" type="submit">Guardar cambios</button>
                <a class="button-secondary" href="{{ route('pagos.index') }}">Cancelar</a>
            </div>
        </form>
    </section>
@endsection
