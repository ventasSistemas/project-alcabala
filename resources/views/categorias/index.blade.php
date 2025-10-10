<!--views/categorias/index.blade.php-->
@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Título y botón -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary mb-0 d-flex align-items-center">
            <i class="fas fa-store me-2"></i> Gestión de Categorías de Establecimientos
        </h4>
        <button class="btn btn-primary rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCategoria">
            <i class="fas fa-plus-circle me-1"></i> Nuevo Establecimiento
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
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>Pago Puesto (S/)</th>
                            <th>Hora Apertura</th>
                            <th>Hora Cierre</th>
                            <th>Accesores Asignados</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categorias as $index => $categoria)
                            <tr class="text-center">
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-semibold">{{ $categoria->nombre }}</td>
                                <td>{{ $categoria->direccion ?? '-' }}</td>
                                <td>{{ number_format($categoria->pago_puesto, 2) }}</td>
                                <td>{{ $categoria->hora_apertura ?? '-' }}</td>
                                <td>{{ $categoria->hora_cierre ?? '-' }}</td>
                                <td>
                                    @if($categoria->accesores && $categoria->accesores->count())
                                        @foreach($categoria->accesores as $accesor)
                                            <span class="badge bg-primary">{{ $accesor->nombres }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Sin asignar</span>
                                    @endif
                                </td>
                                <td>
                                    <!-- 🔹 Botón Ver -->
                                    <button class="btn btn-sm btn-outline-info me-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalVerCategoria"
                                        data-nombre="{{ $categoria->nombre }}"
                                        data-direccion="{{ $categoria->direccion }}"
                                        data-pago_puesto="{{ number_format($categoria->pago_puesto, 2) }}"
                                        data-hora_apertura="{{ $categoria->hora_apertura }}"
                                        data-hora_cierre="{{ $categoria->hora_cierre }}"
                                        data-lat_actual="{{ $categoria->latitud_actual }}"
                                        data-lng_actual="{{ $categoria->lng_actual }}"
                                        data-lat_destino="{{ $categoria->lat_destino }}"
                                        data-lng_destino="{{ $categoria->lng_destino }}"
                                        data-imagen="{{ $categoria->imagen_lugar }}"
                                        data-accesores='@json($categoria->accesores)'
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <!-- Botón Editar -->
                                    <button class="btn btn-sm btn-outline-warning me-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalCategoria"
                                        data-id="{{ $categoria->id }}"
                                        data-nombre="{{ $categoria->nombre }}"
                                        data-direccion="{{ $categoria->direccion }}"
                                        data-imagen="{{ $categoria->imagen_lugar }}"
                                        data-latactual="{{ $categoria->latitud_actual }}"
                                        data-lngactual="{{ $categoria->lng_actual }}"
                                        data-latdestino="{{ $categoria->lat_destino }}"
                                        data-lngdestino="{{ $categoria->lng_destino }}"
                                        data-pago_puesto="{{ $categoria->pago_puesto }}"
                                        data-hora_apertura="{{ $categoria->hora_apertura }}"
                                        data-hora_cierre="{{ $categoria->hora_cierre }}"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <!-- Botón Eliminar -->
                                    <form action="{{ route('categorias.destroy', $categoria->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este establecimiento?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-3">
                                    <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                    No hay establecimientos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $categorias->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear/Editar Establecimiento -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalCategoriaLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-semibold" id="modalCategoriaLabel">Nuevo Establecimiento</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form id="formCategoria" method="POST" action="{{ route('categorias.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="row g-3">

            <!-- Datos Generales -->
            <div class="col-12">
              <h6 class="fw-bold text-primary border-bottom pb-1">Datos Generales</h6>
            </div>

            <div class="col-md-6">
              <label class="fw-semibold">Nombre del Establecimiento</label>
              <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Ej: Mercado El Techado" required>
            </div>

            <div class="col-md-6">
              <label class="fw-semibold">Dirección</label>
              <input type="text" class="form-control" name="direccion" id="direccion" placeholder="Ej: Av. 2 de Mayo">
            </div>

            <div class="col-md-6">
              <label class="fw-semibold">Imagen del Lugar (Opcional)</label>
              <input type="file" class="form-control" name="imagen_lugar" id="imagen_lugar" accept="image/*">
              <div class="mt-2 text-center">
                <img id="previewImagen" src="#" alt="Vista previa" class="img-fluid rounded shadow-sm d-none" style="max-height: 180px; object-fit: cover;">
              </div>
            </div>

           

            <!-- Coordenadas -->
            <div class="col-12 mt-3">
            <h6 class="fw-bold text-primary border-bottom pb-1">Coordenadas</h6>
            </div>

            <!-- Botón centrado -->
            <div class="col-12 d-flex justify-content-center my-2">
            <div class="col-md-6">
                <button type="button" class="btn btn-outline-success w-100" id="btnCapturarUbicacion">
                <i class="fas fa-map-marker-alt me-2"></i> Capturar Ubicación Actual
                </button>
            </div>
            </div>

            <!-- Campos de coordenadas -->
            <div class="col-md-3">
            <label class="fw-semibold">Latitud Actual</label>
            <input type="number" step="0.0000001" class="form-control" name="latitud_actual" id="lat_actual" placeholder="Ej: -12.0464">
            </div>

            <div class="col-md-3">
            <label class="fw-semibold">Longitud Actual</label>
            <input type="number" step="0.0000001" class="form-control" name="longitud_actual" id="lng_actual" placeholder="Ej: -77.0428">
            </div>

            <div class="col-md-3">
            <label class="fw-semibold">Latitud Destino</label>
            <input type="number" step="0.0000001" class="form-control" name="latitud_destino" id="lat_destino" placeholder="Ej: -12.0472">
            </div>

            <div class="col-md-3">
            <label class="fw-semibold">Longitud Destino</label>
            <input type="number" step="0.0000001" class="form-control" name="longitud_destino" id="lng_destino" placeholder="Ej: -77.0416">
            </div>

            <div class="col-12">
            <div id="mapa" style="height: 300px; border-radius: 10px;"></div>
            <p class="text-muted small mt-2" id="distancia"></p>
            </div>

            <!-- Horarios y Pago -->
            <div class="col-12 mt-3">
              <h6 class="fw-bold text-primary border-bottom pb-1">Horario y Pago</h6>
            </div>

            <div class="col-md-4">
              <label class="fw-semibold">Hora de Apertura</label>
              <input type="time" class="form-control" name="hora_apertura" id="hora_apertura">
            </div>

            <div class="col-md-4">
              <label class="fw-semibold">Hora de Cierre</label>
              <input type="time" class="form-control" name="hora_cierre" id="hora_cierre">
            </div>

            <div class="col-md-4">
              <label class="fw-semibold">Pago del Puesto (S/)</label>
              <input type="number" step="0.01" class="form-control" name="pago_puesto" id="pago_puesto" required>
            </div>

            <!-- 👥 Personal -->
            <div class="col-12 mt-3">
            <h6 class="fw-bold text-primary border-bottom pb-1">Personal Asignado</h6>
            </div>

            <div class="col-12 mb-2">
            <label class="fw-semibold">Buscar Accesor</label>
            <input 
                type="text" 
                id="buscarAccesor" 
                class="form-control" 
                placeholder="Escribe nombre o DNI para buscar...">
            <div id="resultadosBusqueda" class="list-group mt-2 d-none"></div>
            </div>

            <!-- Contenedor donde se mostrarán los seleccionados -->
            <div class="col-12 mt-3">
            <label class="fw-semibold">Accesores Seleccionados</label>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0" id="tablaAccesoresSeleccionados">
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

            <!-- Campo oculto donde se guardan los IDs seleccionados -->
            <input type="hidden" name="accesores[]" id="accesor_id">

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

<!-- Modal Ver Establecimiento -->
<div class="modal fade" id="modalVerCategoria" tabindex="-1" aria-labelledby="modalVerCategoriaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content rounded-4 border-0 shadow">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title fw-semibold" id="modalVerCategoriaLabel">
          <i class="fas fa-eye me-2"></i> Detalles del Establecimiento
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">

          <div class="col-md-6">
            <label class="fw-semibold">Nombre:</label>
            <p id="ver_nombre" class="form-control-plaintext border-bottom"></p>
          </div>

          <div class="col-md-6">
            <label class="fw-semibold">Dirección:</label>
            <p id="ver_direccion" class="form-control-plaintext border-bottom"></p>
          </div>

          <div class="col-md-4">
            <label class="fw-semibold">Pago Puesto (S/):</label>
            <p id="ver_pago" class="form-control-plaintext border-bottom"></p>
          </div>

          <div class="col-md-4">
            <label class="fw-semibold">Hora Apertura:</label>
            <p id="ver_hora_apertura" class="form-control-plaintext border-bottom"></p>
          </div>

          <div class="col-md-4">
            <label class="fw-semibold">Hora Cierre:</label>
            <p id="ver_hora_cierre" class="form-control-plaintext border-bottom"></p>
          </div>

          <div class="col-md-12">
            <label class="fw-semibold">Accesores Asignados:</label>
            <div id="ver_accesores" class="mt-1"></div>
          </div>

          <div class="col-md-12 text-center mt-3">
            <img id="ver_imagen" src="#" alt="Imagen del lugar" class="img-fluid rounded shadow-sm d-none" style="max-height: 250px; object-fit: cover;">
          </div>
        </div>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


@push('scripts')
<!-- Leaflet y plugins -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

<script>

const modalVer = document.getElementById('modalVerCategoria');
modalVer.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;

    // Extrae datos del botón
    const nombre = button.getAttribute('data-nombre');
    const direccion = button.getAttribute('data-direccion');
    const pago = button.getAttribute('data-pago_puesto');
    const horaApertura = button.getAttribute('data-hora_apertura');
    const horaCierre = button.getAttribute('data-hora_cierre');
    const imagen = button.getAttribute('data-imagen');
    const accesores = JSON.parse(button.getAttribute('data-accesores'));

    // Asigna los valores
    document.getElementById('ver_nombre').textContent = nombre || '-';
    document.getElementById('ver_direccion').textContent = direccion || '-';
    document.getElementById('ver_pago').textContent = pago || '-';
    document.getElementById('ver_hora_apertura').textContent = horaApertura || '-';
    document.getElementById('ver_hora_cierre').textContent = horaCierre || '-';

    const contenedorAccesores = document.getElementById('ver_accesores');
    contenedorAccesores.innerHTML = accesores.length 
        ? accesores.map(a => `<span class="badge bg-primary me-1">${a.nombres}</span>`).join('')
        : '<span class="text-muted">Sin asignar</span>';

    const img = document.getElementById('ver_imagen');
    if (imagen) {
        img.src = `/storage/${imagen}`;
        img.classList.remove('d-none');
    } else {
        img.classList.add('d-none');
    }
});

document.getElementById('btnCapturarUbicacion').addEventListener('click', () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            document.getElementById('lat_actual').value = lat;
            document.getElementById('lng_actual').value = lng;
            actualizarMapa();
        }, err => alert('Error al obtener ubicación: ' + err.message), {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        });
    } else {
        alert('Tu navegador no soporta geolocalización');
    }
});

