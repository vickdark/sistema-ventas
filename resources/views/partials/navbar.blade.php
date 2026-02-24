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
        @php
            $onCentralDomain = in_array(request()->getHost(), config('tenancy.central_domains', []));
            
            // Lógica unificada para mostrar notificaciones:
            // 1. Si es Tenant: verificamos ruta y permiso 'notifications.low-stock'
            // 2. Si es Central: mostramos el icono (luego implementaremos la lógica específica)
            
            $showNotifications = false;

            if (function_exists('tenant') && tenant() && !$onCentralDomain) {
                // Estamos en Tenant
                $showNotifications = Route::has('notifications.low-stock') && 
                                     auth()->check() && 
                                     auth()->user()->can('notifications.low-stock');
            } else {
                // En el dominio central, por ahora no mostramos notificaciones de stock bajo
                $showNotifications = false; 
            }
        @endphp

        @if($showNotifications)
        <div class="dropdown me-3">
            <button class="btn btn-light position-relative border-0 shadow-sm rounded-circle" type="button" id="notificationBtn" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px;">
                <i class="fa-regular fa-bell text-secondary"></i>
                <span id="notificationDot" class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle d-none">
                    <span class="visually-hidden">New alerts</span>
                </span>
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-0 overflow-hidden" aria-labelledby="notificationBtn" style="width: 320px;">
                @if(function_exists('tenant') && tenant())
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
                @else
                <!-- Contenido específico para Central -->
                <div class="p-3 border-bottom bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold text-dark">Notificaciones Central</h6>
                    </div>
                </div>
                <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                    <div class="text-center py-5">
                        <i class="fa-regular fa-bell-slash fa-2x text-muted mb-2 opacity-50"></i>
                        <p class="text-muted small mb-0">No tienes notificaciones nuevas.</p>
                    </div>
                </div>
                @endif
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

        {{-- Selector de Sucursal (Solo en Tenant y con Usuario Autenticado) --}}
        @if(function_exists('tenant') && tenant() && auth()->check() && !$onCentralDomain)
            @php
                $user = auth()->user();
                $activeBranchId = session('active_branch_id');
                
                // Obtener sucursales disponibles según el rol
                $branchesQuery = \App\Models\Tenant\Branch::where('is_active', true);
                if (!$user->isAdmin()) {
                    $branchesQuery->where('id', $user->branch_id);
                }
                $availableBranches = $branchesQuery->get();
                
                // Determinar la sucursal actual para mostrar en el botón
                $currentBranch = $availableBranches->firstWhere('id', $activeBranchId) 
                                ?? \App\Models\Tenant\Branch::find($activeBranchId)
                                ?? $availableBranches->first();
            @endphp

            @if($currentBranch)
            <div class="dropdown me-3 border-start ps-3">
                <button class="btn btn-light d-flex align-items-center gap-2 border-0 shadow-sm rounded-pill px-3" 
                        type="button" id="branchSelectorBtn" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false"
                        @if($availableBranches->count() <= 1) disabled @endif>
                    <i class="fa-solid fa-building text-primary small"></i>
                    <span class="small fw-semibold d-none d-md-inline">{{ $currentBranch->name }}</span>
                    @if($availableBranches->count() > 1)
                        <i class="fa-solid fa-chevron-down text-muted small" style="font-size: 0.6rem;"></i>
                    @endif
                </button>
                
                @if($availableBranches->count() > 1)
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2 p-2" aria-labelledby="branchSelectorBtn" style="min-width: 220px;">
                    <li class="px-3 py-2 border-bottom mb-2 text-muted small fw-bold">CAMBIAR SUCURSAL</li>
                    @foreach($availableBranches as $branch)
                    <li>
                        <form action="{{ route('branches.set-active') }}" method="POST">
                            @csrf
                            <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                            <button type="submit" class="dropdown-item rounded-2 py-2 d-flex align-items-center justify-content-between {{ $activeBranchId == $branch->id ? 'bg-primary bg-opacity-10 text-primary active' : '' }}">
                                <span>{{ $branch->name }}</span>
                                @if($activeBranchId == $branch->id)
                                    <i class="fa-solid fa-check ms-2 small"></i>
                                @endif
                            </button>
                        </form>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
            @endif
        @endif

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
                    <a class="dropdown-item rounded-2 py-2" href="{{ request()->routeIs('central.*') ? route('central.profile.index') : route('profile.index') }}">
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
