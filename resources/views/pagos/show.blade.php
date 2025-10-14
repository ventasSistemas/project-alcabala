@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4 class="text-primary"><i class="fa-solid fa-money-bill-wave"></i> Detalle del Pago</h4>
    <hr>

    <div class="card shadow-sm">
        <div class="card-body">
            <p><strong>Número de Pago:</strong> {{ $pago->numero_pago }}</p>
            <p><strong>Fecha de Pago:</strong> {{ $pago->fecha_pago }}</p>
            <p><strong>Monto:</strong> S/ {{ number_format($pago->monto, 2) }}</p>
            <p><strong>Estado:</strong> {{ $pago->estado }}</p>
        </div>
    </div>

    <a href="{{ route('pagos.index') }}" class="btn btn-secondary mt-3">
        <i class="fa-solid fa-arrow-left"></i> Volver a la lista
    </a>
</div>
@endsection
