@extends('layouts.app')

@section('title', 'BiblioTech - Catalogo Alumno')
@section('section-title', 'Catalogo')

@section('content')
    <section class="panel">
        <h1 class="page-title">Catalogo</h1>
        <p class="page-description">Libros disponibles y prestados en la biblioteca.</p>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Titulo</th>
                        <th>Autor</th>
                        <th>Codigo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($libros as $libro)
                        <tr>
                            <td><strong>{{ $libro->titulo }}</strong></td>
                            <td>{{ $libro->autor ?? 'No registrado' }}</td>
                            <td>{{ $libro->isbn ?? 'Sin codigo' }}</td>
                            <td>
                                <span class="badge {{ $libro->estado === 'DISPONIBLE' ? 'badge-green' : 'badge-warn' }}">
                                    {{ $libro->estado }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No hay libros registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
