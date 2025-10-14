@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <h4 class="fw-bold text-primary mb-0 d-flex align-items-center">
            <i class="fa-solid fa-chart-simple"></i>Reportes
        </h4>
        <div class="card-body">
            <!-- 🔍 FILTROS -->
            <form method="GET" action="{{ route('reports.index') }}" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                </div>

                <div class="col-md-3">
                    <label for="fecha_fin" class="form-label">Fecha Fin</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                </div>

                <div class="col-md-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="TODOS">-- Todos --</option>
                        @foreach ($estados as $estado)
                            <option value="{{ $estado }}" {{ request('estado') == $estado ? 'selected' : '' }}>
                                {{ ucfirst(strtolower($estado)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>
            </form>

            <!-- 🧾 TABLA DE RESULTADOS -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Número de Pago</th>
                            <th>Fecha Programada</th>
                            <th>Fecha de Pago</th>
                            <th>Monto (S/)</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pagos as $pago)
                            <tr>
                                <td>{{ $loop->iteration + ($pagos->currentPage() - 1) * $pagos->perPage() }}</td>
                                <td>{{ $pago->numero_pago ?? '—' }}</td>
                                <td>{{ \Carbon\Carbon::parse($pago->fecha_a_pagar)->format('d/m/Y') }}</td>
                                <td>{{ $pago->fecha_pago ? \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') : '—' }}</td>
                                <td>{{ number_format($pago->monto, 2) }}</td>
                                <td>
                                    <span class="badge 
                                        @if($pago->estado === 'PAGADO') bg-success 
                                        @elseif($pago->estado === 'PAGO ATRASADO') bg-warning 
                                        @else bg-secondary 
                                        @endif">
                                        {{ $pago->estado }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No se encontraron resultados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- 🔁 Paginación -->
            <div class="d-flex justify-content-end mt-3">
                {{ $pagos->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
