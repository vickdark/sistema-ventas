<div class="text-center mb-4">
    @php
        $isTenant = function_exists('tenant') && tenant();
        $logo = $isTenant ? tenant('logo') : null;
        $businessName = $isTenant ? (tenant('business_name') ?? ucfirst(tenant('id'))) : config('app.name', 'Laravel');
    @endphp

    <div class="app-brand-identity mb-4">
        @if($logo)
            <img src="{{ asset('storage/' . $logo) }}" alt="Logo" class="img-fluid mb-3" style="max-height: 100px; width: auto; object-fit: contain;">
        @else
            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                <i class="fa-solid fa-rocket fa-2x text-primary"></i>
            </div>
        @endif
        <h1 class="h3 fw-bold text-gray-900 mb-1">{{ $businessName }}</h1>
        
        @if($isTenant)
            <div class="d-flex flex-column gap-1 mt-2">
                @if(tenant('tax_id'))
                    <span class="text-xs text-muted fw-semibold text-uppercase tracking-wider">
                        NIT/RUC: {{ tenant('tax_id') }}
                    </span>
                @endif
                
                @if(tenant('invoice_footer'))
                    <div class="mb-3 px-4">
                        <p class="text-muted fst-italic mb-0" style="font-size: 0.85rem;">
                            "{{ tenant('invoice_footer') }}"
                        </p>
                    </div>
                @endif

                <div class="d-flex justify-content-center flex-wrap gap-3 mt-1 small">
                    @if(tenant('address'))
                        <span class="text-muted"><i class="fas fa-map-marker-alt me-1 text-primary opacity-75"></i> {{ tenant('address') }}</span>
                    @endif
                    
                    @if(tenant('phone'))
                        <span class="text-muted"><i class="fas fa-phone me-1 text-primary opacity-75"></i> {{ tenant('phone') }}</span>
                    @endif
                    
                    @if(tenant('email'))
                        <span class="text-muted"><i class="fas fa-envelope me-1 text-primary opacity-75"></i> {{ tenant('email') }}</span>
                    @endif
                </div>

                @if(tenant('website'))
                    <div class="mt-2">
                        <a href="{{ str_starts_with(tenant('website'), 'http') ? tenant('website') : 'https://' . tenant('website') }}" 
                           target="_blank" class="text-decoration-none small text-primary fw-medium">
                            <i class="fas fa-globe me-1"></i> {{ str_replace(['https://', 'http://'], '', tenant('website')) }}
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
    
    <div class="border-top pt-4">
        <h2 class="h5 fw-semibold mb-1">{{ $title ?? 'Acceso al Sistema' }}</h2>
        <p class="text-secondary small mb-0">{{ $subtitle ?? 'Por favor, complete los datos.' }}</p>
    </div>
</div>
