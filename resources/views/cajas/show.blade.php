@extends('layouts.app')

@section('title', 'Detalle de Caja')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold">💼 Detalle de Caja #{{ $caja->id }}</h3>
        <a href="{{ route('cajas.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p><strong>Usuario:</strong> {{ $caja->user?->full_names ?? 'N/A' }}</p>
            <p><strong>Monto Inicial:</strong> S/. {{ number_format($caja->monto_inicial, 2) }}</p>
            <p><strong>Total Ingresos:</strong> S/. {{ number_format($caja->total_ingresos, 2) }}</p>
            <p><strong>Total Egresos:</strong> S/. {{ number_format($caja->total_egresos, 2) }}</p>
            <p><strong>Saldo Final:</strong> S/. {{ number_format($caja->saldo_final, 2) }}</p>
            <p><strong>Estado:</strong> 
                <span class="badge {{ $caja->estado === 'ABIERTA' ? 'bg-success' : 'bg-secondary' }}">
                    {{ $caja->estado }}
                </span>
            </p>
            <p><strong>Fecha Apertura:</strong> {{ $caja->fecha_apertura }}</p>
            <p><strong>Fecha Cierre:</strong> {{ $caja->fecha_cierre ?? '—' }}</p>

            @if($caja->estado === 'ABIERTA')
                <form action="{{ route('cajas.cerrar', $caja->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-lock"></i> Cerrar Caja
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>📋 Movimientos Registrados</h4>
        @if($caja->estado === 'ABIERTA')
            <a href="{{ route('movimientos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nuevo Movimiento
            </a>
        @endif
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($caja->movimientos as $mov)
                    <tr>
                        <td>{{ $mov->id }}</td>
                        <td>
                            <span class="badge {{ $mov->tipo === 'INGRESO' ? 'bg-success' : 'bg-danger' }}">
                                {{ $mov->tipo }}
                            </span>
                        </td>
                        <td>{{ $mov->descripcion }}</td>
                        <td>S/. {{ number_format($mov->monto, 2) }}</td>
                        <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">No hay movimientos registrados aún.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