let mapa, marcadorInicio, marcadorDestino, controlRuta;

function actualizarMapa() {
    const lat1 = parseFloat(document.getElementById('lat_actual').value);
    const lng1 = parseFloat(document.getElementById('lng_actual').value);
    const lat2 = parseFloat(document.getElementById('lat_destino').value);
    const lng2 = parseFloat(document.getElementById('lng_destino').value);

    if (!mapa) {
        mapa = L.map('mapa').setView([lat1 || -12.07, lng1 || -75.21], 14);
        // Las calles deben ir sobre la línea, por eso las agregamos después de la ruta
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            zIndex: 1
        }).addTo(mapa);
    }

    if (marcadorInicio) mapa.removeLayer(marcadorInicio);
    if (marcadorDestino) mapa.removeLayer(marcadorDestino);

    if (lat1 && lng1)
        marcadorInicio = L.marker([lat1, lng1], { zIndexOffset: 1000 })
            .addTo(mapa)
            .bindPopup("Ubicación actual");

    if (lat2 && lng2)
        marcadorDestino = L.marker([lat2, lng2], { zIndexOffset: 1000 })
            .addTo(mapa)
            .bindPopup("Destino");

    if (controlRuta) mapa.removeControl(controlRuta);

    if (lat1 && lng1 && lat2 && lng2) {
        controlRuta = L.Routing.control({
            waypoints: [L.latLng(lat1, lng1), L.latLng(lat2, lng2)],
            lineOptions: {
                styles: [{ color: 'red', weight: 4, opacity: 0.8 }],
                // 👇 aseguramos que la línea quede por debajo del mapa
                zIndex: 1
            },
            createMarker: function() { return null; },
            addWaypoints: false,
            draggableWaypoints: false,
            routeWhileDragging: false,
            show: false,
            altLineOptions: { styles: [{ opacity: 0 }] }
        })
        .on('routesfound', function(e) {
            const route = e.routes[0];
            const distanciaKm = route.summary.totalDistance / 1000;
            const duracionSeg = route.summary.totalTime;

            const duracionMinAuto = duracionSeg / 60;
            const duracionMinPie = (distanciaKm / 5) * 60;

            function formatearTiempo(min) {
                if (min >= 60) {
                    const h = Math.floor(min / 60);
                    const m = Math.round(min % 60);
                    return `${h} h ${m} min`;
                } else {
                    return `${Math.round(min)} min`;
                }
            }

            document.getElementById('distancia').innerHTML = `
                🚗 En auto: <strong>${formatearTiempo(duracionMinAuto)}</strong> |
                🚶‍♂️ A pie: <strong>${formatearTiempo(duracionMinPie)}</strong> |
                📏 Distancia: <strong>${distanciaKm.toFixed(2)} km</strong>
            `;
        })
        .addTo(mapa);

        // 👇 esto asegura que las calles (tileLayer) estén sobre la ruta
        mapa.eachLayer(layer => {
            if (layer instanceof L.TileLayer) {
                layer.setZIndex(1000);
            }
        });
    }
}

