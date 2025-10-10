<!--views/dashboard/index.blade.php-->
@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <h4 class="fw-bold text-primary mb-4 d-flex align-items-center">
        <i class="fas fa-tachometer-alt me-2"></i> Panel Principal
    </h4>

    <div class="row g-4">
        @php
            $cards = [
                ['fas fa-tags', 'Total Categorías', $totalCategorias ?? 0, 'text-primary'],
                ['fas fa-store', 'Total Puestos', $totalPuestos ?? 0, 'text-success'],
                ['fas fa-users', 'Total Clientes', $totalClientes ?? 0, 'text-warning'],
                ['fas fa-credit-card', 'Pagos Pendientes', $totalPendientes ?? 0, 'text-info'],
            ];
        @endphp

        @foreach ($cards as [$icon, $title, $value, $color])
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 hover-card h-100">
                    <div class="card-body text-center py-4">
                        <i class="{{ $icon }} fa-2x {{ $color }} mb-2"></i>
                        <h6 class="fw-semibold text-secondary">{{ $title }}</h6>
                        <h3 class="fw-bold {{ $color }}">{{ $value }}</h3>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    .hover-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        transition: all 0.3s ease-in-out;
    }
</style>
@endsection
