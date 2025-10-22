@extends('layouts.app')

@section('title', 'Gestión de Proveedores')

@push('styles')
    <link href="{{ asset('css/pages/suppliers.css') }}" rel="stylesheet">
    <link href="{{ asset('css/supplier-modals.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header responsive -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <h1 class="h3 mb-0"><i class="fas fa-truck me-2"></i> Gestión de Proveedores</h1>
                <button type="button" class="btn btn-add btn-responsive" onclick="openCreateProveedorModal()">
                    <i class="fas fa-plus me-1"></i> 
                    <span class="d-none d-sm-inline">Nuevo Proveedor</span>
                    <span class="d-sm-none">Nuevo</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Resumen y atajos de filtros -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-2 p-3 bg-light rounded border">
                <div class="text-muted">
                    🚚 <strong>{{ $suppliers->count() }}</strong> de <strong>{{ $totals['all'] ?? 0 }}</strong> proveedores
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#" class="btn btn-sm btn-outline-secondary" role="button"
                       onclick="document.getElementById('filtroEstado').value='';document.getElementById('filtroInsumos').value='';aplicarFiltros();return false;">
                        <i class="fas fa-list"></i> Todos
                        <span class="badge bg-secondary ms-1">{{ $totals['all'] ?? 0 }}</span>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-success" role="button"
                       onclick="document.getElementById('filtroEstado').value='Activo';document.getElementById('filtroInsumos').value='';aplicarFiltros();return false;">
                        <i class="fas fa-check-circle"></i> Activos
                        <span class="badge bg-success ms-1">{{ $totals['active'] ?? 0 }}</span>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-danger" role="button"
                       onclick="document.getElementById('filtroEstado').value='Inactivo';document.getElementById('filtroInsumos').value='';aplicarFiltros();return false;">
                        <i class="fas fa-times-circle"></i> Inactivos
                        <span class="badge bg-danger ms-1">{{ $totals['inactive'] ?? 0 }}</span>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-primary" role="button"
                       onclick="document.getElementById('filtroEstado').value='';document.getElementById('filtroInsumos').value='con-insumos';aplicarFiltros();return false;">
                        <i class="fas fa-boxes"></i> Con insumos
                        <span class="badge bg-primary ms-1">{{ $totals['with_supplies'] ?? 0 }}</span>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-dark" role="button"
                       onclick="document.getElementById('filtroEstado').value='';document.getElementById('filtroInsumos').value='sin-insumos';aplicarFiltros();return false;">
                        <i class="fas fa-box-open"></i> Sin insumos
                        <span class="badge bg-dark ms-1">{{ $totals['without_supplies'] ?? 0 }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel de Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros de Búsqueda</h6>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse" aria-expanded="false">
                            <i class="fas fa-chevron-down" id="filtrosIcon"></i>
                        </button>
                    </div>
                </div>
                <div class="collapse" id="filtrosCollapse">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6 col-lg-3">
                                <label for="filtroNombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="filtroNombre" placeholder="Buscar por nombre...">
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <label for="filtroEstado" class="form-label">Estado</label>
                                <select class="form-select" id="filtroEstado">
                                    <option value="">Todos los estados</option>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <label for="filtroInsumos" class="form-label">Insumos</label>
                                <select class="form-select" id="filtroInsumos">
                                    <option value="">Todos</option>
                                    <option value="con-insumos">Con insumos</option>
                                    <option value="sin-insumos">Sin insumos</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="aplicarFiltros()">
                                        <i class="fas fa-search me-1"></i>Aplicar Filtros
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="limpiarFiltros()">
                                        <i class="fas fa-times me-1"></i>Limpiar
                                    </button>
                                    <span class="text-muted small align-self-center ms-2" id="resultadosFiltro">
                                        Mostrando todos los proveedores
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($suppliers->count() > 0)
        <!-- Vista de tabla para pantallas grandes -->
        <div class="d-none d-lg-block">
            <div class="table-responsive">
                <table class="table proveedores-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Contacto</th>
                            <th>Total Compras</th>
                            <th>Insumos</th>
                            <th>Estado</th>
                            <th class="accion">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suppliers as $supplier)
                        <tr class="proveedor-row" 
                            data-nombre="{{ strtolower($supplier->name) }}" 
                            data-estado="{{ $supplier->status_in_spanish }}" 
                            data-contacto="{{ strtolower($supplier->phone . ' ' . $supplier->email) }}" 
                            data-supplies="{{ $supplier->supplies->count() }}">
                            <td>{{ $supplier->supplier_id }}</td>
                            <td>
                                <strong>{{ $supplier->name }}</strong>
                                <br>
                                <small class="text-muted">{{ Str::limit($supplier->address, 50) }}</small>
                            </td>
                            <td class="contacto-info">
                                <i class="fas fa-phone"></i> {{ $supplier->phone }}<br>
                                <i class="fas fa-envelope"></i> {{ Str::limit($supplier->email, 25) }}
                            </td>
                            <td>₡{{ number_format($supplier->total_purchases, 2) }}</td>
                            <td>
                                @if($supplier->supplies->count() > 0)
                                    <span class="badge bg-success">{{ $supplier->supplies->count() }} insumos</span>
                                @else
                                    <span class="text-muted">Sin insumos</span>
                                @endif
                            </td>
                            <td>
                                @if($supplier->status_in_spanish == 'Activo')
                                    <span class="estado-activo-badge">{{ $supplier->status_in_spanish }}</span>
                                @else
                                    <span class="estado-inactivo-badge">{{ $supplier->status_in_spanish }}</span>
                                @endif
                            </td>
                            <td class="baction">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info btn-sm" title="Ver" 
                                        onclick="openShowProveedorModal({{ $supplier->supplier_id }})"
                                        onmouseenter="preloadShowProveedorModal({{ $supplier->supplier_id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" title="Editar" 
                                        onclick="openEditProveedorModal({{ $supplier->supplier_id }})"
                                        onmouseenter="preloadEditProveedorModal({{ $supplier->supplier_id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('suppliers.destroy', $supplier->supplier_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar" 
                                            onclick="return confirm('¿Estás seguro de eliminar este proveedor?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Vista de cards para pantallas medianas y pequeñas -->
        <div class="d-lg-none">
            <div class="row g-3">
                @foreach($suppliers as $supplier)
                <div class="col-12 col-md-6 proveedor-card-item" 
                     data-nombre="{{ strtolower($supplier->name) }}" 
                     data-estado="{{ $supplier->status_in_spanish }}" 
                     data-contacto="{{ strtolower($supplier->phone . ' ' . $supplier->email) }}" 
                     data-supplies="{{ $supplier->supplies->count() }}">
                    <div class="card proveedor-card-responsive">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary me-2">#{{ $supplier->supplier_id }}</span>
                                <h6 class="mb-0 fw-bold">{{ $supplier->name }}</h6>
                            </div>
                            @if($supplier->status_in_spanish == 'Activo')
                                <span class="estado-activo-badge">{{ $supplier->status_in_spanish }}</span>
                            @else
                                <span class="estado-inactivo-badge">{{ $supplier->status_in_spanish }}</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ Str::limit($supplier->address, 60) }}
                                    </small>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-12 col-sm-6 mb-1">
                                    <small class="contacto-info">
                                        <i class="fas fa-phone me-1"></i> {{ $supplier->phone }}
                                    </small>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <small class="contacto-info">
                                        <i class="fas fa-envelope me-1"></i> {{ Str::limit($supplier->email, 20) }}
                                    </small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="stat-mini">
                                        <span class="text-muted small">Total Compras</span>
                                        <div class="fw-bold text-success">₡{{ number_format($supplier->total_purchases, 2) }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-mini">
                                        <span class="text-muted small">Insumos</span>
                                        <div class="fw-bold">
                                            @if($supplier->supplies->count() > 0)
                                                <span class="badge bg-success">{{ $supplier->supplies->count() }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" class="btn btn-info btn-sm flex-fill" 
                                    onclick="openShowProveedorModal({{ $supplier->supplier_id }})"
                                    onmouseenter="preloadShowProveedorModal({{ $supplier->supplier_id }})">
                                    <i class="fas fa-eye me-1"></i>
                                    <span class="d-none d-sm-inline">Ver</span>
                                </button>
                                <button type="button" class="btn btn-warning btn-sm flex-fill" 
                                    onclick="openEditProveedorModal({{ $supplier->supplier_id }})"
                                    onmouseenter="preloadEditProveedorModal({{ $supplier->supplier_id }})">
                                    <i class="fas fa-edit me-1"></i>
                                    <span class="d-none d-sm-inline">Editar</span>
                                </button>
                                <form action="{{ route('suppliers.destroy', $supplier->supplier_id) }}" method="POST" class="flex-fill">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm w-100" 
                                        onclick="return confirm('¿Estás seguro de eliminar este proveedor?')">
                                        <i class="fas fa-trash me-1"></i>
                                        <span class="d-none d-sm-inline">Eliminar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                    <h4>No hay proveedores registrados</h4>
                    <p class="text-muted">Comienza agregando tu primer proveedor.</p>
                    <button type="button" class="btn btn-primary" onclick="openCreateProveedorModal()">
                        <i class="fas fa-plus me-1"></i> Crear Primer Proveedor
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Modal para Ver Detalles de Proveedor -->
<div id="showProveedorModal" class="custom-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-info-circle"></i> Detalles del Proveedor</h3>
            <span class="close" onclick="closeProveedorModal('showProveedorModal')">&times;</span>
        </div>
        <div class="modal-body" id="showProveedorModalContent">
            <!-- El contenido se cargará aquí dinámicamente -->
        </div>
    </div>
</div>

<!-- Modal para Crear Proveedor -->
<div id="createProveedorModal" class="custom-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Crear Nuevo Proveedor</h3>
            <span class="close" onclick="closeProveedorModal('createProveedorModal')">&times;</span>
        </div>
        <div class="modal-body">
            <form id="createProveedorForm" action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="create_proveedor_nombre" class="form-label">Nombre del Proveedor *</label>
                    <input type="text" class="form-control" id="create_proveedor_nombre" name="nombre" required placeholder="Ej: Distribuidora Alimentos Frescos">
                </div>

                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="mb-3">
                            <label for="create_proveedor_telefono" class="form-label">Teléfono *</label>
                            <input type="text" class="form-control" id="create_proveedor_telefono" name="telefono" required placeholder="Ej: 3001234567">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="mb-3">
                            <label for="create_proveedor_correo" class="form-label">Correo Electrónico *</label>
                            <input type="email" class="form-control" id="create_proveedor_correo" name="correo" required placeholder="Ej: contacto@proveedor.com">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="create_proveedor_direccion" class="form-label">Dirección *</label>
                    <textarea class="form-control" id="create_proveedor_direccion" name="direccion" required placeholder="Ej: Calle 123 #45-67, Bogotá"></textarea>
                </div>

                <div class="section-divider"></div>

                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="mb-3">
                            <label for="create_proveedor_total_compras" class="form-label">Total de Compras *</label>
                            <div class="input-group">
                                <span class="input-group-text">₡</span>
                                <input type="number" step="0.01" class="form-control" id="create_proveedor_total_compras" name="total_compras" required value="0" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="mb-3">
                            <label for="create_proveedor_estado" class="form-label">Estado *</label>
                            <select class="form-select" id="create_proveedor_estado" name="estado" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="section-divider"></div>
                
                <div class="mb-3">
                    <label class="form-label">Insumos que Provee <span class="info-tooltip" data-tooltip="Seleccione los insumos que este proveedor puede suministrar">ℹ️</span></label>
                    
                    <div class="border p-3 rounded" id="createProveedorInsumosList" style="background-color: white; border-radius: 10px; max-height: 200px; overflow-y: auto;">
                        @foreach($supplies as $supply)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="insumos[]" value="{{ $supply->supply_id }}" id="create_proveedor_insumo{{ $supply->supply_id }}">
                            <label class="form-check-label" for="create_proveedor_insumo{{ $supply->supply_id }}">
                                <strong>{{ $supply->name }}</strong> - ₡{{ number_format($supply->price, 2) }}
                                <br><small class="text-muted">{{ $supply->unit_of_measure }} | Stock: {{ $supply->current_stock }}</small>
                            </label>
                        </div>
                        @endforeach
                        @if($supplies->count() == 0)
                        <div class="text-center p-3">
                            <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No hay insumos disponibles.</p>
                            <small>Puede crear insumos primero y luego asignarlos a este proveedor.</small>
                        </div>
                        @endif
                    </div>
                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-info-circle"></i> 
                        Puede seleccionar múltiples insumos que este proveedor puede suministrar
                    </small>
                </div>

                <div class="modal-actions d-flex flex-column flex-sm-row gap-2">
                    <button type="button" class="btn btn-secondary flex-sm-fill" onclick="closeProveedorModal('createProveedorModal')">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary flex-sm-fill">
                        <i class="fas fa-save me-1"></i> Guardar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Editar Proveedor -->
<div id="editProveedorModal" class="custom-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Editar Proveedor</h3>
            <span class="close" onclick="closeProveedorModal('editProveedorModal')">&times;</span>
        </div>
        <div class="modal-body" id="editProveedorModalContent">
            <!-- El contenido se cargará aquí dinámicamente -->
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/supplier-modals.js') }}"></script>
<script src="{{ asset('js/supplier-validations.js') }}"></script>
<script src="{{ asset('js/supplier-filters.js') }}"></script>
@endpush