['lat_actual', 'lng_actual', 'lat_destino', 'lng_destino'].forEach(id => {
    document.getElementById(id).addEventListener('change', actualizarMapa);
});

// Mostrar vista previa de imagen seleccionada
document.getElementById('imagen_lugar').addEventListener('change', function(event) {
    const input = event.target;
    const preview = document.getElementById('previewImagen');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        }

        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = "#";
        preview.classList.add('d-none');
    }
});

//Buscar y Seleccionar un Accesor
const inputBuscar = document.getElementById('buscarAccesor');
const resultadosDiv = document.getElementById('resultadosBusqueda');
const tablaAccesores = document.getElementById('tablaAccesoresSeleccionados').querySelector('tbody');
const inputIds = document.getElementById('accesor_id');

// 🔹 Simulación de lista de accesores desde Blade (ya disponible en tu controlador)
const accesores = @json($accesors);

// 🔍 Buscar accesores por nombre o DNI
inputBuscar.addEventListener('keyup', () => {
  const texto = inputBuscar.value.toLowerCase().trim();
  resultadosDiv.innerHTML = '';

  if (texto.length < 2) {
    resultadosDiv.classList.add('d-none');
    return;
  }

  const resultados = accesores.filter(a => 
    a.nombres.toLowerCase().includes(texto) || 
    a.dni.includes(texto)
  );

  if (resultados.length === 0) {
    resultadosDiv.innerHTML = '<div class="list-group-item text-muted">No se encontraron resultados.</div>';
  } else {
    resultados.forEach(a => {
      const item = document.createElement('button');
      item.type = 'button';
      item.className = 'list-group-item list-group-item-action';
      item.textContent = `${a.nombres} — DNI: ${a.dni}`;
      item.onclick = () => seleccionarAccesor(a);
      resultadosDiv.appendChild(item);
    });
  }

  resultadosDiv.classList.remove('d-none');
});

