<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda en Mantenimiento | {{ optional($config)->company_name ?? tenant('business_name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary-color: {{ optional($config)->primary_color ?? '#3b82f6' }};
            --secondary-color: {{ optional($config)->secondary_color ?? '#1e3a8a' }};
        }
        .text-primary-custom { color: var(--primary-color) !important; }
        .bg-gradient-custom {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">

    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="bg-gradient-custom py-5 px-4 text-white">
                        <i class="fas fa-tools fa-4x mb-3 animate-pulse"></i>
                        <h2 class="fw-bold mb-0">Tienda en Mantenimiento</h2>
                    </div>
                    <div class="card-body p-5">
                        <div class="mb-4">
                            @if(optional($config)->logo_path)
                                <img src="{{ asset('storage/' . $config->logo_path) }}" alt="Logo" height="60" class="object-fit-contain mb-3">
                            @elseif(tenant('logo'))
                                <img src="{{ asset('storage/' . tenant('logo')) }}" alt="Logo" height="60" class="object-fit-contain mb-3">
                            @endif
                            <h4 class="fw-bold text-secondary">{{ optional($config)->company_name ?? tenant('business_name') }}</h4>
                        </div>
                        
                        <p class="lead text-muted mb-4">
                            Estamos realizando mejoras en nuestra tienda para brindarte una mejor experiencia.
                        </p>
                        
                        <div class="alert alert-light border border-light-subtle rounded-3 mb-4">
                            <p class="mb-0 small text-muted">
                                <i class="fas fa-info-circle me-1 text-primary-custom"></i>
                                Por favor, vuelve a visitarnos en unos momentos.
                            </p>
                        </div>

                        <div class="d-flex justify-content-center gap-3">
                            @if(optional($config)->facebook_url)
                                <a href="{{ $config->facebook_url }}" target="_blank" class="btn btn-outline-secondary rounded-circle"><i class="fab fa-facebook-f"></i></a>
                            @endif
                            @if(optional($config)->instagram_url)
                                <a href="{{ $config->instagram_url }}" target="_blank" class="btn btn-outline-secondary rounded-circle"><i class="fab fa-instagram"></i></a>
                            @endif
                            @if(optional($config)->whatsapp_number)
                                <a href="https://wa.me/{{ $config->whatsapp_number }}" target="_blank" class="btn btn-outline-success rounded-circle"><i class="fab fa-whatsapp"></i></a>
                            @endif
                        </div>
                        
                        @php
                            $maintEmail = optional($config)->contact_email ?? tenant('email');
                            $maintPhone = optional($config)->contact_phone ?? tenant('phone');
                            $maintAddress = optional($config)->contact_address ?? tenant('address');
                        @endphp

                        @if($maintEmail || $maintPhone || $maintAddress)
                            <div class="mt-4 pt-3 border-top">
                                <small class="text-muted d-block mb-1">Contáctanos:</small>
                                @if($maintEmail)
                                    <div class="small fw-bold">{{ $maintEmail }}</div>
                                @endif
                                @if($maintPhone)
                                    <div class="small fw-bold">{{ $maintPhone }}</div>
                                @endif
                                @if($maintAddress)
                                    <div class="small fw-bold text-muted mt-1">{{ $maintAddress }}</div>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-light py-3 border-0">
                        <a href="{{ route('login') }}" class="btn btn-link text-decoration-none text-muted small">Acceso Administrativo</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
