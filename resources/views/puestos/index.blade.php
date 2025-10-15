<!--views/puestos/index.blade.php-->
@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
{{-- Si NO hay categoría seleccionada: mostrar las categorías --}}
    @if (!isset($puestos))
        <div class="mb-4">
            <h4 class="fw-bold text-primary mb-3 d-flex align-items-center">
                <i class="fas fa-layer-group me-2"></i> Categorías de Establecimientos
            </h4>
            <p class="text-muted">Selecciona una categoría para ver sus puestos.</p>
        </div>

        <div class="row g-4">
            @forelse ($categorias as $categoria)
                <div class="col-md-4 col-lg-3">
                    <div class="card shadow-sm border-0 h-100 hover-shadow rounded-4">
                        @if($categoria->imagen_lugar)
                            <img src="{{ asset('storage/'.$categoria->imagen_lugar) }}" 
                                 class="card-img-top rounded-top-4" 
                                 alt="{{ $categoria->nombre }}" 
                                 style="height:160px; object-fit:cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center rounded-top-4" style="height:160px;">
                                <i class="fas fa-store fa-3x text-secondary"></i>
                            </div>
                        @endif

                        <div class="card-body text-center">
                            <h6 class="fw-bold text-primary">{{ $categoria->nombre }}</h6>
                            <p class="text-muted small mb-2">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $categoria->direccion ?? 'Sin dirección' }}
                            </p>
                            <span class="badge bg-info text-dark mb-2">
                                {{ $categoria->puestos_count }} Puestos
                            </span>
                            <div>
                                <a href="{{ route('puestos.index', ['categoria_id' => $categoria->id]) }}" 
                                   class="btn btn-outline-primary btn-sm rounded-3">
                                    <i class="fas fa-eye me-1"></i> Ver Puestos
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted">
                    <i class="fas fa-folder-open fa-2x mb-2"></i><br>
                    No hay categorías registradas.
                </div>
            @endforelse
        </div>

    {{-- Si HAY categoría seleccionada: mostrar los puestos --}}
    @else
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-primary mb-0 d-flex align-items-center">
                <i class="fas fa-store-alt me-2"></i> Puestos - {{ $categoria->nombre }}
            </h4>

            <div class="d-flex gap-2">
                <a href="{{ route('puestos.index') }}" class="btn btn-outline-secondary rounded-3 shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>

                @if(!auth()->user()->accesor)
                    <button class="btn btn-primary rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPuesto">
                        <i class="fas fa-plus-circle me-1"></i> Nuevo Puesto
                    </button>
                @endif
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light text-center">
                            <tr>
                                <th>#</th>
                                <th>N° de Puesto</th>
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
                                    <td>
                                        @if ($puesto->disponible)
                                            <span class="badge bg-success">Disponible</span>
                                        @else
                                            <span class="badge bg-danger">Ocupado</span>
                                        @endif
                                    </td>
                                    <td>{{ $puesto->cliente->nombres ?? '-' }}</td>
                                    <td>
                                        @if($puesto->cliente && $puesto->cliente->puestos->flatMap->cartillas->count() > 0)
                                            <button 
                                                class="btn btn-sm btn-outline-info ms-1 btnVerCartilla"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalCartilla"
                                                data-cliente="{{ $puesto->cliente->id }}"
                                            >
                                                <i class="fas fa-file-invoice-dollar me-1"></i> Ver Cartilla
                                            </button>
                                        @endif

                                        @if(!auth()->user()->accesor)
                                            <button class="btn btn-sm btn-outline-warning me-1 btn-editar-puesto" 
                                                data-id="{{ $puesto->id }}"
                                                data-numero="{{ $puesto->numero_puesto }}"
                                                data-categoria="{{ $puesto->categoria_id }}"
                                                data-disponible="{{ $puesto->disponible }}"
                                                data-cliente-id="{{ $puesto->cliente->id ?? '' }}"
                                                data-cliente-nombre="{{ $puesto->cliente ? $puesto->cliente->nombres.' '.$puesto->cliente->apellidos : '' }}"
                                                data-servicios="{{ json_encode($puesto->servicios ?? []) }}"
                                                data-observaciones="{{ $puesto->observaciones ?? '' }}"
                                                data-hora-apertura="{{ $puesto->hora_apertura ?? '' }}"
                                                data-hora-cierre="{{ $puesto->hora_cierre ?? '' }}"
                                                data-fecha-inicio="{{ $puesto->fecha_inicio ?? '' }}"
                                                data-fecha-fin="{{ $puesto->fecha_fin ?? '' }}"
                                                data-primer-pago-fecha="{{ $puesto->primer_pago_fecha ?? '' }}"
                                                data-primer-pago-monto="{{ $puesto->primer_pago_monto ?? '' }}"
                                                data-modo-pago="{{ $puesto->modo_pago ?? '' }}"
                                                data-accesor-cobro="{{ $puesto->accesor_cobro ?? '' }}"
                                                data-imagen="{{ $puesto->imagen_puesto ?? '' }}"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <form action="{{ route('puestos.destroy', $puesto->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este puesto?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
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
    @endif

<!-- MODAL: Crear/Editar Puesto -->
<div class="modal fade" id="modalPuesto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-height: 90vh; overflow-y: auto;">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-semibold" id="modalPuestoLabel">Registrar Puesto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formPuesto" method="POST" action="{{ route('puestos.store') }}" enctype="multipart/form-data">
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
                                <!--
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Costo (S/)</label>
                                    <input type="text" id="pago_puesto" class="form-control" readonly>
                                </div>-->
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
                                    <label class="form-label fw-semibold">Fecha de Inscripción</label>
                                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Fecha Fin de Inscripción</label>
                                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Hora Apertura (Usuario)</label>
                                    <input type="time" class="form-control" id="hora_apertura" name="hora_apertura">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Hora Cierre (Usuario)</label>
                                    <input type="time" class="form-control" id="hora_cierre" name="hora_cierre">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Fecha del Primer Pago</label>
                                    <input type="date" class="form-control" id="primer_pago_fecha" name="primer_pago_fecha">
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Costo a cobrar cada jueves (S/)</label>
                                    <input type="text" id="pago_puesto" class="form-control" readonly>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Costo Inscripcion Anual (S/)</label>
                                    <input type="text" id="pago_inscripcion_anual" class="form-control">
                                </div>

                                <!-- 👥 Asignar Accesores -->
                                <div class="col-12 mt-4">
                                <h6 class="fw-bold text-primary border-bottom pb-1">Accesor Asignado</h6>
                                </div>

                                <div class="col-12 mb-2">
                                <label class="fw-semibold">Buscar Accesor</label>
                                <input 
                                    type="text" 
                                    id="buscarAccesorPuesto" 
                                    class="form-control" 
                                    placeholder="Escribe nombre o DNI para buscar...">
                                <div id="resultadosBusquedaPuesto" class="list-group mt-2 d-none"></div>
                                </div>

                                <div class="col-12 mt-3">
                                <label class="fw-semibold">Accesores Seleccionados</label>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle mb-0" id="tablaAccesoresSeleccionadosPuesto">
                                        <thead class="table-light">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>DNI</th>
                                            <th>Acción</th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                </div>

                                <!-- Campo oculto donde se guardan los IDs -->
                                <input type="hidden" name="accesores[]" id="accesor_id_puesto">

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
@endsection

@push('scripts')
<script>

//Abrir modal de Cartillas
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

// --- Manejo temporal del cliente asignado (guardado en localStorage) ---
function guardarClienteTemporal(cliente) {
    localStorage.setItem('clienteTemporal', JSON.stringify(cliente));
}

function obtenerClienteTemporal() {
    const c = localStorage.getItem('clienteTemporal');
    return c ? JSON.parse(c) : null;
}

function limpiarClienteTemporal() {
    localStorage.removeItem('clienteTemporal');
}

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

        // Campos básicos
        document.getElementById('puesto_id').value = btn.dataset.id;
        document.getElementById('numero_puesto').value = btn.dataset.numero;
        document.getElementById('categoria_id').value = btn.dataset.categoria;
        document.getElementById('disponible').value = btn.dataset.disponible;

        // --- Servicios ---
        const serviciosSeleccionados = JSON.parse(btn.dataset.servicios || '[]');
        document.querySelectorAll('input[name="servicios[]"]').forEach(chk => {
            chk.checked = serviciosSeleccionados.includes(chk.value);
        });

        // --- Otros campos ---
        document.getElementById('observaciones').value = btn.dataset.observaciones || '';
        document.getElementById('hora_apertura').value = btn.dataset.horaApertura || '';
        document.getElementById('hora_cierre').value = btn.dataset.horaCierre || '';
        document.getElementById('fecha_inicio').value = btn.dataset.fechaInicio || '';
        document.getElementById('fecha_fin').value = btn.dataset.fechaFin || '';
        document.getElementById('primer_pago_fecha').value = btn.dataset.primerPagoFecha || '';
        document.getElementById('primer_pago_monto').value = btn.dataset.primerPagoMonto || '';
        document.getElementById('modo_pago').value = btn.dataset.modoPago || '';
        document.getElementById('accesor_cobro').value = btn.dataset.accesorCobro || '';

        // --- Cliente ---
        const contenedorCliente = document.getElementById('contenedorClienteDinamico');
        if (btn.dataset.clienteNombre) {
            document.getElementById('radioBuscar').checked = true;
            contenedorCliente.innerHTML = `
                <label class="form-label fw-semibold">Cliente Asignado</label>
                <input type="text" class="form-control" value="${btn.dataset.clienteNombre}" readonly>
                <input type="hidden" id="cliente_id" name="cliente_id" value="${btn.dataset.clienteId}">
            `;
        } else {
            document.getElementById('radioNinguno').checked = true;
            contenedorCliente.innerHTML = '';
        }

        // --- Imagen ---
        const imgPreview = document.getElementById('preview-imagen');
        imgPreview.src = btn.dataset.imagen
            ? `/storage/${btn.dataset.imagen}`
            : 'https://media.istockphoto.com/id/1147544807/es/vector/no-imagen-en-miniatura-gr%C3%A1fico-vectorial.jpg?s=612x612';

        // Título y botón
        document.getElementById('modalPuestoLabel').textContent = 'Editar Puesto';
        document.getElementById('btnGuardarPuesto').textContent = 'Actualizar Puesto';

        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('modalPuesto'));
        modal.show();
    });
});


    // --- Antes de enviar el formulario ---
