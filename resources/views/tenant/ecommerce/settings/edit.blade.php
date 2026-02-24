@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Configuración de Tienda Online</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('tenant.shop.index') }}" target="_blank" class="btn btn-outline-primary rounded-pill px-4">
                <i class="fas fa-external-link-alt me-2"></i> Ver Tienda
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-soft rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('tenant.ecommerce-settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Sección 1: Información General y Contacto -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="mb-3 text-secondary border-bottom pb-2">Información de la Tienda</h5>
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">Nombre de la Tienda</label>
                                    <input type="text" class="form-control rounded-3" id="company_name" name="company_name" value="{{ old('company_name', $config->company_name ?? tenant('business_name')) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="about_us_text" class="form-label">Sobre Nosotros</label>
                                    <textarea class="form-control rounded-3" id="about_us_text" name="about_us_text" rows="4">{{ old('about_us_text', $config->about_us_text) }}</textarea>
                                    <small class="text-muted">Breve descripción que aparecerá en el pie de página o sección "Nosotros".</small>
                                </div>
                                <div class="mb-3">
                                    <label for="footer_info" class="form-label">Información de Pie de Página</label>
                                    <textarea class="form-control rounded-3" id="footer_info" name="footer_info" rows="2" placeholder="Ej: Atención las 24 horas a través de nuestra tienda online.">{{ old('footer_info', $config->footer_info) }}</textarea>
                                    <small class="text-muted">Texto corto para la sección de información o horario en el pie de página.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3 text-secondary border-bottom pb-2">Contacto y Redes Sociales</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_email" class="form-label">Email de Contacto</label>
                                        <input type="email" class="form-control rounded-3" id="contact_email" name="contact_email" value="{{ old('contact_email', $config->contact_email ?? tenant('email')) }}" placeholder="{{ tenant('email') }}">
                                        <small class="text-muted d-block mt-1">Si se deja vacío, se usará el email del negocio.</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_phone" class="form-label">Teléfono / Celular</label>
                                        <input type="text" class="form-control rounded-3" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $config->contact_phone ?? tenant('phone')) }}" placeholder="{{ tenant('phone') }}">
                                        <small class="text-muted d-block mt-1">Si se deja vacío, se usará el teléfono del negocio.</small>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="contact_address" class="form-label">Dirección del Negocio</label>
                                        <input type="text" class="form-control rounded-3" id="contact_address" name="contact_address" value="{{ old('contact_address', $config->contact_address ?? tenant('address')) }}" placeholder="{{ tenant('address') }}">
                                        <small class="text-muted d-block mt-1">Si se deja vacío, se usará la dirección registrada en el negocio.</small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="whatsapp_number" class="form-label"><i class="fab fa-whatsapp text-success me-1"></i> WhatsApp (Número)</label>
                                    <input type="text" class="form-control rounded-3" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', $config->whatsapp_number) }}" placeholder="Ej: 59170000000">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="facebook_url" class="form-label"><i class="fab fa-facebook text-primary me-1"></i> Facebook URL</label>
                                        <input type="url" class="form-control rounded-3" id="facebook_url" name="facebook_url" value="{{ old('facebook_url', $config->facebook_url) }}" placeholder="https://facebook.com/...">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="instagram_url" class="form-label"><i class="fab fa-instagram text-danger me-1"></i> Instagram URL</label>
                                        <input type="url" class="form-control rounded-3" id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $config->instagram_url) }}" placeholder="https://instagram.com/...">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tiktok_url" class="form-label"><i class="fab fa-tiktok text-dark me-1"></i> TikTok URL</label>
                                        <input type="url" class="form-control rounded-3" id="tiktok_url" name="tiktok_url" value="{{ old('tiktok_url', $config->tiktok_url) }}" placeholder="https://tiktok.com/...">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="twitter_url" class="form-label"><i class="fab fa-twitter text-info me-1"></i> Twitter/X URL</label>
                                        <input type="url" class="form-control rounded-3" id="twitter_url" name="twitter_url" value="{{ old('twitter_url', $config->twitter_url) }}" placeholder="https://twitter.com/...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Sección: Barra Superior (Top Bar) -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3 text-secondary border-bottom pb-2">Barra Superior de Anuncios</h5>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="top_bar_active" name="top_bar_active" value="1" {{ $config->top_bar_active ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="top_bar_active">Activar Barra Superior</label>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="top_bar_text" class="form-label">Texto del Anuncio</label>
                                        <input type="text" class="form-control rounded-3" id="top_bar_text" name="top_bar_text" value="{{ old('top_bar_text', $config->top_bar_text) }}" placeholder="Ej: ¡Oferta Especial! 20% de descuento">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="top_bar_link" class="form-label">Enlace (Opcional)</label>
                                        <input type="text" class="form-control rounded-3" id="top_bar_link" name="top_bar_link" value="{{ old('top_bar_link', $config->top_bar_link) }}" placeholder="Ej: /shop/category/ofertas">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="top_bar_bg_color" class="form-label">Color de Fondo</label>
                                        <input type="color" class="form-control form-control-color w-100 rounded-3" id="top_bar_bg_color" name="top_bar_bg_color" value="{{ old('top_bar_bg_color', $config->top_bar_bg_color ?? '#000000') }}" title="Elige el color de fondo">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="top_bar_text_color" class="form-label">Color de Texto</label>
                                        <input type="color" class="form-control form-control-color w-100 rounded-3" id="top_bar_text_color" name="top_bar_text_color" value="{{ old('top_bar_text_color', $config->top_bar_text_color ?? '#ffffff') }}" title="Elige el color de texto">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Sección: Configuración de la Portada -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3 text-secondary border-bottom pb-2">Configuración de la Portada</h5>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="hero_title" class="form-label">Título Principal (Hero)</label>
                                        <input type="text" class="form-control rounded-3" id="hero_title" name="hero_title" value="{{ old('hero_title', $config->hero_title) }}" placeholder="Ej: Bienvenido a mi tienda">
                                        <small class="text-muted">Aparece grande sobre el banner.</small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="hero_subtitle" class="form-label">Subtítulo (Hero)</label>
                                        <input type="text" class="form-control rounded-3" id="hero_subtitle" name="hero_subtitle" value="{{ old('hero_subtitle', $config->hero_subtitle) }}" placeholder="Ej: Los mejores productos...">
                                        <small class="text-muted">Aparece debajo del título.</small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="products_section_title" class="form-label">Título Sección Productos</label>
                                        <input type="text" class="form-control rounded-3" id="products_section_title" name="products_section_title" value="{{ old('products_section_title', $config->products_section_title) }}" placeholder="Ej: Nuestros Productos">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Sección: Sección Destacada (Split) -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3 text-secondary border-bottom pb-2">Sección Destacada (Split)</h5>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="show_featured_section" name="show_featured_section" value="1" {{ $config->show_featured_section ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="show_featured_section">Mostrar Sección Destacada</label>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="featured_title" class="form-label">Título</label>
                                        <input type="text" class="form-control rounded-3" id="featured_title" name="featured_title" value="{{ old('featured_title', $config->featured_title) }}" placeholder="Ej: Calidad Premium">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="featured_description" class="form-label">Descripción</label>
                                        <textarea class="form-control rounded-3" id="featured_description" name="featured_description" rows="1" placeholder="Ej: Descubre nuestra selección...">{{ old('featured_description', $config->featured_description) }}</textarea>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="featured_btn_text" class="form-label">Texto del Botón</label>
                                        <input type="text" class="form-control rounded-3" id="featured_btn_text" name="featured_btn_text" value="{{ old('featured_btn_text', $config->featured_btn_text) }}" placeholder="Ej: Comprar Ahora">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="featured_btn_link" class="form-label">Enlace del Botón</label>
                                        <input type="text" class="form-control rounded-3" id="featured_btn_link" name="featured_btn_link" value="{{ old('featured_btn_link', $config->featured_btn_link) }}" placeholder="Ej: /shop/products">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Sección: Testimonios -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                    <h5 class="text-secondary mb-0">Sección de Testimonios</h5>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" id="show_testimonials" name="show_testimonials" value="1" {{ $config->show_testimonials ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="show_testimonials">Mostrar en Tienda</label>
                                    </div>
                                </div>

                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <div class="row align-items-center mb-4">
                                            <div class="col-md-8">
                                                <label for="testimonials_title" class="form-label small text-muted text-uppercase fw-bold mb-1">Título Público de la Sección</label>
                                                <input type="text" class="form-control" id="testimonials_title" name="testimonials_title" value="{{ old('testimonials_title', $config->testimonials_title) }}" placeholder="Ej: Lo que dicen nuestros clientes">
                                            </div>
                                            <div class="col-md-4 text-end mt-3 mt-md-0">
                                                <button type="button" class="btn btn-primary rounded-pill px-3 w-100" data-bs-toggle="modal" data-bs-target="#createTestimonialModal">
                                                    <i class="fas fa-plus me-1"></i> Agregar Testimonio
                                                </button>
                                            </div>
                                        </div>

                                        <div class="card border-0 shadow-sm overflow-hidden">
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="ps-3">Cliente</th>
                                                            <th>Rol</th>
                                                            <th>Calificación</th>
                                                            <th class="text-end pe-3">Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($testimonials as $testimonial)
                                                            <tr>
                                                                <td class="align-middle ps-3">
                                                                    <div class="d-flex align-items-center">
                                                                        @if($testimonial->image_path)
                                                                            <img src="{{ asset('storage/' . $testimonial->image_path) }}" class="rounded-circle me-2 object-fit-cover" width="32" height="32" alt="">
                                                                        @else
                                                                            <div class="bg-primary bg-opacity-10 rounded-circle text-primary d-flex align-items-center justify-content-center fw-bold me-2" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                                                                {{ substr($testimonial->name, 0, 1) }}
                                                                            </div>
                                                                        @endif
                                                                        <div>
                                                                            <div class="fw-bold small">{{ $testimonial->name }}</div>
                                                                            <div class="small text-muted d-md-none">{{ $testimonial->role }}</div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="align-middle small text-muted">{{ $testimonial->role ?? '-' }}</td>
                                                                <td class="align-middle">
                                                                    <div class="text-warning small">
                                                                        @for($i = 1; $i <= 5; $i++)
                                                                            @if($i <= $testimonial->rating)
                                                                                <i class="fas fa-star"></i>
                                                                            @else
                                                                                <i class="far fa-star text-muted opacity-25"></i>
                                                                            @endif
                                                                        @endfor
                                                                    </div>
                                                                </td>
                                                                <td class="text-end align-middle pe-3">
                                                                    <div class="btn-group">
                                                                        <button type="button" class="btn btn-sm btn-light text-primary" data-bs-toggle="modal" data-bs-target="#editTestimonialModal{{ $testimonial->id }}" title="Editar">
                                                                            <i class="fas fa-edit"></i>
                                                                        </button>
                                                                        <button type="button" class="btn btn-sm btn-light text-danger" onclick="if(confirm('¿Eliminar testimonio?')) document.getElementById('delete-testimonial-{{ $testimonial->id }}').submit()" title="Eliminar">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                    <form id="delete-testimonial-{{ $testimonial->id }}" action="{{ route('tenant.ecommerce.testimonials.destroy', $testimonial->id) }}" method="POST" class="d-none">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>
                                                                </td>
                                                            </tr>

                                                            <!-- Modal Editar Testimonio -->
                                                            <div class="modal fade" id="editTestimonialModal{{ $testimonial->id }}" tabindex="-1" aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <form action="{{ route('tenant.ecommerce.testimonials.update', $testimonial->id) }}" method="POST" enctype="multipart/form-data">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title">Editar Testimonio</h5>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div class="mb-3">
                                                                                    <label class="form-label">Nombre del Cliente</label>
                                                                                    <input type="text" class="form-control" name="name" value="{{ $testimonial->name }}" required>
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <label class="form-label">Rol / Cargo (Opcional)</label>
                                                                                    <input type="text" class="form-control" name="role" value="{{ $testimonial->role }}">
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <label class="form-label">Testimonio</label>
                                                                                    <textarea class="form-control" name="content" rows="3" required>{{ $testimonial->content }}</textarea>
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <label class="form-label">Calificación (1-5)</label>
                                                                                    <div class="d-flex gap-2">
                                                                                        <input type="number" class="form-control" name="rating" min="1" max="5" value="{{ $testimonial->rating }}" required>
                                                                                        <span class="form-text">Estrellas</span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <label class="form-label">Foto (Opcional)</label>
                                                                                    <input type="file" class="form-control" name="image" accept="image/*">
                                                                                    @if($testimonial->image_path)
                                                                                        <div class="mt-2">
                                                                                            <img src="{{ asset('storage/' . $testimonial->image_path) }}" class="rounded" width="50" alt="Preview">
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                                <div class="form-check form-switch">
                                                                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $testimonial->is_active ? 'checked' : '' }}>
                                                                                    <label class="form-check-label">Visible al público</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="text-center py-4 text-muted">
                                                                    <div class="mb-2"><i class="far fa-comment-dots fa-2x opacity-50"></i></div>
                                                                    <small>No has agregado testimonios aún.</small>
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Sección: Enlaces Legales -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3 text-secondary border-bottom pb-2">Enlaces Legales y Políticas</h5>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="shipping_policy_link" class="form-label">Política de Envíos (URL)</label>
                                        <input type="text" class="form-control rounded-3" id="shipping_policy_link" name="shipping_policy_link" value="{{ old('shipping_policy_link', $config->shipping_policy_link) }}" placeholder="Ej: /shipping-policy">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="returns_policy_link" class="form-label">Política de Devoluciones (URL)</label>
                                        <input type="text" class="form-control rounded-3" id="returns_policy_link" name="returns_policy_link" value="{{ old('returns_policy_link', $config->returns_policy_link) }}" placeholder="Ej: /returns">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="terms_conditions_link" class="form-label">Términos y Condiciones (URL)</label>
                                        <input type="text" class="form-control rounded-3" id="terms_conditions_link" name="terms_conditions_link" value="{{ old('terms_conditions_link', $config->terms_conditions_link) }}" placeholder="Ej: /terms">
                                    </div>
                                </div>
                                <small class="text-muted">Si dejas estos campos vacíos, los enlaces en el pie de página se ocultarán.</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Sección: Secciones de la Tienda -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3 text-secondary border-bottom pb-2">Secciones Visibles</h5>
                                <div class="d-flex gap-4 flex-wrap">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_search_bar" name="show_search_bar" value="1" {{ $config->show_search_bar ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_search_bar">Mostrar Barra de Búsqueda</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_categories_section" name="show_categories_section" value="1" {{ $config->show_categories_section ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_categories_section">Mostrar Sección de Categorías</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_benefits_section" name="show_benefits_section" value="1" {{ $config->show_benefits_section ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_benefits_section">Mostrar Sección de Beneficios</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Configuración de Beneficios (Solo si está activo) -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3 text-secondary border-bottom pb-2">Configuración de Beneficios</h5>
                                <div class="row g-3">
                                    <!-- Beneficio 1 -->
                                    <div class="col-md-4">
                                        <div class="card bg-light border-0">
                                            <div class="card-body">
                                                <h6 class="card-title text-primary fw-bold">Beneficio 1</h6>
                                                <div class="mb-2">
                                                    <label class="form-label small">Icono (FontAwesome)</label>
                                                    <input type="text" class="form-control form-control-sm" name="benefit_1_icon" value="{{ old('benefit_1_icon', $config->benefit_1_icon) }}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small">Título</label>
                                                    <input type="text" class="form-control form-control-sm" name="benefit_1_title" value="{{ old('benefit_1_title', $config->benefit_1_title) }}">
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small">Descripción</label>
                                                    <input type="text" class="form-control form-control-sm" name="benefit_1_desc" value="{{ old('benefit_1_desc', $config->benefit_1_desc) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Beneficio 2 -->
                                    <div class="col-md-4">
                                        <div class="card bg-light border-0">
                                            <div class="card-body">
                                                <h6 class="card-title text-primary fw-bold">Beneficio 2</h6>
                                                <div class="mb-2">
                                                    <label class="form-label small">Icono (FontAwesome)</label>
                                                    <input type="text" class="form-control form-control-sm" name="benefit_2_icon" value="{{ old('benefit_2_icon', $config->benefit_2_icon) }}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small">Título</label>
                                                    <input type="text" class="form-control form-control-sm" name="benefit_2_title" value="{{ old('benefit_2_title', $config->benefit_2_title) }}">
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small">Descripción</label>
                                                    <input type="text" class="form-control form-control-sm" name="benefit_2_desc" value="{{ old('benefit_2_desc', $config->benefit_2_desc) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Beneficio 3 -->
                                    <div class="col-md-4">
                                        <div class="card bg-light border-0">
                                            <div class="card-body">
                                                <h6 class="card-title text-primary fw-bold">Beneficio 3</h6>
                                                <div class="mb-2">
                                                    <label class="form-label small">Icono (FontAwesome)</label>
                                                    <input type="text" class="form-control form-control-sm" name="benefit_3_icon" value="{{ old('benefit_3_icon', $config->benefit_3_icon) }}">
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small">Título</label>
                                                    <input type="text" class="form-control form-control-sm" name="benefit_3_title" value="{{ old('benefit_3_title', $config->benefit_3_title) }}">
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small">Descripción</label>
                                                    <input type="text" class="form-control form-control-sm" name="benefit_3_desc" value="{{ old('benefit_3_desc', $config->benefit_3_desc) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <hr class="my-4">

                        <!-- Sección 2: Apariencia y Configuración -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="mb-3 text-secondary border-bottom pb-2">Logotipo y Banner</h5>
                                <div class="mb-3">
                                    <label for="logo" class="form-label">Logo del Sistema</label>
                                    <input type="file" class="form-control rounded-3" id="logo" name="logo" accept="image/*">
                                    <small class="text-muted">Este logo se mostrará en tickets, menú y tienda online.</small>
                                    @if($config->logo_path)
                                        <div class="mt-3 p-3 bg-light rounded-3 text-center border">
                                            <small class="text-muted d-block mb-2">Logo Actual</small>
                                            <img src="{{ asset('storage/' . $config->logo_path) }}" alt="Logo actual" style="max-height: 60px;" class="img-fluid">
                                        </div>
                                    @elseif(tenant('logo'))
                                        <div class="mt-3 p-3 bg-light rounded-3 text-center border">
                                            <small class="text-muted d-block mb-2">Logo Actual</small>
                                            <img src="{{ asset('storage/' . tenant('logo')) }}" alt="Logo actual" style="max-height: 60px;" class="img-fluid">
                                        </div>
                                    @else
                                        <div class="mt-3 p-3 bg-light rounded-3 text-center border">
                                            <i class="fas fa-store fa-2x text-muted mb-2"></i>
                                            <small class="d-block text-muted">Sin logo (se usa icono por defecto)</small>
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="banner" class="form-label">Banner Principal</label>
                                    <input type="file" class="form-control rounded-3" id="banner" name="banner" accept="image/*">
                                    @if($config->banner_path)
                                        <div class="mt-3 p-3 bg-light rounded-3 text-center border">
                                            <small class="text-muted d-block mb-2">Banner Actual</small>
                                            <img src="{{ asset('storage/' . $config->banner_path) }}" alt="Banner actual" style="max-height: 100px; width: auto;" class="img-fluid rounded">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="mb-3 text-secondary border-bottom pb-2">Personalización</h5>
                                <div class="mb-3">
                                    <label for="primary_color" class="form-label">Color Primario</label>
                                    <div class="d-flex align-items-center">
                                        <input type="color" class="form-control form-control-color w-100 rounded-3" id="primary_color" name="primary_color" value="{{ old('primary_color', $config->primary_color) }}" title="Elige tu color">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="secondary_color" class="form-label">Color Secundario</label>
                                    <div class="d-flex align-items-center">
                                        <input type="color" class="form-control form-control-color w-100 rounded-3" id="secondary_color" name="secondary_color" value="{{ old('secondary_color', $config->secondary_color) }}" title="Elige tu color">
                                    </div>
                                </div>

                                <h6 class="text-secondary mt-4 mb-3 small fw-bold text-uppercase tracking-wide">Barra Superior</h6>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label for="top_bar_bg_color" class="form-label small">Fondo</label>
                                        <input type="color" class="form-control form-control-color w-100 rounded-3" id="top_bar_bg_color" name="top_bar_bg_color" value="{{ old('top_bar_bg_color', $config->top_bar_bg_color) }}" title="Color de fondo">
                                    </div>
                                    <div class="col-6">
                                        <label for="top_bar_text_color" class="form-label small">Texto</label>
                                        <input type="color" class="form-control form-control-color w-100 rounded-3" id="top_bar_text_color" name="top_bar_text_color" value="{{ old('top_bar_text_color', $config->top_bar_text_color) }}" title="Color de texto">
                                    </div>
                                </div>
                                
                                <h5 class="mb-3 text-secondary border-bottom pb-2 mt-4">Estado de la Tienda</h5>
                                <div class="form-check form-switch p-3 bg-light rounded-3 d-flex align-items-center">
                                    <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="is_active" name="is_active" {{ $config->is_active ? 'checked' : '' }} value="1" style="width: 2.5em; height: 1.25em;">
                                    <label class="form-check-label fw-bold mb-0" for="is_active">Tienda Activa (Visible al público)</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mb-5">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
                                <i class="fas fa-save me-2"></i> Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Testimonio -->
<div class="modal fade" id="createTestimonialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tenant.ecommerce.testimonials.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Testimonio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Cliente</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol / Cargo (Opcional)</label>
                        <input type="text" class="form-control" name="role" placeholder="Ej: Cliente Frecuente">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Testimonio</label>
                        <textarea class="form-control" name="content" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Calificación (1-5)</label>
                        <input type="number" class="form-control" name="rating" min="1" max="5" value="5" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto (Opcional)</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <label class="form-check-label">Visible</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Testimonio</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Preview de imagen de logo
    // ... (Scripts adicionales si se necesitan)
</script>
@endsection
