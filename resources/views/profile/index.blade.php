@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Mi Perfil</h1>
            <p class="text-muted small mb-0">Gestiona tu información personal y seguridad de tu cuenta.</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Columna de Información -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-soft rounded-4 overflow-hidden h-100">
                <div class="card-body p-4 text-center">
                    <div class="app-user-avatar lg mx-auto mb-3 shadow-soft" style="width: 120px; height: 120px; font-size: 3rem; display: flex; align-items: center; justify-content: center; background: var(--brand); color: white; border-radius: 50%;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-4">{{ $user->email }}</p>
                    
                    <div class="d-flex flex-column gap-3 text-start mt-4 pt-4 border-top">
                        <div class="profile-info-item">
                            <label class="text-sidebar-muted x-small fw-bold text-uppercase d-block mb-1">Rol en el Sistema</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <span class="fw-semibold text-dark">{{ $isOwner ? 'Administrador Central' : (optional($user->role)->nombre ?? 'Sin Rol') }}</span>
                            </div>
                        </div>

                        <div class="profile-info-item">
                            <label class="text-sidebar-muted x-small fw-bold text-uppercase d-block mb-1">Miembro desde</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-info bg-opacity-10 p-2 rounded-3 text-info">
                                    <i class="fa-solid fa-calendar-check"></i>
                                </div>
                                <span class="fw-semibold text-dark">{{ $user->created_at->format('d \d\e F, Y') }}</span>
                            </div>
                        </div>

                        @if(!$isOwner && isset($user->branch))
                        <div class="profile-info-item">
                            <label class="text-sidebar-muted x-small fw-bold text-uppercase d-block mb-1">Sucursal Activa</label>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success">
                                    <i class="fa-solid fa-building"></i>
                                </div>
                                <span class="fw-semibold text-dark">{{ $user->branch->name }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna de Seguridad / Configuración -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-soft rounded-4 h-100">
                <div class="card-header bg-white border-0 p-4 pb-0">
                    <h5 class="fw-bold mb-0">Seguridad de la Cuenta</h5>
                    <p class="text-muted small">Actualiza tu contraseña para mantener tu cuenta protegida.</p>
                </div>
                <div class="card-body p-4">
                    <form id="password-update-form">
                        @csrf
                        @method('PUT')
                        <div class="row g-4">
                            <div class="col-md-12" x-data="{ show: false }">
                                <label class="form-label fw-bold small">Contraseña Actual</label>
                                <div class="input-group shadow-sm rounded-3 overflow-hidden border">
                                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-lock text-muted"></i></span>
                                    <input :type="show ? 'text' : 'password'" name="current_password" class="form-control border-0 py-2" placeholder="Introduce tu clave actual" required>
                                    <button type="button" class="btn bg-white border-0 text-muted" @click="show = !show">
                                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6" x-data="{ show: false }">
                                <label class="form-label fw-bold small">Nueva Contraseña</label>
                                <div class="input-group shadow-sm rounded-3 overflow-hidden border">
                                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-key text-muted"></i></span>
                                    <input :type="show ? 'text' : 'password'" name="password" class="form-control border-0 py-2" placeholder="Mínimo 8 caracteres" required>
                                    <button type="button" class="btn bg-white border-0 text-muted" @click="show = !show">
                                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6" x-data="{ show: false }">
                                <label class="form-label fw-bold small">Confirmar Contraseña</label>
                                <div class="input-group shadow-sm rounded-3 overflow-hidden border">
                                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-check-circle text-muted"></i></span>
                                    <input :type="show ? 'text' : 'password'" name="password_confirmation" class="form-control border-0 py-2" placeholder="Repite la nueva clave" required>
                                    <button type="button" class="btn bg-white border-0 text-muted" @click="show = !show">
                                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-soft">
                                    <i class="fa-solid fa-save me-2"></i> Actualizar Contraseña
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('password-update-form');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            try {
                window.Notify.loading('Actualizando contraseña...');
                
                const response = await fetch('{{ route("password.update.ajax") }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    window.Notify.success(result.message);
                    form.reset();
                } else {
                    let errorMessage = result.message || 'Error al actualizar la contraseña';
                    if (result.errors) {
                        const firstError = Object.values(result.errors)[0][0];
                        errorMessage = firstError;
                    }
                    window.Notify.error(errorMessage);
                }
            } catch (error) {
                window.Notify.error('Ocurrió un error en la conexión');
                console.error(error);
            }
        });
    }
});
</script>
@endsection
