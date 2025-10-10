<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Pagos</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h4>Reporte de Pagos</h4>
    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>DNI</th>
                <th>Categoría</th>
                <th>N° Puesto</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Accesor</th>
                <th>Fecha a Pagar</th>
                <th>Fecha de Pago</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagos as $pago)
            <tr>
                <td>{{ $pago->contrato->cliente->nombres_completos }}</td>
                <td>{{ $pago->contrato->cliente->dni }}</td>
                <td>{{ $pago->contrato->puesto->categoria->nombre }}</td>
                <td>{{ $pago->contrato->puesto->numero_puesto }}</td>
                <td>S/ {{ number_format($pago->monto,2) }}</td>
                <td>{{ $pago->estado }}</td>
                <td>{{ $pago->accesor->nombres_completos ?? '-' }}</td>
                <td>{{ $pago->fecha_a_pagar }}</td>
                <td>{{ $pago->fecha_pago ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
