@extends('layouts.app')

@section('title', 'Abrir Caja')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">💼 Abrir Nueva Caja</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('cajas.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="monto_inicial" class="form-label">Monto Inicial (S/)</label>
                    <input type="number" step="0.01" name="monto_inicial" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Abrir Caja
                </button>
                <a href="{{ route('cajas.index') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection