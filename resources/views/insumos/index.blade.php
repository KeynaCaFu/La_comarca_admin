@extends('layouts.app')

@section('title', 'Gestión de Insumos')

@push('styles')
<link href="{{ asset('css/validations.css') }}" rel="stylesheet">
<link href="{{ asset('css/pages/insumos.css') }}" rel="stylesheet">
<style>
/* Estilos simples para los filtros */
.filtros-simples {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.filtros-simples .form-control, .filtros-simples .form-select {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 8px 12px;
    transition: all 0.3s ease;
}

.filtros-simples .form-control:focus, .filtros-simples .form-select:focus {
    border-color: #485a1a;
    box-shadow: 0 0 0 0.2rem rgba(72, 90, 26, 0.25);
}

.btn-filtro {
    border-radius: 20px;
    padding: 8px 16px;
    margin: 2px;
    border: 2px solid #dee2e6;
    background: white;
    color: #6c757d;
    transition: all 0.3s ease;
}

.btn-filtro:hover, .btn-filtro.activo {
    background: #485a1a;
    border-color: #485a1a;
    color: white;
    transform: translateY(-1px);
}

.contador-filtro {
    background: #ff9900;
    color: white;
    font-size: 0.7rem;
    padding: 2px 6px;
    border-radius: 10px;
    margin-left: 5px;
}

.resumen-resultados {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #485a1a;
    margin-bottom: 20px;
}
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-boxes"></i> Gestión de Insumos</h1>
    <button type="button" class="btn btn-primary" onclick="openCreateModal()">
        <i class="fas fa-plus"></i> Nuevo Insumo
    </button>
</div>

<!-- Filtros Simples -->
<div class="filtros-simples">
    <form method="GET" action="{{ route('insumos.index') }}" id="filtrosForm">
        <div class="row align-items-end">
            <!-- Búsqueda por nombre -->
            <div class="col-md-4">
                <label class="form-label fw-bold">🔍 Buscar por nombre:</label>
                <input type="text" 
                       class="form-control" 
                       name="buscar" 
                       value="{{ request('buscar') }}" 
                       placeholder="Escribe el nombre del insumo..."
                       onkeyup="buscarEnTiempoReal()">
            </div>
            
            <!-- Botón de limpiar -->
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">
                    <i class="fas fa-eraser"></i> Limpiar
                </button>
            </div>
            
            <!-- Mostrar total -->
            <div class="col-md-6 text-end">
                <span class="h5 text-muted">
                    📦 Total: <strong>{{ $insumos->count() }}</strong> de <strong>{{ $totales['todos'] }}</strong> insumos
                </span>
            </div>
        </div>
    </form>
</div>

<!-- Filtros Rápidos con Botones -->
<div class="mb-4">
    <div class="row">
        <div class="col-md-12">
            <h6 class="mb-3">📊 <strong>Filtrar por Estado:</strong></h6>
            <div class="d-flex flex-wrap">
                <a href="{{ route('insumos.index') }}" 
                   class="btn btn-filtro {{ !request('estado') ? 'activo' : '' }}">
                    📋 Todos <span class="contador-filtro">{{ $totales['todos'] }}</span>
                </a>
                
                <a href="{{ route('insumos.index', ['estado' => 'Disponible']) }}" 
                   class="btn btn-filtro {{ request('estado') == 'Disponible' ? 'activo' : '' }}">
                    ✅ Disponibles <span class="contador-filtro">{{ $totales['disponibles'] }}</span>
                </a>
                
                <a href="{{ route('insumos.index', ['estado' => 'Agotado']) }}" 
                   class="btn btn-filtro {{ request('estado') == 'Agotado' ? 'activo' : '' }}">
                    ❌ Agotados <span class="contador-filtro">{{ $totales['agotados'] }}</span>
                </a>
                
                <a href="{{ route('insumos.index', ['estado' => 'Vencido']) }}" 
                   class="btn btn-filtro {{ request('estado') == 'Vencido' ? 'activo' : '' }}">
                    💀 Vencidos <span class="contador-filtro">{{ $totales['vencidos'] }}</span>
                </a>
            </div>
        </div>
    </div>
    
    <div class="row mt-3">
        <div class="col-md-12">
            <h6 class="mb-3">⚠️ <strong>Filtros de Alerta:</strong></h6>
            <div class="d-flex flex-wrap">
                <a href="{{ route('insumos.index', ['stock' => 'bajo']) }}" 
                   class="btn btn-filtro {{ request('stock') == 'bajo' ? 'activo' : '' }}">
                    📉 Stock Bajo <span class="contador-filtro">{{ $totales['stock_bajo'] }}</span>
                </a>
                
                <a href="{{ route('insumos.index', ['vencimiento' => 'por_vencer']) }}" 
                   class="btn btn-filtro {{ request('vencimiento') == 'por_vencer' ? 'activo' : '' }}">
                    ⏰ Por Vencer <span class="contador-filtro">{{ $totales['por_vencer'] }}</span>
                </a>
                
                <a href="{{ route('insumos.index', ['vencimiento' => 'vencidos']) }}" 
                   class="btn btn-filtro {{ request('vencimiento') == 'vencidos' ? 'activo' : '' }}">
                    💀 Ya Vencidos
                </a>
                
                <a href="{{ route('insumos.index', ['vencimiento' => 'buenos']) }}" 
                   class="btn btn-filtro {{ request('vencimiento') == 'buenos' ? 'activo' : '' }}">
                    👍 En Buen Estado
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Mostrar filtros activos -->
@if(request()->hasAny(['buscar', 'estado', 'stock', 'vencimiento']))
<div class="resumen-resultados">
    <strong>🎯 Filtros activos:</strong>
    @if(request('buscar'))
        <span class="badge bg-primary">Buscar: "{{ request('buscar') }}"</span>
    @endif
    @if(request('estado'))
        <span class="badge bg-success">Estado: {{ request('estado') }}</span>
    @endif
    @if(request('stock'))
        <span class="badge bg-warning">Stock: {{ ucfirst(request('stock')) }}</span>
    @endif
    @if(request('vencimiento'))
        <span class="badge bg-info">Vencimiento: {{ ucfirst(str_replace('_', ' ', request('vencimiento'))) }}</span>
    @endif
    
    <a href="{{ route('insumos.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
        <i class="fas fa-times"></i> Quitar todos los filtros
    </a>
</div>
@endif

<!-- Alertas de validación automática -->
@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Tabla de Insumos -->
<div class="card">
    <div class="card-body">
        @if($insumos->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Stock</th>
                        <th>Precio</th>
                        <th>Vencimiento</th>
                        <th>Proveedores</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($insumos as $insumo)
                    <tr class="{{ $insumo->estado == 'Vencido' ? 'table-danger' : ($insumo->stock_actual <= $insumo->stock_minimo ? 'table-warning' : '') }}">
                        <td>{{ $insumo->insumo_id }}</td>
                        <td>
                            <strong>{{ $insumo->nombre }}</strong>
                            <br>
                            <small class="text-muted">{{ $insumo->unidad_medida }} - Cant: {{ $insumo->cantidad }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $insumo->stock_actual > $insumo->stock_minimo ? 'success' : 'warning' }}">
                                {{ $insumo->stock_actual }}
                            </span>
                            <small class="text-muted d-block">Mín: {{ $insumo->stock_minimo }}</small>
                            @if($insumo->stock_actual <= $insumo->stock_minimo)
                                <small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Stock bajo</small>
                            @endif
                        </td>
                        <td>₡{{ number_format($insumo->precio, 0) }}</td>
                        <td>
                            @if($insumo->fecha_vencimiento)
                                @php
                                    $fechaVencimiento = \Carbon\Carbon::parse($insumo->fecha_vencimiento);
                                    $diasRestantes = \Carbon\Carbon::now()->diffInDays($fechaVencimiento, false);
                                @endphp
                                
                                <span class="badge bg-{{ $diasRestantes < 0 ? 'danger' : ($diasRestantes <= 30 ? 'warning' : 'success') }}">
                                    {{ $fechaVencimiento->format('d/m/Y') }}
                                </span>
                                
                                @if($diasRestantes < 0)
                                    <small class="text-danger d-block"><i class="fas fa-skull-crossbones"></i> Vencido</small>
                                @elseif($diasRestantes <= 30)
                                    <small class="text-warning d-block"><i class="fas fa-clock"></i> {{ $diasRestantes }} días</small>
                                @endif
                            @else
                                <span class="text-muted">Sin fecha</span>
                            @endif
                        </td>
                        <td>
                            @if($insumo->proveedores->count() > 0)
                                @foreach($insumo->proveedores->take(2) as $proveedor)
                                    <span class="badge-insumo">{{ $proveedor->nombre }}</span>
                                @endforeach
                                @if($insumo->proveedores->count() > 2)
                                    <span class="badge bg-secondary">+{{ $insumo->proveedores->count() - 2 }}</span>
                                @endif
                            @else
                                <span class="text-muted">Sin proveedores</span>
                            @endif
                        </td>
                        <td>
                            @if($insumo->estado == 'Disponible')
                                <span class="badge bg-success">✅ {{ $insumo->estado }}</span>
                            @elseif($insumo->estado == 'Agotado')
                                <span class="badge bg-danger">❌ {{ $insumo->estado }}</span>
                            @else
                                <span class="badge bg-secondary">💀 {{ $insumo->estado }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-info btn-sm" title="Ver detalles" onclick="openShowModal({{ $insumo->insumo_id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" title="Editar" onclick="openEditModal({{ $insumo->insumo_id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('insumos.destroy', $insumo->insumo_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar" 
                                        onclick="return confirm('¿Estás seguro de eliminar este insumo?')">
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
        @else
        <div class="text-center py-5">
            @if(request()->hasAny(['buscar', 'estado', 'stock', 'vencimiento']))
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4>😔 No se encontraron insumos</h4>
                <p class="text-muted">No hay insumos que coincidan con los filtros seleccionados.</p>
                <button type="button" class="btn btn-outline-secondary me-2" onclick="limpiarFiltros()">
                    <i class="fas fa-eraser"></i> Quitar Filtros
                </button>
            @else
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h4>📦 No hay insumos registrados</h4>
                <p class="text-muted">Comienza agregando tu primer insumo.</p>
            @endif
            <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                <i class="fas fa-plus"></i> Crear Insumo
            </button>
        </div>
        @endif
    </div>
</div>

<!-- Modal para Ver Detalles -->
<div id="showModal" class="custom-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-info-circle"></i> Detalles del Insumo</h3>
            <span class="close" onclick="closeModal('showModal')">&times;</span>
        </div>
        <div class="modal-body" id="showModalContent">
            <!-- El contenido se cargará aquí dinámicamente -->
        </div>
    </div>
</div>

<!-- Modal para Crear -->
<div id="createModal" class="custom-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Crear Nuevo Insumo</h3>
            <span class="close" onclick="closeModal('createModal')">&times;</span>
        </div>
        <div class="modal-body">
            <!-- Mostrar errores de validación -->
            <div id="createErrors" class="alert alert-danger d-none">
                <ul class="mb-0" id="createErrorsList"></ul>
            </div>
            
            <form id="createForm" action="{{ route('insumos.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="create_nombre" class="form-label">Nombre del Insumo *</label>
                            <input type="text" class="form-control" id="create_nombre" name="nombre" required 
                                   placeholder="Ej: Harina de Trigo" 
                                   pattern="^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s\-\.]+$"
                                   title="Solo se permiten letras, espacios, guiones y puntos"
                                   maxlength="255">
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">Solo letras, espacios, guiones y puntos</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="create_unidad_medida" class="form-label">Unidad de Medida *</label>
                            <select class="form-select" id="create_unidad_medida" name="unidad_medida" required>
                                <option value="">Seleccionar unidad...</option>
                                <optgroup label="Peso">
                                    <option value="kg">Kilogramos (kg)</option>
                                    <option value="gramos">Gramos (g)</option>
                                </optgroup>
                                <optgroup label="Volumen">
                                    <option value="litros">Litros (L)</option>
                                    <option value="ml">Mililitros (ml)</option>
                                </optgroup>
                                <optgroup label="Longitud">
                                    <option value="metros">Metros (m)</option>
                                    <option value="cm">Centímetros (cm)</option>
                                </optgroup>
                                <optgroup label="Cantidad">
                                    <option value="unidades">Unidades</option>
                                    <option value="cajas">Cajas</option>
                                    <option value="bolsas">Bolsas</option>
                                    <option value="botellas">Botellas</option>
                                    <option value="latas">Latas</option>
                                    <option value="paquetes">Paquetes</option>
                                </optgroup>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="create_stock_actual" class="form-label">Stock Actual *</label>
                            <input type="number" class="form-control" id="create_stock_actual" name="stock_actual" 
                                   required value="0" min="0" max="999999" step="1"
                                   title="Solo números enteros del 0 al 999,999">
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">Números enteros del 0 al 999,999</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="create_stock_minimo" class="form-label">Stock Mínimo *</label>
                            <input type="number" class="form-control" id="create_stock_minimo" name="stock_minimo" 
                                   required value="0" min="0" max="999999" step="1"
                                   title="Solo números enteros del 0 al 999,999">
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">Números enteros del 0 al 999,999</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="create_cantidad" class="form-label">Cantidad *</label>
                            <input type="number" class="form-control" id="create_cantidad" name="cantidad" 
                                   required value="1" min="1" max="999999" step="1"
                                   title="Solo números enteros del 1 al 999,999">
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">Números enteros del 1 al 999,999</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="create_precio" class="form-label">Precio *</label>
                            <div class="input-group">
                                <span class="input-group-text">₡</span>
                                <input type="number" step="0.01" class="form-control" id="create_precio" name="precio" 
                                       required min="0.01" max="999999.99" placeholder="0.00"
                                       title="Precio válido entre ₡0.01 y ₡999,999.99">
                            </div>
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">Precio entre ₡0.01 y ₡999,999.99</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="create_fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                            <input type="date" class="form-control" id="create_fecha_vencimiento" name="fecha_vencimiento"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   title="La fecha debe ser posterior a hoy">
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">Opcional - debe ser posterior a hoy</small>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="create_estado" class="form-label">Estado *</label>
                    <select class="form-select" id="create_estado" name="estado" required>
                        <option value="">Seleccionar estado...</option>
                        <option value="Disponible">✅ Disponible</option>
                        <option value="Agotado">❌ Agotado</option>
                        <option value="Vencido">💀 Vencido</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Proveedores</label>
                    <div class="border p-3 rounded">
                        @foreach($proveedores as $proveedor)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="proveedores[]" 
                                   value="{{ $proveedor->proveedor_id }}" id="create_proveedor{{ $proveedor->proveedor_id }}">
                            <label class="form-check-label" for="create_proveedor{{ $proveedor->proveedor_id }}">
                                {{ $proveedor->nombre }} - {{ $proveedor->telefono }}
                            </label>
                        </div>
                        @endforeach
                        @if($proveedores->count() == 0)
                        <p class="text-muted">No hay proveedores activos.</p>
                        @endif
                    </div>
                    <small class="form-text text-muted">Selecciona uno o más proveedores (opcional)</small>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('createModal')">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Insumo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Editar -->
<div id="editModal" class="custom-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Editar Insumo</h3>
            <span class="close" onclick="closeModal('editModal')">&times;</span>
        </div>
        <div class="modal-body" id="editModalContent">
            <!-- El contenido se cargará aquí dinámicamente -->
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/insumo-modals.js') }}"></script>
<script src="{{ asset('js/insumo-validations.js') }}"></script>

<script>
// Búsqueda en tiempo real simple
let timeoutBusqueda;

function buscarEnTiempoReal() {
    clearTimeout(timeoutBusqueda);
    timeoutBusqueda = setTimeout(function() {
        document.getElementById('filtrosForm').submit();
    }, 500); // Espera 500ms después de que el usuario deje de escribir
}

function limpiarFiltros() {
    // Ir a la página sin filtros
    window.location.href = "{{ route('insumos.index') }}";
}
</script>
@endpush