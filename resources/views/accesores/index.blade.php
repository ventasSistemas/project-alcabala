<!--views/accesores/index.blade.php-->
@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Título y botón -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary mb-0 d-flex align-items-center">
            <i class="fas fa-users-cog me-2"></i> Gestión de Accesores
        </h4>
        <button class="btn btn-primary rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAccesor">
            <i class="fas fa-plus-circle me-1"></i> Nuevo Accesor
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
                            <th>Nombres</th>
                            <th>Dirección</th>
                            <th>Celular</th>
                            <th>DNI</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($accesores as $index => $accesor)
                            <tr class="text-center">
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-semibold">{{ $accesor->nombres }}</td>
                                <td>{{ $accesor->direccion ?? '-' }}</td>
                                <td>{{ $accesor->celular ?? '-' }}</td>
                                <td>{{ $accesor->dni }}</td>
                                <td>
                                    <button 
                                        class="btn btn-sm btn-outline-warning me-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalAccesor"
                                        data-id="{{ $accesor->id }}"
                                        data-nombres="{{ $accesor->nombres }}"
                                        data-direccion="{{ $accesor->direccion }}"
                                        data-celular="{{ $accesor->celular }}"
                                        data-dni="{{ $accesor->dni }}"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('accesores.destroy', $accesor->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este accesor?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                    No hay accesores registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $accesores->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear/Editar Accesor -->
<div class="modal fade" id="modalAccesor" tabindex="-1" aria-labelledby="modalAccesorLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-semibold" id="modalAccesorLabel">Nuevo Accesor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAccesor" method="POST" action="{{ route('accesores.store') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="accesor_id" name="id">

                    <div class="mb-3">
                        <label for="nombres_completos" class="form-label fw-semibold">Nombres Completos</label>
                        <input type="text" class="form-control" id="nombres" name="nombres" required>
                    </div>

                    <div class="mb-3">
                        <label for="direccion" class="form-label fw-semibold">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Ej: Jr. Los Álamos 123">
                    </div>

                    <div class="mb-3">
                        <label for="celular" class="form-label fw-semibold">Celular</label>
                        <input type="text" class="form-control" id="celular" name="celular" maxlength="9" placeholder="Ej: 987654321">
                    </div>

                    <div class="mb-3">
                        <label for="dni" class="form-label fw-semibold">DNI</label>
                        <input type="text" class="form-control" id="dni" name="dni" maxlength="8" required placeholder="Ej: 12345678">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                        <input type="email" name="email" id="email" required autofocus
                            class="form-control" placeholder="ejemplo@correo.com">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
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

@push('scripts')
<script>
    const modalAccesor = document.getElementById('modalAccesor');
    modalAccesor.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        const form = document.getElementById('formAccesor');

        if (button && button.dataset.id) {
            // --- MODO EDICIÓN ---
            document.getElementById('modalAccesorLabel').textContent = 'Editar Accesor';
            form.action = `/accesores/${button.dataset.id}`;
            form.querySelector('input[name="_method"]')?.remove();

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);

            document.getElementById('accesor_id').value = button.dataset.id;
            document.getElementById('nombres').value = button.dataset.nombres;
            document.getElementById('direccion').value = button.dataset.direccion || '';
            document.getElementById('celular').value = button.dataset.celular || '';
            document.getElementById('dni').value = button.dataset.dni;
        } else {
            // --- MODO CREACIÓN ---
            document.getElementById('modalAccesorLabel').textContent = 'Nuevo Accesor';
            form.action = "{{ route('accesores.store') }}";
            form.querySelector('input[name="_method"]')?.remove();
            form.reset();
        }
    });
</script>
@endpush

@endsection