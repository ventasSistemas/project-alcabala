@extends('layouts.app')

@section('title', 'Movimientos de Caja')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold">📋 Movimientos de Caja</h3>
        <a href="{{ route('movimientos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Movimiento
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Caja</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientos as $mov)
                    <tr>
                        <td>{{ $mov->id }}</td>
                        <td>#{{ $mov->caja->id }}</td>
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
                        <td colspan="6">No hay movimientos registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
