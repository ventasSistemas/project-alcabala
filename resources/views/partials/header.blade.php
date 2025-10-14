<!--views/partials/header.blade.php-->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top py-3">
    <div class="container-fluid px-4">
        <h5 class="fw-bold text-primary mb-0">
            <i class="fa-solid fa-laptop"></i> Sistema Feria
        </h5>

        <div class="d-flex align-items-center ms-auto">
            <!-- 🔔 Icono de notificaciones -->
            <div class="dropdown me-3">
                <a class="text-secondary position-relative" href="#" role="button" id="notiDropdown" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-bell fa-lg"></i>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" 
                    aria-labelledby="notiDropdown" 
                    style="width: 420px; max-height: 420px; overflow-y: auto;">

                    @forelse(auth()->user()->notifications->sortByDesc('created_at') as $notificacion)
                        <li>
                            <a href="{{ route('notificaciones.leer', $notificacion->id) }}"
                                class="dropdown-item d-flex align-items-start {{ $notificacion->read_at ? 'bg-white' : 'bg-secondary bg-opacity-10' }}">
                                <i class="fa-solid fa-money-bill-wave text-success me-2 mt-1"></i>
                                <div>
                                    <strong>{{ $notificacion->data['titulo'] }}</strong>
                                    <div class="small text-muted">{{ $notificacion->data['mensaje'] }}</div>
                                    <div class="small text-end text-secondary">{{ $notificacion->data['fecha'] }}</div>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                    @empty
                        <li><span class="dropdown-item text-center text-muted">No hay notificaciones</span></li>
                    @endforelse
                </ul>
            </div>

            <span class="me-3 text-secondary">
                <i class="fas fa-user-circle me-1"></i> {{ auth()->user()->name ?? 'Invitado' }}
            </span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-sign-out-alt me-1"></i> Salir
                </button>
            </form>
        </div>
    </div>
</nav>

