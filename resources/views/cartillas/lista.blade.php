<!--views/cartillas/lista.blade.php-->
@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">📋 Lista de Cartillas</h4>

    @if($cartillas->isEmpty())
        <div class="alert alert-info">No hay cartillas registradas.</div>
    @else
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Puesto</th>
                    <th>Cliente</th>
                    <th>Fecha a Pagar</th>
                    <th>Cuota</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartillas as $cartilla)
                    <tr>
                        <td>{{ $cartilla->nro }}</td>
                        <td>{{ $cartilla->puesto->numero_puesto ?? 'N/A' }}</td>
                        <td>{{ $cartilla->cliente->nombres ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($cartilla->fecha_pagar)->format('d/m/Y') }}</td>
                        <td>S/ {{ number_format($cartilla->cuota, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $cartilla->observacion === 'Pagado' ? 'success' : 'warning' }}">
                                {{ $cartilla->observacion }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('cartillas.index', $cartilla->puesto_id) }}" class="btn btn-sm btn-primary">
                                Ver detalle
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
