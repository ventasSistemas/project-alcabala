@extends('layouts.app')

@section('title', 'Cajas Registradas')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold">💼 Cajas Registradas</h3>
        <a href="{{ route('cajas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Abrir Nueva Caja
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Monto Inicial</th>
                        <th>Saldo Final</th>
                        <th>Estado</th>
                        <th>Fecha Apertura</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cajas as $caja)
                    <tr>
                        <td>{{ $caja->id }}</td>
                        <td>{{ $caja->user?->full_names ?? 'N/A' }}</td>
                        <td>S/. {{ number_format($caja->monto_inicial, 2) }}</td>
                        <td>S/. {{ number_format($caja->saldo_final, 2) }}</td>
                        <td>
                            <span class="badge {{ $caja->estado === 'ABIERTA' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $caja->estado }}
                            </span>
                        </td>
                        <td>{{ $caja->fecha_apertura }}</td>
                        <td>
                            <a href="{{ route('cajas.show', $caja->id) }}" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-eye"></i>
                            </a>

                            @if($caja->estado === 'ABIERTA')
                            <form action="{{ route('cajas.cerrar', $caja->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-warning">
                                    <i class="bi bi-lock"></i> Cerrar
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">No hay cajas registradas aún.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection