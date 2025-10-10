<!--views/puestos/cartilla.blade.php-->
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">Cartilla de Pagos - Puesto {{ $puesto->numero_puesto }}</h4>
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>Nro</th>
                <th>Fecha a Pagar</th>
                <th>Cuota</th>
                <th>Observación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagos as $p)
            <tr>
                <td>{{ $p['nro'] }}</td>
                <td>{{ $p['fecha_pagar'] }}</td>
                <td>{{ $p['cuota'] }}</td>
                <td>{{ $p['observacion'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