document.getElementById('formPuesto').addEventListener('submit', function(e) {
    const clienteTemp = obtenerClienteTemporal();
    const radioNinguno = document.getElementById('radioNinguno');

    // Si se eligió "Ninguno", agrega cliente_id vacío
    if (radioNinguno.checked) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'cliente_id';
        input.value = '';
        e.target.appendChild(input);
        limpiarClienteTemporal();
        return;
    }

    // Si se eligió cliente (buscar o crear)
    if (clienteTemp) {
        Object.keys(clienteTemp).forEach(key => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `cliente_temp[${key}]`;
            input.value = clienteTemp[key];
            e.target.appendChild(input);
        });
        limpiarClienteTemporal();
    }
});

    // ---- Abrir modal CREAR ----
    document.getElementById('modalPuesto').addEventListener('show.bs.modal', (e) => {
        const form = document.getElementById('formPuesto');

        // Si NO es editar, es un nuevo registro
        if (!e.relatedTarget?.classList.contains('btn-editar-puesto')) {
            form.action = '/puestos';
            document.querySelector('input[name="_method"]')?.remove();
            form.reset(); // limpia todos los inputs

            // Limpia valores específicos
            document.getElementById('puesto_id').value = '';
            document.getElementById('disponible').value = '1';
            document.getElementById('cliente_sugerencias')?.replaceChildren(); 
            document.getElementById('contenedorClienteDinamico').innerHTML = '';
            document.getElementById('contenedorContrato').classList.add('d-none');
            
            // Deseleccionar radios y dejar "ninguno"
            document.getElementById('radioNinguno').checked = true;

            // Deseleccionar todos los servicios
            document.querySelectorAll('input[name="servicios[]"]').forEach(chk => chk.checked = false);

            // Restablecer imagen de vista previa
            const preview = document.getElementById('preview-imagen');
            preview.src = 'https://media.istockphoto.com/id/1147544807/es/vector/no-imagen-en-miniatura-gr%C3%A1fico-vectorial.jpg?s=612x612&w=0&k=20&c=Bb7KlSXJXh3oSDlyFjIaCiB9llfXsgS7mHFZs6qUgVk=';

            // Restaurar textos del botón
            const btnGuardar = document.getElementById('btnGuardarPuesto');
            btnGuardar.textContent = 'Guardar Puesto';
            btnGuardar.type = 'submit';
        }
    });



});

// ---- Mostrar información de la categoría seleccionada ----
document.getElementById('categoria_id').addEventListener('change', function() {
    const categoriaId = this.value;

    // Limpia los campos si no hay selección
    document.getElementById('pago_puesto').value = '';
    document.getElementById('pago_inscripcion_anual').value = '';
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
        document.getElementById('pago_inscripcion_anual').value = data.pago_inscripcion_anual ?? '';
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
                            guardarClienteTemporal(c); 

                            Swal.fire({
                                icon: 'success',
                                title: 'Cliente asignado correctamente',
                                text: `${c.nombres} fue vinculado temporalmente al puesto.`,
                                showConfirmButton: false,
                                timer: 2000
                            });

                            document.getElementById('contenedorContrato').classList.remove('d-none');
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

        const nuevoCliente = { nombres, dni, celular };
            guardarClienteTemporal(nuevoCliente);

            Swal.fire({
                icon: 'success',
                title: 'Cliente creado temporalmente',
                text: `${nombres} ha sido asignado temporalmente al puesto.`,
                showConfirmButton: false,
                timer: 2500
            });
            document.getElementById('contenedorContrato').classList.remove('d-none');
    });
}

