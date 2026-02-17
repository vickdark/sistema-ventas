@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Configuración de Página para PageLoader.js --}}
    <div id="import-index-page"></div>

    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-import text-primary me-2"></i>
                Importación Masiva (ETL)
            </h1>
            <p class="text-muted small mb-0">Carga masiva de datos mediante archivos CSV o Excel</p>
        </div>
    </div>

    <!-- Compras Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-soft rounded-4 hover-lift">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            <div class="icon-circle bg-danger bg-opacity-10 mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fas fa-cart-shopping fa-2x text-danger"></i>
                            </div>
                        </div>
                        <div class="col-md-7 mb-3 mb-md-0">
                            <h5 class="card-title mb-2 fw-bold text-danger">Importación Masiva de Compras</h5>
                            <p class="text-muted mb-3">Registra múltiples compras simultáneamente actualizando el stock automático.</p>
                            
                            <div class="alert alert-light border-danger border-start border-4 small mb-0">
                                <h6 class="text-danger fw-bold"><i class="fas fa-exclamation-circle me-1"></i> Requisitos Obligatorios:</h6>
                                <ul class="mb-0 ps-3">
                                    <li>El <strong>Código del Producto</strong> debe existir previamente en el sistema.</li>
                                    <li>El <strong>Nombre de la Empresa del Proveedor</strong> debe coincidir exactamente.</li>
                                    <li>La cantidad no puede exceder el <strong>Stock Máximo</strong> configurado para el producto.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <button type="button" class="btn btn-danger rounded-pill w-100 mb-2 py-2" onclick="openImportModal('purchases')">
                                <i class="fas fa-upload me-1"></i> Importar Compras
                            </button>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary rounded-pill w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                    <i class="fas fa-download me-1"></i> Plantilla
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                    <li><a class="dropdown-item small" href="{{ route('import.template', ['module' => 'purchases', 'format' => 'csv']) }}"><i class="fas fa-file-csv me-2 text-primary"></i>Descargar CSV</a></li>
                                    <li><a class="dropdown-item small" href="{{ route('import.template', ['module' => 'purchases', 'format' => 'excel']) }}"><i class="fas fa-file-excel me-2 text-success"></i>Descargar Excel</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Otros Módulos -->
    <div class="row">
        <!-- Categorías -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-0 shadow-soft rounded-4 h-100 hover-lift">
                <div class="card-body p-4 text-center">
                    <div class="mb-3">
                        <div class="icon-circle bg-primary bg-opacity-10 mx-auto" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <i class="fas fa-tags fa-2x text-primary"></i>
                        </div>
                    </div>
                    <h5 class="card-title mb-1 fw-bold text-primary">Categorías</h5>
                    <p class="text-muted small mb-3">Catálogo de categorías</p>
                    
                    <div class="alert alert-light border-primary border-start border-3 small mb-3 text-start py-2 px-2" style="font-size: 0.8rem;">
                        <ul class="mb-0 ps-3">
                            <li><strong>Nombre</strong> único.</li>
                            <li>Omitir duplicados.</li>
                        </ul>
                    </div>

                    <button type="button" class="btn btn-outline-primary rounded-pill w-100 mb-2" onclick="openImportModal('categories')">
                        <i class="fas fa-upload me-1"></i> Importar
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-link text-decoration-none dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                            <i class="fas fa-download me-1"></i> Plantilla
                        </button>
                        <ul class="dropdown-menu shadow border-0 rounded-3">
                            <li><a class="dropdown-item small" href="{{ route('import.template', ['module' => 'categories', 'format' => 'csv']) }}"><i class="fas fa-file-csv me-2 text-primary"></i>CSV</a></li>
                            <li><a class="dropdown-item small" href="{{ route('import.template', ['module' => 'categories', 'format' => 'excel']) }}"><i class="fas fa-file-excel me-2 text-success"></i>Excel</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clientes -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-0 shadow-soft rounded-4 h-100 hover-lift">
                <div class="card-body p-4 text-center">
                    <div class="mb-3">
                        <div class="icon-circle bg-success bg-opacity-10 mx-auto" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <i class="fas fa-users fa-2x text-success"></i>
                        </div>
                    </div>
                    <h5 class="card-title mb-1 fw-bold text-success">Clientes</h5>
                    <p class="text-muted small mb-3">Base de clientes</p>

                    <div class="alert alert-light border-success border-start border-3 small mb-3 text-start py-2 px-2" style="font-size: 0.8rem;">
                        <ul class="mb-0 ps-3">
                            <li><strong>NIT/CI</strong> único.</li>
                            <li>Email válido.</li>
                        </ul>
                    </div>

                    <button type="button" class="btn btn-outline-success rounded-pill w-100 mb-2" onclick="openImportModal('clients')">
                        <i class="fas fa-upload me-1"></i> Importar
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-link text-decoration-none dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                            <i class="fas fa-download me-1"></i> Plantilla
                        </button>
                        <ul class="dropdown-menu shadow border-0 rounded-3">
                            <li><a class="dropdown-item small" href="{{ route('import.template', ['module' => 'clients', 'format' => 'csv']) }}"><i class="fas fa-file-csv me-2 text-primary"></i>CSV</a></li>
                            <li><a class="dropdown-item small" href="{{ route('import.template', ['module' => 'clients', 'format' => 'excel']) }}"><i class="fas fa-file-excel me-2 text-success"></i>Excel</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Proveedores -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-0 shadow-soft rounded-4 h-100 hover-lift">
                <div class="card-body p-4 text-center">
                    <div class="mb-3">
                        <div class="icon-circle bg-warning bg-opacity-10 mx-auto" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <i class="fas fa-truck fa-2x text-warning"></i>
                        </div>
                    </div>
                    <h5 class="card-title mb-1 fw-bold text-warning">Proveedores</h5>
                    <p class="text-muted small mb-3">Registro proveedores</p>

                    <div class="alert alert-light border-warning border-start border-3 small mb-3 text-start py-2 px-2" style="font-size: 0.8rem;">
                        <ul class="mb-0 ps-3">
                            <li><strong>Teléfono</strong> único.</li>
                            <li>Empresa requerida.</li>
                        </ul>
                    </div>

                    <button type="button" class="btn btn-outline-warning rounded-pill w-100 mb-2" onclick="openImportModal('suppliers')">
                        <i class="fas fa-upload me-1"></i> Importar
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-link text-decoration-none dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                            <i class="fas fa-download me-1"></i> Plantilla
                        </button>
                        <ul class="dropdown-menu shadow border-0 rounded-3">
                            <li><a class="dropdown-item small" href="{{ route('import.template', ['module' => 'suppliers', 'format' => 'csv']) }}"><i class="fas fa-file-csv me-2 text-primary"></i>CSV</a></li>
                            <li><a class="dropdown-item small" href="{{ route('import.template', ['module' => 'suppliers', 'format' => 'excel']) }}"><i class="fas fa-file-excel me-2 text-success"></i>Excel</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card border-0 shadow-soft rounded-4 h-100 hover-lift">
                <div class="card-body p-4 text-center">
                    <div class="mb-3">
                        <div class="icon-circle bg-info bg-opacity-10 mx-auto" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <i class="fas fa-box fa-2x text-info"></i>
                        </div>
                    </div>
                    <h5 class="card-title mb-1 fw-bold text-info">Productos</h5>
                    <p class="text-muted small mb-3">Catálogo productos</p>

                    <div class="alert alert-light border-info border-start border-3 small mb-3 text-start py-2 px-2" style="font-size: 0.8rem;">
                        <ul class="mb-0 ps-3">
                            <li><strong>Categoría</strong> (nombre).</li>
                            <li><strong>Proveedor</strong> (nombre/empresa).</li>
                            <li>Se crean categorías si no existen.</li>
                        </ul>
                    </div>

                    <button type="button" class="btn btn-outline-info rounded-pill w-100 mb-2" onclick="openImportModal('products')">
                        <i class="fas fa-upload me-1"></i> Importar
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-link text-decoration-none dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                            <i class="fas fa-download me-1"></i> Plantilla
                        </button>
                        <ul class="dropdown-menu shadow border-0 rounded-3">
                            <li><a class="dropdown-item small" href="{{ route('import.template', ['module' => 'products', 'format' => 'csv']) }}"><i class="fas fa-file-csv me-2 text-primary"></i>CSV</a></li>
                            <li><a class="dropdown-item small" href="{{ route('import.template', ['module' => 'products', 'format' => 'excel']) }}"><i class="fas fa-file-excel me-2 text-success"></i>Excel</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Instrucciones -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-soft rounded-4">
                <div class="card-body p-4">
                    <h5 class="mb-3"><i class="fas fa-info-circle text-primary me-2"></i>Instrucciones de Uso</h5>
                    <ol class="mb-0">
                        <li class="mb-2"><strong>Descarga la plantilla</strong> del módulo que deseas importar</li>
                        <li class="mb-2"><strong>Completa el archivo</strong> con tus datos (Excel o CSV)</li>
                        <li class="mb-2"><strong>Sube el archivo</strong> usando el botón "Importar"</li>
                        <li class="mb-2"><strong>Revisa el resumen</strong> de registros creados y errores</li>
                    </ol>
                    <div class="alert alert-warning small mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Importante:</strong> Los registros duplicados serán omitidos automáticamente. Asegúrate de que los datos estén correctamente formateados.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Importación -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title">
                    <i class="fas fa-upload text-primary me-2"></i>
                    Importar <span id="modalModuleName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="importForm" enctype="multipart/form-data">
                    @csrf
                    <div id="formFields">
                        <input type="hidden" id="importModule" name="module">
                        
                        <div class="mb-3">
                            <label for="importFile" class="form-label">Archivo (CSV o Excel)</label>
                            <input type="file" class="form-control rounded-3" id="importFile" name="file" accept=".csv,.xlsx,.xls" required>
                            <small class="text-muted">Formatos aceptados: CSV, XLSX, XLS</small>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="skipDuplicates" name="skip_duplicates" checked>
                            <label class="form-check-label" for="skipDuplicates">
                                Omitir registros duplicados
                            </label>
                        </div>
                    </div>

                    <div id="importProgress" class="d-none">
                        <div class="progress mb-2" style="height: 25px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%">0%</div>
                        </div>
                        <p class="text-center small text-muted mb-0">Procesando archivo...</p>
                    </div>

                    <div id="importResult" class="d-none"></div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0" id="modalFooter">
                <button type="button" id="btnCancel" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnSubmit" class="btn btn-primary rounded-pill" onclick="submitImport()">
                    <i class="fas fa-upload me-1"></i> Importar Datos
                </button>
            </div>
        </div>
    </div>
</div>



@endsection
