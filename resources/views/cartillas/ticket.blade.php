<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Pago</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 5mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
        }

        .ticket {
            width: 100%;
            text-align: center;
            margin: 0 auto;
        }

        h2 {
            margin: 0 0 3px 0;
            font-size: 15px;
        }

        .info {
            text-align: left;
            margin-top: 6px;
        }

        .info p {
            margin: 2px 0;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th, td {
            padding: 3px 0;
            text-align: center;
        }

        th {
            border-bottom: 1px dashed #000;
            font-size: 11px;
        }

        td {
            font-size: 11px;
        }

        .total {
            text-align: right;
            margin-top: 5px;
            font-weight: bold;
            font-size: 12px;
        }

        .footer {
            margin-top: 8px;
            text-align: center;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <h2>Alcabala - Ticket de Pago</h2>
        <p><strong>N° de Pago:</strong> {{ $numeroPago }}</p>
        <hr>
        <div class="info">
            <p><strong>Cliente:</strong> {{ $cliente->nombres }}</p>
            <p><strong>Puestos:</strong> {{ $puestos->join(', ') }}</p>
            <p><strong>Fecha:</strong> {{ $fecha }}</p>
        </div>
        <hr>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cartillas as $c)
                    <tr>
                        <td>{{ $c->nro }}</td>
                        <td>{{ \Carbon\Carbon::parse($c->fecha_pagar)->format('d/m/y') }}</td>
                        <td>S/ {{ number_format($c->cuota, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <hr>
        <p class="total">TOTAL PAGADO: S/ {{ number_format($totalPago, 2) }}</p>
        <hr>
    </div>
</body>
</html>
