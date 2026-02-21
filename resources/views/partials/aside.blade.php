<aside class="app-sidebar">
    <div class="app-sidebar-inner">
        <div class="app-sidebar-brand px-4 py-3 d-flex align-items-center gap-3">
            @php
                $isTenant = function_exists('tenant') && tenant();
                $logo = $isTenant ? tenant('logo') : null;
                $brandTitle = $isTenant ? (tenant('business_name') ?? ucfirst(tenant('id'))) : config('app.name', 'Laravel');
                $brandSubtitle = $isTenant ? 'Portal de Empresa' : 'Administración Central';
            @endphp

            <div class="app-brand-logo bg-white bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center shadow-sm overflow-hidden" style="width: 45px; height: 45px; min-width: 45px;">
                @if($logo)
                    <img src="{{ asset('storage/' . $logo) }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; padding: 4px;">
                @else
                    <div class="bg-primary bg-opacity-10 w-100 h-100 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-rocket text-primary fs-4"></i>
                    </div>
                @endif
            </div>

            <div class="app-brand-info overflow-hidden">
                <span class="app-brand-text fw-bold text-white fs-5 lh-1 d-block text-truncate">{{ $brandTitle }}</span>
                <span class="text-sidebar-muted fw-medium d-block text-truncate" style="font-size: 0.65rem; letter-spacing: 0.05em; text-transform: uppercase;">{{ $brandSubtitle }}</span>
            </div>
        </div>

        <nav class="nav flex-column app-sidebar-nav" id="sidebarAccordion">
            @php
                $onCentralDomain = in_array(request()->getHost(), config('tenancy.central_domains', []));
                $user = auth()->user();
                $isOwner = auth('owner')->check();

                if ($isOwner) {
                    $permissions = collect();
                } else {
                    $role = $user ? $user->role : null;
                    $permissions = $role 
                        ? $role->permissions()
                            ->where('is_menu', true)
                            ->orderBy('order')
                            ->get()
                            ->filter(function($p) use ($onCentralDomain) {
                                if (!$onCentralDomain) {
                                    return !in_array($p->module, ['Tenancy', 'Central', 'Tenants', 'Administración Central']);
                                }
                                return true;
                            })
                        : collect();
                }
            @endphp

            @if($permissions->isEmpty() && !$isOwner)
                <div class="p-3 text-muted small">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    No hay opciones de menú disponibles.
                </div>
            @endif

            {{-- Sección de Administración Central --}}
            @if(auth('owner')->check())
                <div class="sidebar-heading px-4 mt-3 mb-1 text-sidebar-muted text-uppercase fw-bold" style="font-size: 0.6rem; letter-spacing: 0.05em; opacity: 0.8;">
                    Administración Central
                </div>
                <a class="nav-link {{ request()->routeIs('central.dashboard') ? 'active' : '' }}" href="{{ route('central.dashboard') }}">
                    <i class="fa-solid fa-chart-line"></i>
                    <span class="app-link-text">Dashboard Central</span>
                </a>
                <a class="nav-link {{ request()->routeIs('central.tenants.*') ? 'active' : '' }}" href="{{ route('central.tenants.index') }}">
                    <i class="fa-solid fa-building-shield"></i>
                    <span class="app-link-text">Inquilinos</span>
                </a>
                <a class="nav-link {{ request()->routeIs('central.payment-notifications.*') ? 'active' : '' }}" href="{{ route('central.payment-notifications.index') }}">
                    <i class="fa-solid fa-receipt"></i>
                    <span class="app-link-text">Bandeja de Pagos</span>
                </a>
                <a class="nav-link {{ request()->routeIs('central.settings.*') ? 'active' : '' }}" href="{{ route('central.settings.index') }}">
                    <i class="fa-solid fa-gears"></i>
                    <span class="app-link-text">Configuraciones</span>
                </a>
                <a class="nav-link {{ request()->routeIs('central.maintenance.*') ? 'active' : '' }}" href="{{ route('central.maintenance.index') }}">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                    <span class="app-link-text">Mantenimiento</span>
                </a>
                <a class="nav-link {{ request()->routeIs('central.metrics.*') ? 'active' : '' }}" href="{{ route('central.metrics.index') }}">
                    <i class="fa-solid fa-chart-simple"></i>
                    <span class="app-link-text">Métricas y Logs</span>
                </a>
                <a class="nav-link {{ request()->routeIs('central.users.*') ? 'active' : '' }}" href="{{ route('central.users.index') }}">
                    <i class="fa-solid fa-users"></i>
                    <span class="app-link-text">Usuarios Centrales</span>
                </a>
            @endif

            @php 
                $groupedPermissions = $permissions->groupBy('module');
            @endphp

            @foreach($groupedPermissions as $moduleName => $moduleItems)
                @if($loop->first)
                    @foreach($moduleItems as $item)
                        @php
                            $isActive = request()->routeIs($item->slug) || request()->routeIs(explode('.', $item->slug)[0] . '.*');
                            $routeExists = Route::has($item->slug);
                            $itemUrl = $routeExists ? route($item->slug) : '#';
                        @endphp
                        <a class="nav-link {{ $isActive ? 'active' : '' }} shadow-none rounded-2" href="{{ $itemUrl }}">
                            <i class="{{ $item->icon ?: 'fa-solid fa-circle-dot' }} me-2" style="font-size: 1.1rem; width: 1.5rem; text-align: center;"></i>
                            <span class="app-link-text">{{ $item->nombre }}</span>
                        </a>
                    @endforeach
                @else
                    @php
                        $isModuleActive = false;
                        foreach($moduleItems as $item) {
                            if (request()->routeIs($item->slug) || request()->routeIs(explode('.', $item->slug)[0] . '.*')) {
                                $isModuleActive = true;
                                break;
                            }
                        }
                        $collapseId = 'collapse-' . \Illuminate\Support\Str::slug($moduleName);
                    @endphp

                    <div class="sidebar-group mb-1">
                        <button class="btn sidebar-group-toggle w-100 d-flex align-items-center justify-content-between px-3 py-2 text-start shadow-none {{ $isModuleActive ? '' : 'collapsed' }}" 
                                type="button" 
                                data-toggle="collapse-custom" 
                                data-target="#{{ $collapseId }}" 
                                aria-expanded="{{ $isModuleActive ? 'true' : 'false' }}" 
                                aria-controls="{{ $collapseId }}">
                            <span class="sidebar-group-label fw-bold text-uppercase text-sidebar-muted">{{ $moduleName }}</span>
                            <i class="fa-solid fa-chevron-right group-chevron text-sidebar-muted"></i>
                        </button>
                        
                        <div class="collapse {{ $isModuleActive ? 'show' : '' }}" id="{{ $collapseId }}">
                            <div class="sidebar-group-content pb-2">
                                @foreach($moduleItems as $item)
                                    @php
                                        $isActive = request()->routeIs($item->slug) || request()->routeIs(explode('.', $item->slug)[0] . '.*');
                                        $routeExists = Route::has($item->slug);
                                        $itemUrl = $routeExists ? route($item->slug) : '#';
                                    @endphp
                                    <a class="nav-link {{ $isActive ? 'active' : '' }} shadow-none rounded-2" href="{{ $itemUrl }}">
                                        <i class="{{ $item->icon ?: 'fa-solid fa-circle-dot' }} me-2 opacity-75"></i>
                                        <span class="app-link-text">{{ $item->nombre }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </nav>

        <div class="app-sidebar-footer">
            @php
                $isOwner = auth('owner')->check();
                $authenticatedUser = $isOwner ? auth('owner')->user() : auth()->user();
                
                // Safe route detection
                if ($isOwner) {
                    $logoutRoute = route('central.logout');
                } elseif (Route::has('logout')) {
                    $logoutRoute = route('logout');
                } else {
                    $logoutRoute = '#';
                }
            @endphp

            @if($authenticatedUser)
            <div class="app-user-card d-flex align-items-center gap-3 mb-3" style="color: white !important;">
                <div class="app-user-avatar d-flex align-items-center justify-content-center shadow-sm">
                    {{ strtoupper(substr($authenticatedUser->name ?? 'U', 0, 1)) }}
                </div>
                <div class="app-user-info overflow-hidden flex-grow-1">
                    <div class="fw-bold text-white text-truncate small">{{ $authenticatedUser->name ?? 'Usuario' }}</div>
                    <div class="text-sidebar-muted text-truncate" style="font-size: 0.7rem;">
                        <i class="fa-solid fa-shield-halved me-1 text-primary opacity-75"></i>
                        {{ $isOwner ? 'Administrador Central' : (optional($authenticatedUser->role)->nombre ?? 'Sin Rol') }}
                    </div>
                </div>
            </div>
            
            <form id="logout-form-aside" method="POST" action="{{ $logoutRoute }}" class="d-none">
                @csrf
            </form>
            
            <button class="btn logout-btn w-100 d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 shadow-sm" type="button" onclick="handleLogout('logout-form-aside')" style="background-color: #dc3545 !important; color: white !important;">
                <i class="fa-solid fa-power-off small"></i>
                <span class="app-link-text fw-semibold small">Cerrar sesión</span>
            </button>
            @endif
        </div>
    </div>
</aside>

<script>
/**
 * Maneja el cierre de sesión utilizando el módulo global Notify (Notifications.js)
 * @param {string} formId ID del formulario a enviar
 */
async function handleLogout(formId) {
    const confirmed = await window.Notify.confirm({
        title: '¿Cerrar sesión?',
        text: 'Tu sesión actual se finalizará.',
        confirmButtonText: 'Sí, salir',
        cancelButtonText: 'Mantenerse'
    });

    if (confirmed) {
        document.getElementById(formId).submit();
    }
}

</script>
