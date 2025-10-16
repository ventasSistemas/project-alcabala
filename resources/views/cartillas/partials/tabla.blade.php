<!--views/cartillas/partials/tabla.blade.php-->
@if($cartillas->isEmpty())
    <div class="alert alert-warning text-center fs-5 py-3 shadow-sm">
        Este cliente no tiene cartillas generadas aún.
    </div>
@else
    <form id="formPagos" action="{{ route('cartillas.ingresarPagos') }}" method="POST" target="_blank" onsubmit="setTimeout(() => location.reload(), 1500)">
        @csrf
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle shadow-sm mt-3">
                <thead class="table-info text-center align-middle">
                    <tr class="fs-5">
                        <th>
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th>#</th>
                        <th>N° Puesto</th>
                        <th>Fecha a Pagar</th>
                        <th>Monto (S/)</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartillas as $cartilla)
                        <tr class="text-center align-middle">
                            <td>
                                @if(!in_array($cartilla->observacion, ['Pagado', 'Pago Atrasado']))
                                    <input type="checkbox" name="cartillas_id[]" value="{{ $cartilla->id }}" class="form-check-input checkPago">
                                @else
                                    <input type="checkbox" class="form-check-input" checked disabled>
                                @endif
                            </td>

                            <td class="fw-bold">{{ $cartilla->nro }}</td>
                            <td>{{ $cartilla->puesto->numero_puesto ?? 'N/D' }}</td>
                            <td>{{ \Carbon\Carbon::parse($cartilla->fecha_pagar)->format('d/m/Y') }}</td>
                            <td class="fw-semibold">{{ number_format($cartilla->cuota, 2) }}</td>

                            <td>
                                @switch($cartilla->observacion)
                                    @case('Pagado')
                                        <span class="badge bg-success fs-6 px-3 py-2">Pagado</span>
                                        @break
                                    @case('Pendiente')
                                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">Pendiente</span>
                                        @break
                                    @case('Pago Atrasado')
                                        <span class="badge bg-danger fs-6 px-3 py-2">Pago Atrasado</span>
                                        @break
                                    @case('No Pago')
                                        <span class="badge bg-secondary fs-6 px-3 py-2">No Pago</span>
                                        @break
                                    @default
                                        <span class="badge bg-light text-dark fs-6 px-3 py-2">{{ $cartilla->observacion ?? 'N/A' }}</span>
                                @endswitch
                            </td>

                            <td>
                                @if($cartilla->observacion === 'Pagado' || $cartilla->observacion === 'Pago Atrasado')
                                    <button class="btn btn-outline-secondary btn-sm" disabled>
                                        <i class="bi bi-lock"></i>
                                    </button>
                                @else
                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                            Cambiar Estado
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if($cartilla->observacion !== 'Pagado')
                                                <li>
                                                    <a href="{{ route('cartillas.cambiarEstado', [$cartilla->id, 'Pagado']) }}" 
                                                        class="dropdown-item text-success cambiar-estado"
                                                        target="_blank"
                                                        onclick="setTimeout(() => location.reload(), 1500)">
                                                        <i class="bi bi-check-circle"></i> Marcar Pagado
                                                    </a>
                                                </li>
                                            @endif
                                            @if($cartilla->observacion === 'Pendiente')
                                                <li>
                                                    <a href="{{ route('cartillas.cambiarEstado', [$cartilla->id, 'Pago Atrasado']) }}" 
                                                        class="dropdown-item text-danger cambiar-estado"
                                                        target="_blank"
                                                        onclick="setTimeout(() => location.reload(), 1500)">
                                                        <i class="bi bi-clock-history"></i> Pago Atrasado
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('cartillas.cambiarEstado', [$cartilla->id, 'No Pago']) }}" class="dropdown-item text-secondary">
                                                        <i class="bi bi-x-circle"></i> No Pago
                                                    </a>
                                                </li>
                                            @elseif($cartilla->observacion === 'No Pago')
                                                <li>
                                                    <a href="{{ route('cartillas.cambiarEstado', [$cartilla->id, 'Pago Atrasado']) }}" class="dropdown-item text-danger">
                                                        <i class="bi bi-clock-history"></i> Pago Atrasado
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Botón de registrar pagos rápidos -->
        <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn btn-success btn-lg shadow-sm">
                <i class="bi bi-cash-coin"></i> Registrar Pago(s) Rápido(s)
            </button>
        </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const formPagos = document.getElementById('formPagos');

        // Log para saber si el formulario se detecta
        console.log("✅ Script cargado, formulario detectado:", formPagos ? "Sí" : "No");

        formPagos?.addEventListener('submit', (e) => {
            console.log("🟡 Enviando formulario de pagos múltiples...");

            // Verificar si hay cartillas seleccionadas
            const seleccionadas = Array.from(document.querySelectorAll('.checkPago:checked')).map(ch => ch.value);
            console.log("📋 Cartillas seleccionadas:", seleccionadas);

            if (seleccionadas.length === 0) {
                console.warn("⚠️ No hay cartillas seleccionadas.");
                alert("Selecciona al menos una cartilla para registrar el pago.");
                e.preventDefault();
                return;
            }

            // Verificar destino
            console.log("🔗 Acción del formulario:", formPagos.action);
            console.log("🔍 Método:", formPagos.method);
            console.log("🪟 Target:", formPagos.target);

            // Esperar recarga
            setTimeout(() => {
                console.log("🔄 Recargando la página luego del envío...");
                location.reload();
            }, 1500);
        });

        // Checkbox "Seleccionar todo"
        const selectAll = document.getElementById('selectAll');
        selectAll?.addEventListener('change', function () {
            const checks = document.querySelectorAll('.checkPago');
            checks.forEach(ch => ch.checked = this.checked);
            console.log("☑️ Estado 'Seleccionar todo':", this.checked);
        });
    });
</script>

@endif