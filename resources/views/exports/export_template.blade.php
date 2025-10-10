<table class="table table-bordered">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>DNI</th>
            <th>Establecimiento</th>
            <th>Puesto</th>
            <th>Fecha Pago</th>
            <th>Monto</th>
            <th>Estado</th>
            <th>Accesor</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pagos as $pago)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $pago->cliente->nombres_completos }}</td>
            <td>{{ $pago->cliente->dni }}</td>
            <td>{{ $pago->puesto->categoria->nombre }}</td>
            <td>{{ $pago->puesto->numero_puesto }}</td>
            <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
            <td>S/ {{ number_format($pago->monto, 2) }}</td>
            <td>{{ $pago->estado }}</td>
            <td>{{ $pago->accesor->nombres_completos ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>