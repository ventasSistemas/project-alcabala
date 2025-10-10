<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="text-center mb-4">
        <h4><strong>RECIBO DE PAGO</strong></h4>
        <p><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table class="table table-bordered">
        <tr><th>Cliente</th><td>{{ $pago->cliente->nombres_completos }}</td></tr>
        <tr><th>DNI</th><td>{{ $pago->cliente->dni }}</td></tr>
        <tr><th>Categoría</th><td>{{ $pago->puesto->categoria->nombre }}</td></tr>
        <tr><th>Nº de Puesto</th><td>{{ $pago->puesto->numero_puesto }}</td></tr>
        <tr><th>Monto</th><td>S/ {{ number_format($pago->monto,2) }}</td></tr>
        <tr><th>Estado</th><td>{{ $pago->estado }}</td></tr>
        <tr><th>Accesor a cargo</th><td>{{ $pago->accesor->nombres_completos ?? '-' }}</td></tr>
    </table>

    <div class="text-center mt-4">
        <small>Gracias por su pago.</small>
    </div>
</body>
</html>