@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="fw-bold mb-4 text-primary">Reportes</h4>

    <form method="GET" action="{{ route('reports.index') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <x-form.select label="Categoría" name="categoria_id" :options="$categorias->pluck('nombre','id')->toArray()" />
        </div>
        <div class="col-md-3">
            <x-form.input label="DNI Cliente" name="dni" type="text" />
        </div>
        <div class="col-md-3">
            <x-form.input label="N° Puesto" name="numero_puesto" type="text" />
        </div>
        <div class="col-md-3">
            <x-form.select label="Accesor" name="accesor_id" :options=$accesores->pluck('nombres_completos','id')->toArray() />
        </div>
        <div class="col-md-3">
            <x-form.input label="Fecha Inicio" name="fecha_inicio" type="date" />
        </div>
        <div class="col-md-3">
            <x-form.input label="Fecha Fin" name="fecha_fin" type="date" />
        </div>
        <div class="col-md-3">
            <x-form.select label="Estado" name="estado" :options="['TODOS'=>'Todos','PAGADO'=>'Pagados','PENDIENTE'=>'Pendientes','RETIRADO'=>'Retirados']" />
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <x-buttons.btn-primary type="submit">Filtrar</x-buttons.btn-primary>
        </div>
    </form>

    <div class="mb-3">
        <a href="{{ route('reports.export', array_merge(request()->all(), ['format'=>'pdf'])) }}" class="btn btn-danger">Exportar PDF</a>
        <a href="{{ route('reports.export', array_merge(request()->all(), ['format'=>'excel'])) }}" class="btn btn-success">Exportar Excel</a>
    </div>

    <table class="table table-bordered table-striped">
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
            @forelse($pagos as $pago)
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
            @empty
            <tr><td colspan="9" class="text-center">No se encontraron pagos</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $pagos->links() }}
</div>
@endsection
