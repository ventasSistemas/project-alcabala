<!--views/clientes/index.blade.php-->
@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Título y botón -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary mb-0 d-flex align-items-center">
            <i class="fas fa-users me-2"></i> Gestión de Clientes
        </h4>
        @if(!auth()->user()->accesor)
        <button class="btn btn-primary rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCliente">
            <i class="fas fa-plus-circle me-1"></i> Nuevo Cliente
        </button>
        @endif
    </div>

    <!-- Tabla -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th>#</th>
                            <th>Nombres</th>
                            <th>DNI</th>
                            <th>Celular</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clientes as $index => $cliente)
                            <tr class="text-center">
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-semibold">{{ $cliente->nombres }}</td>
                               
                                <td>{{ $cliente->dni }}</td>
                                <td>{{ $cliente->celular ?? '-' }}</td>
                                <td>
                                    <!-- Botón Editar Cliente -->
                                    @if(!auth()->user()->accesor)
                                    <button 
                                        class="btn btn-sm btn-outline-warning me-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalCliente"
                                        data-id="{{ $cliente->id }}"
                                        data-nombres="{{ $cliente->nombres }}"
                                        
                                        data-dni="{{ $cliente->dni }}"
                                        data-celular="{{ $cliente->celular }}"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @endif

                                    <!-- Botón Elimninar Cliente -->
                                    @if(!auth()->user()->accesor)
                                    <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este cliente?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif

                                    <!-- Botón Ver Cartilla -->
                                    @php
                                        $tieneCartillas = $cliente->puestos->flatMap->cartillas->count() > 0;
                                    @endphp
                                    @if ($tieneCartillas)
                                        <!-- Botón para ver las cartillas -->
                                    <button 
                                        class="btn btn-sm btn-outline-info ms-2 btnVerCartilla"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalCartilla"
                                        data-cliente="{{ $cliente->id }}"
                                    >
                                        <i class="fas fa-file-invoice-dollar me-1"></i> Ver Cartilla
                                    </button>

                                    @endif

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                    No hay clientes registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $clientes->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear/Editar Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalClienteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-semibold" id="modalClienteLabel">Nuevo Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCliente" method="POST" action="{{ route('clientes.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="cliente_id" name="id">

                    <div class="mb-3">
                        <label for="nombres" class="form-label fw-semibold">Nombres</label>
                        <input type="text" class="form-control" id="nombres" name="nombres" required placeholder="Ej: Juan">
                    </div>

                    <!--
                    <div class="mb-3">
                        <label for="apellidos" class="form-label fw-semibold">Apellidos</label>
                        <input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Ej: Pérez">
                    </div>-->

                    <div class="mb-3">
                        <label for="dni" class="form-label fw-semibold">DNI</label>
                        <input type="text" class="form-control" id="dni" name="dni" maxlength="8" required placeholder="Ej: 12345678">
                    </div>

                    <div class="mb-3">
                        <label for="celular" class="form-label fw-semibold">Celular</label>
                        <input type="text" class="form-control" id="celular" name="celular" maxlength="9" placeholder="Ej: 987654321">
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Cartilla del Cliente -->
<div class="modal fade" id="modalCartilla" tabindex="-1" aria-labelledby="modalCartillaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalCartillaLabel">Cartilla de Pagos del Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="tablaCartilla">
                <div class="text-center text-muted py-5">Selecciona un cliente para ver su cartilla...</div>
            </div>
        </div>
    </div>
</div>



@push('scripts')
<script>
    const modalCliente = document.getElementById('modalCliente');
    modalCliente.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        const form = document.getElementById('formCliente');

        if (button && button.dataset.id) {
            // --- MODO EDICIÓN ---
            document.getElementById('modalClienteLabel').textContent = 'Editar Cliente';
            form.action = `/clientes/${button.dataset.id}`;
            form.querySelector('input[name="_method"]')?.remove();

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);

            document.getElementById('cliente_id').value = button.dataset.id;
            document.getElementById('nombres').value = button.dataset.nombres;
            //document.getElementById('apellidos').value = button.dataset.apellidos || '';
            document.getElementById('dni').value = button.dataset.dni;
            document.getElementById('celular').value = button.dataset.celular || '';
        } else {
            // --- MODO CREACIÓN ---
            document.getElementById('modalClienteLabel').textContent = 'Nuevo Cliente';
            form.action = "{{ route('clientes.store') }}";
            form.querySelector('input[name="_method"]')?.remove();
            form.reset();
        }
    });

document.querySelectorAll('.btnVerCartilla').forEach(btn => {
    btn.addEventListener('click', async () => {
        const clienteId = btn.dataset.cliente;
        const tablaCartilla = document.getElementById('tablaCartilla');
        tablaCartilla.innerHTML = '<div class="text-center text-muted py-5">Cargando información...</div>';

        try {
            const response = await fetch(`/clientes/${clienteId}/cartillas`);
            const html = await response.text();
            tablaCartilla.innerHTML = html;
        } catch (error) {
            tablaCartilla.innerHTML = '<div class="text-center text-danger py-5">Error al cargar la cartilla.</div>';
            console.error('Error al cargar cartillas:', error);
        }
    });
});
</script>
@endpush

@endsection
