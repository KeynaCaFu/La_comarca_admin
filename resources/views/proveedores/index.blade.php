@extends('layouts.app')

@section('title', 'Gestión de Proveedores')

@push('styles')
    <link href="{{ asset('css/pages/proveedores.css') }}" rel="stylesheet">
    <link href="{{ asset('css/proveedor-modals.css') }}" rel="stylesheet">
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

    @if($proveedores->count() > 0)
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
                        @foreach($proveedores as $proveedor)
                        <tr class="proveedor-row" 
                            data-nombre="{{ strtolower($proveedor->nombre) }}" 
                            data-estado="{{ $proveedor->estado }}" 
                            data-contacto="{{ strtolower($proveedor->telefono . ' ' . $proveedor->correo) }}" 
                            data-insumos="{{ $proveedor->insumos->count() }}">
                            <td>{{ $proveedor->proveedor_id }}</td>
                            <td>
                                <strong>{{ $proveedor->nombre }}</strong>
                                <br>
                                <small class="text-muted">{{ Str::limit($proveedor->direccion, 50) }}</small>
                            </td>
                            <td class="contacto-info">
                                <i class="fas fa-phone"></i> {{ $proveedor->telefono }}<br>
                                <i class="fas fa-envelope"></i> {{ Str::limit($proveedor->correo, 25) }}
                            </td>
                            <td>₡{{ number_format($proveedor->total_compras, 2) }}</td>
                            <td>
                                @if($proveedor->insumos->count() > 0)
                                    <span class="badge bg-success">{{ $proveedor->insumos->count() }} insumos</span>
                                @else
                                    <span class="text-muted">Sin insumos</span>
                                @endif
                            </td>
                            <td>
                                @if($proveedor->estado == 'Activo')
                                    <span class="estado-activo-badge">{{ $proveedor->estado }}</span>
                                @else
                                    <span class="estado-inactivo-badge">{{ $proveedor->estado }}</span>
                                @endif
                            </td>
                            <td class="baction">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info btn-sm" title="Ver" onclick="openShowProveedorModal({{ $proveedor->proveedor_id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" title="Editar" onclick="openEditProveedorModal({{ $proveedor->proveedor_id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('proveedores.destroy', $proveedor->proveedor_id) }}" method="POST" class="d-inline">
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
                @foreach($proveedores as $proveedor)
                <div class="col-12 col-md-6 proveedor-card-item" 
                     data-nombre="{{ strtolower($proveedor->nombre) }}" 
                     data-estado="{{ $proveedor->estado }}" 
                     data-contacto="{{ strtolower($proveedor->telefono . ' ' . $proveedor->correo) }}" 
                     data-insumos="{{ $proveedor->insumos->count() }}">
                    <div class="card proveedor-card-responsive">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary me-2">#{{ $proveedor->proveedor_id }}</span>
                                <h6 class="mb-0 fw-bold">{{ $proveedor->nombre }}</h6>
                            </div>
                            @if($proveedor->estado == 'Activo')
                                <span class="estado-activo-badge">{{ $proveedor->estado }}</span>
                            @else
                                <span class="estado-inactivo-badge">{{ $proveedor->estado }}</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ Str::limit($proveedor->direccion, 60) }}
                                    </small>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-12 col-sm-6 mb-1">
                                    <small class="contacto-info">
                                        <i class="fas fa-phone me-1"></i> {{ $proveedor->telefono }}
                                    </small>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <small class="contacto-info">
                                        <i class="fas fa-envelope me-1"></i> {{ Str::limit($proveedor->correo, 20) }}
                                    </small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="stat-mini">
                                        <span class="text-muted small">Total Compras</span>
                                        <div class="fw-bold text-success">₡{{ number_format($proveedor->total_compras, 2) }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-mini">
                                        <span class="text-muted small">Insumos</span>
                                        <div class="fw-bold">
                                            @if($proveedor->insumos->count() > 0)
                                                <span class="badge bg-success">{{ $proveedor->insumos->count() }}</span>
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
                                <button type="button" class="btn btn-info btn-sm flex-fill" onclick="openShowProveedorModal({{ $proveedor->proveedor_id }})">
                                    <i class="fas fa-eye me-1"></i>
                                    <span class="d-none d-sm-inline">Ver</span>
                                </button>
                                <button type="button" class="btn btn-warning btn-sm flex-fill" onclick="openEditProveedorModal({{ $proveedor->proveedor_id }})">
                                    <i class="fas fa-edit me-1"></i>
                                    <span class="d-none d-sm-inline">Editar</span>
                                </button>
                                <form action="{{ route('proveedores.destroy', $proveedor->proveedor_id) }}" method="POST" class="flex-fill">
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
            <form id="createProveedorForm" action="{{ route('proveedores.store') }}" method="POST">
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
                        @foreach($insumos as $insumo)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="insumos[]" value="{{ $insumo->insumo_id }}" id="create_proveedor_insumo{{ $insumo->insumo_id }}">
                            <label class="form-check-label" for="create_proveedor_insumo{{ $insumo->insumo_id }}">
                                <strong>{{ $insumo->nombre }}</strong> - ₡{{ number_format($insumo->precio, 2) }}
                                <br><small class="text-muted">{{ $insumo->unidad_medida }} | Stock: {{ $insumo->stock_actual }}</small>
                            </label>
                        </div>
                        @endforeach
                        @if($insumos->count() == 0)
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
<script src="{{ asset('js/proveedor-modals.js') }}"></script>
<script src="{{ asset('js/proveedor-validations.js') }}"></script>
<script src="{{ asset('js/proveedor-filters.js') }}"></script>
@endpush