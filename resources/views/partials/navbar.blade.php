<nav class="app-topbar sticky-top">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-light" type="button" data-toggle="sidebar-mini" aria-label="Toggle sidebar">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="app-breadcrumbs">
            <i class="fa-solid fa-house"></i>
            <span class="text-muted">/</span>
            <span>Inicio</span>
        </div>
    </div>
    <div class="app-topbar-actions">
        @if(Route::has('notifications.low-stock'))
        <div class="dropdown me-3">
            <button class="btn btn-light position-relative border-0 shadow-sm rounded-circle" type="button" id="notificationBtn" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px;">
                <i class="fa-regular fa-bell text-secondary"></i>
                <span id="notificationDot" class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle d-none">
                    <span class="visually-hidden">New alerts</span>
                </span>
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-0 overflow-hidden" aria-labelledby="notificationBtn" style="width: 320px;">
                <div class="p-3 border-bottom bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold text-dark">Notificaciones</h6>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1" style="font-size: 0.65rem;">STOCK BAJO</span>
                    </div>
                </div>
                <div id="notificationList" class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                    <!-- Items cargados vía AJAX -->
                    <div class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
                    </div>
                </div>
                <div class="p-2 text-center border-top bg-light">
                    <a href="{{ route('products.index') }}" class="small text-decoration-none fw-bold text-primary">Ver todo el inventario</a>
                </div>
            </div>
        </div>
        @endif
        
        @php
            $isOwner = auth('owner')->check();
            if ($isOwner) {
                // For owner, force central logout. We know this route exists now.
                $logoutRoute = route('central.logout');
            } elseif (Route::has('logout')) {
                // For tenants, use standard logout if it exists
                $logoutRoute = route('logout');
            } else {
                // Fallback
                $logoutRoute = '#';
            }
        @endphp
        <form id="logout-form-navbar" method="POST" action="{{ $logoutRoute }}" class="d-none">
            @csrf
        </form>

        <div class="dropdown app-profile border-start ps-3">
            <div class="d-flex align-items-center gap-2 dropdown-toggle dropdown-toggle-nocaret" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="app-user-avatar sm">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="d-none d-sm-block me-1">
                    <div class="fw-semibold small lh-1">{{ auth()->user()->name ?? 'Usuario' }}</div>
                    <div class="text-muted small" style="font-size: 0.75rem;">{{ optional(auth()->user()->role)->nombre ?? 'Usuario' }}</div>
                </div>
                <i class="fa-solid fa-chevron-down text-muted" style="font-size: 0.7rem;"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2 p-2" style="min-width: 200px;">
                <li class="px-3 py-2 d-md-none border-bottom mb-2">
                    <div class="fw-bold small">{{ auth()->user()->name ?? 'Usuario' }}</div>
                    <div class="text-muted small">{{ optional(auth()->user()->role)->nombre ?? 'Usuario' }}</div>
                </li>
                <li>
                    <a class="dropdown-item rounded-2 py-2" href="#">
                        <i class="fa-solid fa-user-circle me-2 text-muted"></i> Mi Perfil
                    </a>
                </li>

                <li><hr class="dropdown-divider mx-2"></li>
                <li>
                    <button class="dropdown-item rounded-2 py-2 text-danger" type="button" onclick="handleLogout('logout-form-navbar')">
                        <i class="fa-solid fa-power-off me-2"></i> Cerrar Sesión
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>
