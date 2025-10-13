<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Pago</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }
        .container { width: 100%; max-width: 600px; margin: auto; border: 1px solid #ccc; border-radius: 10px; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #0d6efd; margin-bottom: 10px; }
        .header h2 { margin: 0; color: #0d6efd; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 6px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f1f1f1; }
        .footer { text-align: center; margin-top: 10px; font-size: 10px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Comprobante de Pago</h2>
            <p>{{ now()->format('d/m/Y H:i') }}</p>
        </div>

        @php
            $cliente = $pagos[0]->contrato->cliente ?? null;
            $puesto = $pagos[0]->contrato->puesto ?? null;
            $categoria = $puesto->categoria ?? null;
        @endphp

        <p><strong>Cliente:</strong> {{ $cliente->nombre ?? 'N/D' }}</p>
        <p><strong>Categoría:</strong> {{ $categoria->nombre ?? 'N/D' }}</p>
        <p><strong>N° Puesto:</strong> {{ $puesto->numero_puesto ?? 'N/D' }}</p>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha a Pagar</th>
                    <th>Monto (S/)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pagos as $pago)
                    <tr>
                        <td>{{ $pago->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($pago->fecha_a_pagar)->format('d/m/Y') }}</td>
                        <td>{{ number_format($pago->monto, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="text-end" style="text-align: right; font-weight: bold; margin-top: 10px;">
            Total Pagado: S/ {{ number_format(collect($pagos)->sum('monto'), 2) }}
        </p>

        <div class="footer">
            <p>Gracias por su pago.</p>
            <p>CleanWash - Sistema de Control de Pagos © {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>
