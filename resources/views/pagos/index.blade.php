<!-- resources/views/pagos/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary mb-0 d-flex align-items-center">
            <i class="fa-solid fa-laptop-file"></i> Historial de Pagos / Caja
        </h4>

        {{-- 🔹 Botón Enviar todo a Caja --}}
        <form action="{{ route('pagos.enviarTodoCaja') }}" method="POST" id="formEnviarCaja">
            @csrf
            <button type="submit"
                class="btn {{ $totalPendiente > 0 ? 'btn-success' : 'btn-secondary' }}"
                {{ $totalPendiente > 0 ? '' : 'disabled' }}
                onclick="return confirmarEnvio(event)">
                <i class="bi bi-cash-stack"></i> Enviar todo a Caja
            </button>
        </form>
    </div>

    <br>

    @if($pagos->isEmpty())
        <div class="alert alert-warning text-center">
            <i class="bi bi-exclamation-circle"></i> No hay pagos registrados todavía.
        </div>
    @else
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-bordered align-middle">
                <thead class="table-info text-center">
                    <tr>
                        <th>#</th>
                        <th>Número de Pago</th>
                        <th>Fecha del Pago</th>
                        <th>Fecha Programada</th>
                        <th>Monto (S/)</th>
                        <th>Estado</th>
                        <th>Enviado a Caja</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalGeneral = 0; @endphp

                    @foreach ($pagos as $index => $pago)
                        @php
                            $totalGeneral += $pago->monto;
                            $filaPendiente = !$pago->enviado_a_caja ? 'table-warning' : '';
                        @endphp
                        <tr class="text-center {{ $filaPendiente }}">
                            <td>{{ $index + 1 }}</td>
                            <td><span class="fw-semibold">{{ $pago->numero_pago }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($pago->fecha_a_pagar)->format('d/m/Y') }}</td>
                            <td class="fw-bold text-success">S/ {{ number_format($pago->monto, 2) }}</td>
                            <td>
                                @if($pago->estado === 'PAGADO')
                                    <span class="badge bg-success">{{ $pago->estado }}</span>
                                @else
                                    <span class="badge bg-danger">{{ $pago->estado }}</span>
                                @endif
                            </td>
                            <td>
                                @if($pago->enviado_a_caja)
                                    <span class="badge bg-primary">Sí</span>
                                @else
                                    <span class="badge bg-warning text-dark">No</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr class="table-primary text-center fw-bold">
                        <td colspan="4" class="text-end">TOTAL GENERAL:</td>
                        <td class="text-success">S/ {{ number_format($totalGeneral, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                    <tr class="table-warning text-center fw-bold">
                        <td colspan="4" class="text-end">TOTAL PENDIENTE DE CAJA:</td>
                        <td class="text-danger">S/ {{ number_format($totalPendiente, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Confirmación elegante con SweetAlert2 antes de enviar a Caja
    function confirmarEnvio(event) {
        event.preventDefault(); // Detiene el envío inmediato del formulario

        Swal.fire({
            title: '¿Enviar todo a Caja?',
            text: "Se registrarán todos los pagos pendientes en la caja general.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, enviar',
            cancelButtonText: 'Cancelar',
            background: '#f8f9fa',
            color: '#212529',
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formEnviarCaja').submit();
            }
        });

        return false;
    }

    // 🔹 Mostrar mensajes de éxito o error usando SweetAlert2
    document.addEventListener('DOMContentLoaded', function () {
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000,
                background: '#f0fff4',
                color: '#155724',
                showClass: {
                    popup: 'animate__animated animate__fadeInUp'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutDown'
                }
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                showConfirmButton: true,
                confirmButtonColor: '#d33',
                background: '#fff5f5',
                color: '#721c24',
                showClass: {
                    popup: 'animate__animated animate__fadeInUp'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutDown'
                }
            });
        @endif
    });
</script>
@endpush