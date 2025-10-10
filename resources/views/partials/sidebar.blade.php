<!--views/partials/sidebar.blade.php-->
<nav id="sidebar" class="d-flex flex-column flex-shrink-0 text-white p-3">
    <div class="text-center mb-4">
        <h4 class="fw-bold"><i class="fa-solid fa-house"></i> Alcabala</h4>
        <small class="text-light opacity-75">Panel Administrativo</small>
    </div>

    <ul class="nav nav-pills flex-column mb-auto">
        <li><a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-line me-2"></i> Dashboard
        </a></li>
        <li><a href="{{ route('accesores.index') }}" class="nav-link"><i class="fas fa-tools me-2"></i> Accesores</a></li>
        <li><a href="{{ route('categorias.index') }}" class="nav-link"><i class="fas fa-tags me-2"></i> Categorías</a></li>
        <li><a href="{{ route('puestos.index') }}" class="nav-link"><i class="fas fa-store me-2"></i> Puestos</a></li>
        <li><a href="{{ route('clientes.index') }}" class="nav-link"><i class="fas fa-users me-2"></i> Clientes</a></li>
        <li><a href="{{ route('pagos.index') }}" class="nav-link"><i class="fas fa-credit-card me-2"></i> Pagos</a></li>
        <li><a href="{{ route('reports.index') }}" class="nav-link"><i class="fas fa-file-alt me-2"></i> Reportes</a></li>
    </ul>

    <hr class="border-light">
    <div class="text-center">
        <i class="fas fa-user-shield me-1"></i> {{ auth()->user()->name ?? 'Admin' }}
    </div>
</nav>
