<!-- resources/views/pagos/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary mb-0 d-flex align-items-center">
            <i class="fa-solid fa-laptop-file"></i>Historial de Pagos/Caja
        </h4>
    </div>

    <br>

    @if($pagos->isEmpty())
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-circle"></i> No hay pagos registrados todavía.
        </div>
    @else
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-info text-center">
                    <tr>
                        <th>#</th>
                        <th>Número de Pago</th>
                        <th>Fecha del Pago</th>
                        <th>Fecha Programada</th>  
                        <th>Monto (S/)</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pagos as $index => $pago)
                        <tr class="text-center">
                            <td>{{ $index + 1 }}</td>
                            <td><span class="fw-semibold">{{ $pago->numero_pago }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($pago->fecha_a_pagar)->format('d/m/Y') }}</td>
                            <td class="fw-bold text-success">S/ {{ number_format($pago->monto, 2) }}</td>
                            <td>
                                @if($pago->estado === 'PAGADO')
                                    <span class="badge bg-success">{{ $pago->estado }}</span>
                                @elseif($pago->estado === 'PAGO ATRASADO')
                                    <span class="badge bg-danger">{{ $pago->estado }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

@push('scripts')

@endpush
