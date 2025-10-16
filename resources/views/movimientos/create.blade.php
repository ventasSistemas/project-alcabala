@extends('layouts.app')

@section('title', 'Nuevo Movimiento')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">➕ Registrar Movimiento</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('movimientos.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="caja_id" class="form-label">Caja</label>
                    <select name="caja_id" class="form-select" required>
                        <option value="">-- Selecciona una caja abierta --</option>
                        @foreach($cajas as $caja)
                            <option value="{{ $caja->id }}">Caja #{{ $caja->id }} | Saldo: S/. {{ $caja->saldo_final }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo de Movimiento</label>
                    <select name="tipo" class="form-select" required>
                        <option value="INGRESO">Ingreso</option>
                        <option value="EGRESO">Egreso</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <input type="text" name="descripcion" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="monto" class="form-label">Monto (S/)</label>
                    <input type="number" step="0.01" name="monto" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Registrar
                </button>
                <a href="{{ route('movimientos.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
