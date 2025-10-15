<nav id="sidebar" class="d-flex flex-column flex-shrink-0 text-white p-3">
    <div class="text-center mb-4">
        <h4 class="fw-bold"><i class="fa-solid fa-house"></i> Alcabala</h4>
        <small class="text-light opacity-75">Panel Administrativo</small>
    </div>

    <ul class="nav nav-pills flex-column mb-auto">
        @if(auth()->user()->accesor)
            {{-- Solo accesores --}}
            <li>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('puestos.index') }}" class="nav-link"><i class="fas fa-store me-2"></i> Puestos</a>
            </li>
            <li>
                <a href="{{ route('clientes.index') }}" class="nav-link"><i class="fas fa-users me-2"></i> Clientes y Pagos</a>
            </li>
            <li>
                <a href="{{ route('pagos.index') }}" class="nav-link"><i class="fas fa-credit-card me-2"></i> Historial de Pagos/Caja</a>
            </li>
        @else
            {{-- Usuarios normales, todo --}}
            <li><a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line me-2"></i> Dashboard
            </a></li>
            <li><a href="{{ route('accesores.index') }}" class="nav-link"><i class="fas fa-tools me-2"></i> Accesores</a></li>
            <li><a href="{{ route('categorias.index') }}" class="nav-link"><i class="fas fa-tags me-2"></i> Categorías</a></li>
            <li><a href="{{ route('puestos.index') }}" class="nav-link"><i class="fas fa-store me-2"></i> Puestos</a></li>
            <li><a href="{{ route('clientes.index') }}" class="nav-link"><i class="fas fa-users me-2"></i> Clientes y Pagos</a></li>
            <li><a href="{{ route('pagos.index') }}" class="nav-link"><i class="fas fa-credit-card me-2"></i> Historial de Pagos/Caja</a></li>
            <li><a href="{{ route('reports.index') }}" class="nav-link"><i class="fa-solid fa-chart-simple"></i> Reportes</a></li>
        @endif
    </ul>

    <hr class="border-light">
    <div class="text-center">
        <i class="fas fa-user-shield me-1"></i> {{ auth()->user()->name ?? 'Admin' }}
    </div>
</nav>
