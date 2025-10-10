<!--views/partials/header.blade.php-->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top py-3">
    <div class="container-fluid px-4">
        <h5 class="fw-bold text-primary mb-0">
            <i class="fa-solid fa-laptop"></i> Sistema Feria
        </h5>

        <div class="d-flex align-items-center ms-auto">
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
