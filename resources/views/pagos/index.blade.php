@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h4 class="fw-bold text-primary mb-4"><i class="fas fa-cash-register me-2"></i> Gestión de Pagos</h4>

    <!-- Filtros -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Categoría</label>
                    <select class="form-select" name="categoria_id">
                        <option value="">Todas</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">DNI Cliente</label>
                    <input type="text" class="form-control" name="dni" value="{{ request('dni') }}" placeholder="Ej: 12345678">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Número Puesto</label>
                    <input type="text" class="form-control" name="numero_puesto" value="{{ request('numero_puesto') }}" placeholder="Ej: A12">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Buscar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de pagos -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>DNI</th>
                            <th>Categoría</th>
                            <th>N° Puesto</th>
                            <th>Fecha a Pagar</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Accesor</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pagos as $index => $pago)
                            <tr class="text-center">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $pago->contrato->cliente->nombres }} {{ $pago->contrato->cliente->apellidos }}</td>
                                <td>{{ $pago->contrato->cliente->dni }}</td>
                                <td>{{ $pago->contrato->puesto->categoria->nombre }}</td>
                                <td>{{ $pago->contrato->puesto->numero_puesto }}</td>
                                <td>{{ \Carbon\Carbon::parse($pago->fecha_a_pagar)->format('d/m/Y') }}</td>
                                <td>S/ {{ number_format($pago->monto,2) }}</td>
                                <td>
                                    @if($pago->estado === 'PAGADO')
                                        <span class="badge bg-success">{{ $pago->estado }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ $pago->estado }}</span>
                                    @endif
                                </td>
                                <td>{{ $pago->accesor->nombres ?? '-' }}</td>
                                <td>
                                    @if($pago->estado === 'PENDIENTE')
                                        <button class="btn btn-sm btn-outline-success btn-marcar-pagado"
                                            data-pago-id="{{ $pago->id }}"
                                            data-cliente="{{ $pago->contrato->cliente->nombres }} {{ $pago->contrato->cliente->apellidos }}"
                                            data-dni="{{ $pago->contrato->cliente->dni }}"
                                            data-categoria="{{ $pago->contrato->puesto->categoria->nombre }}"
                                            data-puesto="{{ $pago->contrato->puesto->numero_puesto }}"
                                            data-monto="{{ $pago->monto }}">
                                            <i class="fas fa-check-circle"></i> Marcar Pagado
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-outline-secondary" disabled>
                                            <i class="fas fa-check"></i> Pagado
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-3">No hay pagos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $pagos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Marcar Pago -->
<div class="modal fade" id="modalMarcarPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-semibold">Marcar Pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formMarcarPago" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="pago_id" id="modal_pago_id">
                <div class="modal-body">
                    <p><strong>Cliente:</strong> <span id="modal_cliente"></span></p>
                    <p><strong>DNI:</strong> <span id="modal_dni"></span></p>
                    <p><strong>Categoría:</strong> <span id="modal_categoria"></span></p>
                    <p><strong>Puesto:</strong> <span id="modal_puesto"></span></p>
                    <p><strong>Monto:</strong> S/ <span id="modal_monto"></span></p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Accesor a cargo</label>
                        <select class="form-select" name="accesor_id">
                            <option value="">-- Seleccionar --</option>
                            @foreach(\App\Models\Accesor::orderBy('nombres_completos')->get() as $ac)
                                <option value="{{ $ac->id }}">{{ $ac->nombres_completos }} {{ $ac->apellidos }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Observación</label>
                        <textarea class="form-control" name="observacion" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fecha de Pago</label>
                        <input type="date" class="form-control" name="fecha_pago" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Registrar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const modalMarcarPago = new bootstrap.Modal(document.getElementById('modalMarcarPago'));
const formMarcarPago = document.getElementById('formMarcarPago');

document.querySelectorAll('.btn-marcar-pagado').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('modal_pago_id').value = btn.dataset.pagoId;
        document.getElementById('modal_cliente').textContent = btn.dataset.cliente;
        document.getElementById('modal_dni').textContent = btn.dataset.dni;
        document.getElementById('modal_categoria').textContent = btn.dataset.categoria;
        document.getElementById('modal_puesto').textContent = btn.dataset.puesto;
        document.getElementById('modal_monto').textContent = btn.dataset.monto;

        formMarcarPago.action = `/pagos/marcar-pagado/${btn.dataset.pagoId}`;
        modalMarcarPago.show();
    });
});

// Opcional: enviar formulario via fetch si quieres generar PDF y WhatsApp sin recargar
// formMarcarPago.addEventListener('submit', function(e){
//     e.preventDefault();
//     const url = formMarcarPago.action;
//     const data = new FormData(formMarcarPago);
//     fetch(url, { method: 'POST', body: data })
//         .then(res => location.reload());
// });
</script>
@endpush
