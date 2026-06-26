@extends('layouts.app')

@section('title', 'BiblioTech - Pagar Deuda')
@section('section-title', 'Pagar deuda')

@section('content')
    <section class="panel">
        <h1 class="page-title">Pagar deuda</h1>
        <p class="page-description">Vista informativa para revisar multas y registrar pagos en una fase posterior.</p>
    </section>

    <section class="panel" style="margin-top: 18px;">
        <h2 style="margin-top: 0;">Pagos recientes del sistema</h2>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Monto</th>
                        <th>Fecha pago</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pagos as $pago)
                        <tr>
                            <td>{{ $pago->id }}</td>
                            <td>S/ {{ number_format((float) $pago->monto, 2) }}</td>
                            <td>{{ $pago->fecha_pago ?? 'N/A' }}</td>
                            <td><span class="badge badge-blue">{{ $pago->estado }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No hay pagos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
