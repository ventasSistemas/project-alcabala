@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Título -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary mb-0 d-flex align-items-center">
            <i class="fas fa-store-alt me-2"></i> Gestión de Puestos
        </h4>
        <button class="btn btn-primary rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPuesto">
            <i class="fas fa-plus-circle me-1"></i> Nuevo Puesto
        </button>
    </div>

    <!-- Tabla -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th>#</th>
                            <th>N° de Puesto</th>
                            <th>Categoría</th>
                            <th>Disponible</th>
                            <th>Cliente</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($puestos as $index => $puesto)
                            <tr class="text-center">
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-semibold">{{ $puesto->numero_puesto }}</td>
                                <td>{{ $puesto->categoria->nombre ?? '-' }}</td>
                                <td>
                                    @if ($puesto->disponible)
                                        <span class="badge bg-success">Disponible</span>
                                    @else
                                        <span class="badge bg-danger">Ocupado</span>
                                    @endif
                                </td>
                                <td>{{ $puesto->cliente->nombres ?? '-' }}</td>
                                <td>
                                    @if($puesto->cliente)
                                        <button class="btn btn-sm btn-outline-info me-1 btn-ver-contrato" 
                                            data-puesto-id="{{ $puesto->id }}">
                                            <i class="fas fa-file-contract"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-sm btn-outline-warning me-1 btn-editar-puesto" 
                                        data-id="{{ $puesto->id }}"
                                        data-numero="{{ $puesto->numero_puesto }}"
                                        data-categoria="{{ $puesto->categoria_id }}"
                                        data-disponible="{{ $puesto->disponible }}"
                                        data-cliente-id="{{ $puesto->cliente->id ?? '' }}"
                                        data-cliente-nombre="{{ $puesto->cliente ? $puesto->cliente->nombres.' '.$puesto->cliente->apellidos : '' }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('puestos.destroy', $puesto->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este puesto?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">
                                    <i class="fas fa-box-open fa-2x mb-2"></i><br>No hay puestos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $puestos->links() }}
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Crear/Editar Puesto -->
<div class="modal fade" id="modalPuesto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-height: 90vh; overflow-y: auto;">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-semibold" id="modalPuestoLabel">Registrar Puesto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formPuesto" method="POST" action="{{ route('puestos.store') }}">
                @csrf
                <input type="hidden" id="puesto_id" name="puesto_id">

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- N° de Puesto -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">N° de Puesto</label>
                            <input type="text" class="form-control" id="numero_puesto" name="numero_puesto" required>
                        </div>

                        <!-- Categoría -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Categoría</label>
                            <select class="form-select" id="categoria_id" name="categoria_id" required>
                                <option value="">Seleccionar</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Costo, Hora Apertura y Cierre -->
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Costo (S/)</label>
                                    <input type="text" id="pago_puesto" class="form-control" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Hora Apertura</label>
                                    <input type="time" id="hora_apertura" class="form-control" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Hora Cierre</label>
                                    <input type="time" id="hora_cierre" class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Disponibilidad de Servicios</label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="servicios[]" value="Agua"
                                        {{ in_array('Agua', old('servicios', $puesto->servicios ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label">Agua</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="servicios[]" value="Luz"
                                        {{ in_array('Luz', old('servicios', $puesto->servicios ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label">Luz</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="servicios[]" value="Otros"
                                        {{ in_array('Otros', old('servicios', $puesto->servicios ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label">Otros</label>
                                </div>
                            </div>
                        </div>

                        <!-- Imagen del Puesto -->
                        <div class="col-12">
                            <label for="imagen_puesto" class="form-label fw-semibold">Imagen del N° de Puesto (opcional)</label>
                            <input type="file" name="imagen_puesto" id="imagen_puesto" class="form-control" accept="image/*">

                            <!-- Vista previa -->
                            <div class="mt-3 text-center">
                                <img id="preview-imagen"
                                    src="{{ isset($puesto) && $puesto->imagen_puesto ? asset('storage/' . $puesto->imagen_puesto) : 'https://media.istockphoto.com/id/1147544807/es/vector/no-imagen-en-miniatura-gr%C3%A1fico-vectorial.jpg?s=612x612&w=0&k=20&c=Bb7KlSXJXh3oSDlyFjIaCiB9llfXsgS7mHFZs6qUgVk=' }}"
                                    alt="Vista previa"
                                    class="img-thumbnail shadow-sm"
                                    style="width: 200px; height: 150px; object-fit: cover;">
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="col-12">
                            <label for="observaciones" class="form-label fw-semibold">Observaciones (opcional)</label>
                            <textarea name="observaciones" id="observaciones" class="form-control" rows="2">{{ old('observaciones', $puesto->observaciones ?? '') }}</textarea>
                        </div>

                        <!-- Disponible y Servicios en la misma fila -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Disponible</label>
                            <select class="form-select" id="disponible" name="disponible">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>

                        <!-- Selector de acción con cliente -->
                        <label class="fw-bold text-primary border-bottom pb-1">Acción con el Cliente</label>
                        <div class="col-12">
                            <h6 class="form-label fw-semibold">Asignar un Usuario</h6>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="accion_cliente" id="radioBuscar" value="buscar">
                                    <label class="form-check-label" for="radioBuscar">Buscar Usuario</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="accion_cliente" id="radioCrear" value="crear">
                                    <label class="form-check-label" for="radioCrear">Crear Usuario</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="accion_cliente" id="radioNinguno" value="ninguno" checked>
                                    <label class="form-check-label" for="radioNinguno">Ninguno</label>
                                </div>
                            </div>
                        </div>

                        <!-- Contenedor dinámico para Buscar o Crear Cliente -->
                        <div class="col-12 mt-3" id="contenedorClienteDinamico"></div>

                        <!-- Datos del Contrato - Inicialmente ocultos -->
                        <div class="col-12 mt-4 d-none" id="contenedorContrato">
                            <label class="fw-bold text-primary border-bottom pb-1">Detalles del Contrato</label>
                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Fecha de Inicio</label>
                                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Fecha de Fin</label>
                                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Hora Apertura</label>
                                    <input type="time" class="form-control" id="hora_apertura" name="hora_apertura">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Hora Cierre</label>
                                    <input type="time" class="form-control" id="hora_cierre" name="hora_cierre">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Fecha del Primer Pago</label>
                                    <input type="date" class="form-control" id="primer_pago_fecha" name="primer_pago_fecha">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Monto del Primer Pago (S/)</label>
                                    <input type="number" step="0.01" class="form-control" id="primer_pago_monto" name="primer_pago_monto">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Modo de Pago</label>
                                    <select class="form-select" id="modo_pago" name="modo_pago" required>
                                        <option value="">Seleccionar</option>
                                        <option value="SEMANAL">Semanal</option>
                                        <option value="MENSUAL">Mensual</option>
                                        <option value="ANUAL">Anual</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Accesor a Cargo</label>
                                    <input type="text" class="form-control" id="accesor_cobro" name="accesor_cobro" placeholder="Nombre del accesor a cargo...">
                                </div>

                                <div class="col-12 text-end mt-3">
                                    <button type="button" class="btn btn-outline-primary" id="btnVerCartilla">
                                        <i class="bi bi-card-list"></i> Ver Cartilla de Pagos
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarPuesto">Guardar Puesto</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- MODAL: Contrato -->
<div class="modal fade" id="modalContrato" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-semibold">Contrato</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div id="contenidoContrato" class="text-center text-muted">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Cargando información del contrato...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ---- Abrir modal EDITAR ----
    document.querySelectorAll('.btn-editar-puesto').forEach(btn => {
        btn.addEventListener('click', () => {
            const form = document.getElementById('formPuesto');
            form.action = `/puestos/${btn.dataset.id}`;
            if (!form.querySelector('input[name="_method"]')) {
                const m = document.createElement('input');
                m.type = 'hidden';
                m.name = '_method';
                m.value = 'PUT';
                form.appendChild(m);
            }

            document.getElementById('puesto_id').value = btn.dataset.id;
            document.getElementById('numero_puesto').value = btn.dataset.numero;
            document.getElementById('categoria_id').value = btn.dataset.categoria;
            document.getElementById('disponible').value = btn.dataset.disponible;
            document.getElementById('cliente_busqueda').value = btn.dataset.clienteNombre;
            document.getElementById('cliente_id').value = btn.dataset.clienteId;
        });
    });

    // ---- Abrir modal CREAR ----
    document.getElementById('modalPuesto').addEventListener('show.bs.modal', (e) => {
        if (!e.relatedTarget?.classList.contains('btn-editar-puesto')) {
            const form = document.getElementById('formPuesto');
            form.action = '/puestos';
            document.querySelector('input[name="_method"]')?.remove();
            form.reset();
            document.getElementById('disponible').value = '1';
            document.getElementById('cliente_sugerencias').innerHTML = '';
        }
    });

    // ---- Ver contrato ----
    document.querySelectorAll('.btn-ver-contrato').forEach(btn => {
        btn.addEventListener('click', () => {
            const contenedor = document.getElementById('contenidoContrato');
            contenedor.innerHTML = '<i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Cargando...</p>';
            fetch(`/puestos/${btn.dataset.puestoId}`)
                .then(r => r.json())
                .then(data => {
                    const c = data.contratoActivo;
                    if (!c) {
                        contenedor.innerHTML = `<div class="text-muted"><i class="fas fa-info-circle"></i> No hay contrato activo para este puesto.</div>`;
                        return;
                    }
                    contenedor.innerHTML = `
                        <div class="row text-start">
                            <div class="col-md-6"><strong>Cliente:</strong> ${data.cliente.nombres} ${data.cliente.apellidos}</div>
                            <div class="col-md-6"><strong>Puesto:</strong> ${data.puesto.numero_puesto}</div>
                            <div class="col-md-4 mt-3"><strong>Fecha Inicio:</strong> ${c.fecha_inicio}</div>
                            <div class="col-md-4 mt-3"><strong>Fecha Fin:</strong> ${c.fecha_fin ?? '---'}</div>
                            <div class="col-md-4 mt-3"><strong>Frecuencia:</strong> ${c.frecuencia_pago}</div>
                            <div class="col-md-4 mt-3"><strong>Monto:</strong> S/ ${c.monto}</div>
                            <div class="col-md-4 mt-3"><strong>Renovable:</strong> ${c.renovable ? 'Sí' : 'No'}</div>
                        </div>
                    `;
                });
        });
    });

});

// ---- Mostrar información de la categoría seleccionada ----
document.getElementById('categoria_id').addEventListener('change', function() {
    const categoriaId = this.value;

    // Limpia los campos si no hay selección
    document.getElementById('pago_puesto').value = '';
    document.getElementById('hora_apertura').value = '';
    document.getElementById('hora_cierre').value = '';

    if (!categoriaId) return;

    fetch(`/categorias/${categoriaId}/info`, { 
        headers: { 'X-Requested-With': 'XMLHttpRequest' } 
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.warn(data.error);
            return;
        }
        document.getElementById('pago_puesto').value = data.pago_puesto ?? '';
        document.getElementById('hora_apertura').value = data.hora_apertura ?? '';
        document.getElementById('hora_cierre').value = data.hora_cierre ?? '';
    })
    .catch(err => console.error(err));
});

document.getElementById('imagen_puesto').addEventListener('change', function (event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview-imagen');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = e => preview.src = e.target.result;
        reader.readAsDataURL(file);
    } else {
        preview.src = 'https://via.placeholder.com/200x150?text=Vista+Previa';
    }
});


// --- RADIO BUTTONS PARA CLIENTE ---
const radios = document.querySelectorAll('input[name="accion_cliente"]');
const btnGuardar = document.getElementById('btnGuardarPuesto');
const contenedorCliente = document.getElementById('contenedorClienteDinamico');
let tipoAccionCliente = 'ninguno';
let clienteSeleccionado = null;

// Limpia y resetea el contenedor de cliente
function limpiarCliente() {
    contenedorCliente.innerHTML = '';
    clienteSeleccionado = null;
    btnGuardar.textContent = 'Guardar Puesto';
    btnGuardar.type = 'submit';
}

// Escucha cambios en los radios
radios.forEach(radio => {
    radio.addEventListener('change', () => {
        tipoAccionCliente = radio.value;
        contenedorCliente.innerHTML = '';

        if (tipoAccionCliente === 'buscar') {
            contenedorCliente.innerHTML = `
                <label class="form-label fw-semibold">Buscar Cliente</label>
                <input type="text" class="form-control" id="cliente_busqueda" placeholder="Buscar cliente...">
                <div id="cliente_sugerencias" class="list-group mt-2"></div>
                <div id="cliente_info" class="mt-3"></div>
            `;
            btnGuardar.textContent = 'Guardar Puesto';
            btnGuardar.type = 'submit';
            inicializarBuscadorCliente();
        } 
        else if (tipoAccionCliente === 'crear') {
            contenedorCliente.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombres</label>
                        <input type="text" class="form-control" id="nuevo_nombres">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">DNI</label>
                        <input type="text" class="form-control" id="nuevo_dni">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Celular</label>
                        <input type="text" class="form-control" id="nuevo_celular">
                    </div>
                    <div class="col-12 text-end mt-2">
                        <button type="button" class="btn btn-outline-success" id="btnAsignarNuevoCliente">
                            Asignar Cliente
                        </button>
                    </div>
                </div>
            `;
            btnGuardar.textContent = 'Guardar Puesto';
            btnGuardar.type = 'submit';
            inicializarCreacionCliente();
        } 
        else {
            limpiarCliente();
        }
    });
});

// Inicializar búsqueda de cliente
function inicializarBuscadorCliente() {
    const inputBusqueda = document.getElementById('cliente_busqueda');
    const sugerencias = document.getElementById('cliente_sugerencias');
    const info = document.getElementById('cliente_info');

    inputBusqueda.addEventListener('input', () => {
        const q = inputBusqueda.value.trim();
        sugerencias.innerHTML = '';
        info.innerHTML = '';
        if (q.length < 2) return;

        fetch(`/clientes/buscar?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => {
                if (!data.length) {
                    sugerencias.innerHTML = '<div class="list-group-item disabled">No se encontraron clientes</div>';
                    return;
                }

                data.forEach(c => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = `${c.dni} - ${c.nombres}`;
                    item.onclick = () => {
                        clienteSeleccionado = c;
                        sugerencias.innerHTML = '';

                        info.innerHTML = `
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nombres</label>
                                    <input type="text" class="form-control" id="nuevo_nombres" value="${c.nombres}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">DNI</label>
                                    <input type="text" class="form-control" id="nuevo_dni" value="${c.dni}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Celular</label>
                                    <input type="text" class="form-control" id="nuevo_celular" value="${c.celular}" readonly>
                                </div>
                                <div class="col-12 text-end mt-2">
                                    <button type="button" class="btn btn-outline-success" id="btnAsignarExistente">
                                        Asignar Cliente
                                    </button>
                                </div>
                            </div>
                        `;

                        // Botón asignar usuario existente
                        document.getElementById('btnAsignarExistente').addEventListener('click', () => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cliente asignado correctamente',
                                text: `${c.nombres} fue vinculado al puesto.`,
                                showConfirmButton: false,
                                timer: 2000
                            });
                            document.getElementById('contenedorContrato').classList.remove('d-none');
                            btnGuardar.textContent = 'Guardar Puesto';
                            btnGuardar.type = 'submit';
                        });
                    };
                    sugerencias.appendChild(item);
                });
            });
    });
}

// Inicializar creación de cliente
function inicializarCreacionCliente() {
    document.getElementById('btnAsignarNuevoCliente').addEventListener('click', () => {
        const nombres = document.getElementById('nuevo_nombres').value.trim();
        const dni = document.getElementById('nuevo_dni').value.trim();
        const celular = document.getElementById('nuevo_celular').value.trim();

        if (!nombres || !dni) {
            Swal.fire({
                icon: 'warning',
                title: 'Datos incompletos',
                text: 'Por favor, completa los campos obligatorios.',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        clienteSeleccionado = { nombres, dni, celular };
        Swal.fire({
            icon: 'success',
            title: 'Cliente creado y asignado',
            text: `${nombres} ha sido registrado correctamente.`,
            showConfirmButton: false,
            timer: 2500
        });
        document.getElementById('contenedorContrato').classList.remove('d-none');
        btnGuardar.textContent = 'Guardar Puesto';
        btnGuardar.type = 'submit';
    });
}

// --- Botón Crear Contrato ---
btnGuardar.addEventListener('click', () => {
    if (btnGuardar.textContent === 'Crear Contrato') {
        const modalContrato = new bootstrap.Modal(document.getElementById('modalContrato'));
        modalContrato.show();
    }
});

// Mostrar la cartilla de pagos simulada
document.getElementById('btnVerCartilla').addEventListener('click', () => {
    const modo = document.getElementById('modo_pago').value;
    const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin = document.getElementById('fecha_fin').value;
    const monto = document.getElementById('primer_pago_monto').value;
    const puestoId = document.getElementById('puesto_id')?.value; // asegúrate de tenerlo oculto

    if (!modo || !fechaInicio || !fechaFin || !monto) {
        Swal.fire({
            icon: 'warning',
            title: 'Datos incompletos',
            text: 'Completa los campos de contrato para generar la cartilla.',
        });
        return;
    }

    // Redirigir con parámetros
    const url = `/puestos/${puestoId}/cartilla?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&modo_pago=${modo}&primer_pago_monto=${monto}`;
    window.open(url, '_blank'); // abre la cartilla en una nueva pestaña
});

</script>
@endpush