// ➕ Seleccionar un accesor
function seleccionarAccesor(accesor) {
  // Ocultamos resultados y limpiamos búsqueda
  resultadosDiv.classList.add('d-none');
  inputBuscar.value = '';

  // Evitamos duplicados
  if (document.querySelector(`#fila-${accesor.id}`)) return;

  const fila = document.createElement('tr');
  fila.id = `fila-${accesor.id}`;
  fila.innerHTML = `
    <td>${accesor.nombres}</td>
    <td>${accesor.dni}</td>
    <td>
      <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarAccesor(${accesor.id})">
        <i class="fas fa-trash"></i>
      </button>
    </td>
  `;
  tablaAccesores.appendChild(fila);

  actualizarInputIds();
}

// Eliminar un accesor
function eliminarAccesor(id) {
  document.querySelector(`#fila-${id}`)?.remove();
  actualizarInputIds();
}

// Actualizar campo oculto con los IDs seleccionados
function actualizarInputIds() {
  const ids = Array.from(tablaAccesores.querySelectorAll('tr')).map(fila =>
    parseInt(fila.id.replace('fila-', ''))
  );
  
  // Borra campos anteriores
  document.querySelectorAll('input[name="accesores[]"]').forEach(el => el.remove());

  // Crea un input oculto por cada accesor seleccionado
  ids.forEach(id => {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'accesores[]';
    input.value = id;
    document.getElementById('formCategoria').appendChild(input);
  });
}
</script>
@endpush
@endsection