// --- Cálculo automático de Fechas Inicio / Fin ---
document.addEventListener('DOMContentLoaded', function() {
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');

    function calcularFechaFin(fechaBase) {
        const base = new Date(fechaBase);
        let anioFin = base.getFullYear() + 1;
        let mesFin = base.getMonth();
        let diaFin = base.getDate();

        let fechaProxima = new Date(anioFin, mesFin, diaFin);

        // Si el mes cambia (ej: 29 feb → marzo), mover al 1 del mes siguiente
        if (fechaProxima.getMonth() !== mesFin) {
            fechaProxima = new Date(anioFin, mesFin + 1, 1);
        }

        return fechaProxima.toISOString().split('T')[0];
    }

    function establecerFechasPorDefecto() {
        const hoy = new Date();
        const fechaInicioStr = hoy.toISOString().split('T')[0];
        fechaInicio.value = fechaInicioStr;
        fechaFin.value = calcularFechaFin(fechaInicioStr);
    }

    // Inicializar con la fecha actual al cargar el modal
    establecerFechasPorDefecto();

    // Recalcular automáticamente al cambiar la fecha de inicio
    fechaInicio.addEventListener('change', function() {
        if (this.value) {
            fechaFin.value = calcularFechaFin(this.value);
        }
    });

    // También recalcular al asignar o crear un cliente (mostrar contrato)
    const contenedorContrato = document.getElementById('contenedorContrato');
    const observer = new MutationObserver(() => {
        if (!contenedorContrato.classList.contains('d-none')) {
            establecerFechasPorDefecto();
        }
    });
    observer.observe(contenedorContrato, { attributes: true, attributeFilter: ['class'] });
});


// --- Búsqueda y selección de accesores en Puestos ---
const inputBuscarPuesto = document.getElementById('buscarAccesorPuesto');
const resultadosDivPuesto = document.getElementById('resultadosBusquedaPuesto');
const tablaAccesoresPuesto = document.getElementById('tablaAccesoresSeleccionadosPuesto').querySelector('tbody');
const inputIdsPuesto = document.getElementById('accesor_id_puesto');

// Accesores disponibles desde backend
const accesoresPuesto = @json($accesors); // Usa la misma variable que ya pasas desde tu controlador

// Buscar accesores por nombre o DNI
inputBuscarPuesto.addEventListener('keyup', () => {
  const texto = inputBuscarPuesto.value.toLowerCase().trim();
  resultadosDivPuesto.innerHTML = '';

  if (texto.length < 2) {
    resultadosDivPuesto.classList.add('d-none');
    return;
  }

  const resultados = accesoresPuesto.filter(a =>
    a.nombres.toLowerCase().includes(texto) || a.dni.includes(texto)
  );

  if (resultados.length === 0) {
    resultadosDivPuesto.innerHTML = '<div class="list-group-item text-muted">No se encontraron resultados.</div>';
  } else {
    resultados.forEach(a => {
      const item = document.createElement('button');
      item.type = 'button';
      item.className = 'list-group-item list-group-item-action';
      item.textContent = `${a.nombres} — DNI: ${a.dni}`;
      item.onclick = () => seleccionarAccesorPuesto(a);
      resultadosDivPuesto.appendChild(item);
    });
  }

  resultadosDivPuesto.classList.remove('d-none');
});

// Seleccionar un accesor
function seleccionarAccesorPuesto(accesor) {
  resultadosDivPuesto.classList.add('d-none');
  inputBuscarPuesto.value = '';

  if (document.querySelector(`#fila-puesto-${accesor.id}`)) return;

  const fila = document.createElement('tr');
  fila.id = `fila-puesto-${accesor.id}`;
  fila.innerHTML = `
    <td>${accesor.nombres}</td>
    <td>${accesor.dni}</td>
    <td>
      <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarAccesorPuesto(${accesor.id})">
        <i class="fas fa-trash"></i>
      </button>
    </td>
  `;
  tablaAccesoresPuesto.appendChild(fila);

  actualizarInputIdsPuesto();
}

function eliminarAccesorPuesto(id) {
  document.querySelector(`#fila-puesto-${id}`)?.remove();
  actualizarInputIdsPuesto();
}

function actualizarInputIdsPuesto() {
  const ids = Array.from(tablaAccesoresPuesto.querySelectorAll('tr')).map(fila =>
    parseInt(fila.id.replace('fila-puesto-', ''))
  );

  // Limpiar inputs anteriores
  document.querySelectorAll('input[name="accesores[]"]').forEach(el => el.remove());

  // Crear un input oculto por cada accesor seleccionado
  ids.forEach(id => {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'accesores[]';
    input.value = id;
    document.getElementById('formPuesto').appendChild(input);
  });
}

</script>
@endpush