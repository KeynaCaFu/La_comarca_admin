@extends('layouts.app')

@section('title', 'Gestión de Insumos')

@push('styles')
<link href="{{ asset('css/validations.css') }}" rel="stylesheet">
<link href="{{ asset('css/pages/supplies.css') }}" rel="stylesheet">
<style>
/* Estilos para los filtros colapsables */
.filtros-simples {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    border: 2px solid #e9ecef;
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

/* Estilos para barra de búsqueda */
.input-group .input-group-text {
    border: 2px solid #e9ecef;
    border-right: none;
}

.input-group .form-control {
    border: 2px solid #e9ecef;
    border-left: none;
}

.input-group .form-control:focus {
    border-color: #485a1a;
    box-shadow: none;
}

.input-group .form-control:focus + .input-group-text,
.input-group .input-group-text + .form-control:focus {
    border-color: #485a1a;
}

/* Botones de filtro */
.btn-filtro {
    border-radius: 20px;
    padding: 8px 16px;
    border: 2px solid #dee2e6;
    background: white;
    color: #6c757d;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-filtro:hover {
    background: #f8f9fa;
    border-color: #485a1a;
    color: #485a1a;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.btn-filtro.activo {
    background: #485a1a;
    border-color: #485a1a;
    color: white;
    font-weight: bold;
}

.btn-filtro.activo:hover {
    background: #3a4815;
    border-color: #3a4815;
    color: white;
}

.contador-filtro {
    background: #ff9900;
    color: white;
    font-size: 0.75rem;
    padding: 2px 8px;
    border-radius: 10px;
    font-weight: bold;
    min-width: 25px;
    text-align: center;
}

.btn-filtro.activo .contador-filtro {
    background: white;
    color: #485a1a;
}

/* Animación del collapse */
#filtrosCollapse {
    transition: all 0.3s ease;
}

#filtrosCollapse.collapsing {
    transition: height 0.35s ease;
}

.resumen-resultados {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #485a1a;
    margin-bottom: 20px;
}

/* Botón de filtrar */
.btn-outline-primary {
    border: 2px solid #485a1a;
    color: #485a1a;
}

.btn-outline-primary:hover {
    background: #485a1a;
    border-color: #485a1a;
    color: white;
}

/* Badge de filtros activos */
.badge.bg-danger {
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

/* Acordeón de filtros (look limpio como la captura) */
.accordion-filtros { border-radius: 10px; overflow: hidden; }
.accordion-filtros-header {
    background: #ffffff;
    border: 2px solid #e1e7e4;
    border-radius: 10px;
    padding: 14px 16px;
    cursor: pointer;
    transition: background .2s ease, border-color .2s ease;
}
.accordion-filtros-header:hover { background: #f9fbfa; border-color: #cfd8d4; }
.accordion-filtros-header .chevron { transition: transform .2s ease; color: #6c757d; }
.accordion-filtros-header[aria-expanded="true"] .chevron { transform: rotate(180deg); }
.accordion-filtros-body {
    border: 2px solid #e1e7e4;
    border-top: none;
    border-radius: 0 0 10px 10px;
    background: #e7edeb; /* gris verdoso suave como en la captura */
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

<!-- Acordeón de Filtros -->
<div class="accordion-filtros mb-3">
    <div class="accordion-filtros-header" role="button"
         data-bs-toggle="collapse" data-bs-target="#filtrosCollapse"
         aria-controls="filtrosCollapse"
         aria-expanded="{{ request()->hasAny(['estado', 'stock', 'vencimiento']) ? 'true' : 'false' }}">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-filter"></i>
                <span class="fw-semibold">Filtros de Búsqueda</span>
                <i class="fas fa-chevron-down chevron"></i>
            </div>
            <span class="h6 text-muted m-0" id="totalSuppliesText">
                📦 <strong>{{ $supplies->count() }}</strong> de <strong>{{ $totals['all'] ?? 0 }}</strong> insumos
            </span>
        </div>
    </div>

    <div id="filtrosCollapse" class="collapse {{ request()->hasAny(['estado', 'stock', 'vencimiento']) ? 'show' : '' }}">
        <div class="accordion-filtros-body filtros-simples">
            <!-- Búsqueda por nombre -->
            <div class="mb-3">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text"
                                   class="form-control"
                                   id="filtroNombre"
                                   name="buscar"
                                   value="{{ request('buscar') }}"
                                   placeholder="Buscar insumo por nombre..."
                                   autocomplete="off">
                            @if(request('buscar'))
                            <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('filtroNombre').value=''; buscarEnTiempoReal();">
                                <i class="fas fa-times"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0"><i class="fas fa-sliders-h"></i> <strong>Opciones de Filtrado</strong></h6>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarFiltrosSupply()">
                    <i class="fas fa-eraser"></i> Limpiar Filtros
                </button>
            </div>

        <!-- Filtros por Estado -->
        <div class="row mb-3">
            <div class="col-md-12">
                <h6 class="mb-2">📊 <strong>Estado del Insumo:</strong></h6>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#" 
                       class="btn btn-filtro filtro-estado {{ !request('estado') ? 'activo' : '' }}"
                       data-estado="">
                        <i class="fas fa-list"></i> Todos
                        <span class="contador-filtro">{{ $totals['all'] ?? 0 }}</span>
                    </a>
                    
                    <a href="#" 
                       class="btn btn-filtro filtro-estado {{ request('estado') == 'Disponible' ? 'activo' : '' }}"
                       data-estado="Disponible">
                        <i class="fas fa-check-circle text-success"></i> Disponibles
                        <span class="contador-filtro">{{ $totals['available'] ?? 0 }}</span>
                    </a>
                    
                    <a href="#" 
                       class="btn btn-filtro filtro-estado {{ request('estado') == 'Agotado' ? 'activo' : '' }}"
                       data-estado="Agotado">
                        <i class="fas fa-times-circle text-danger"></i> Agotados
                        <span class="contador-filtro">{{ $totals['out_of_stock'] ?? 0 }}</span>
                    </a>
                    
                    <a href="#" 
                       class="btn btn-filtro filtro-estado {{ request('estado') == 'Vencido' ? 'activo' : '' }}"
                       data-estado="Vencido">
                        <i class="fas fa-exclamation-triangle text-warning"></i> Vencidos
                        <span class="contador-filtro">{{ $totals['expired'] ?? 0 }}</span>
                    </a>
                </div>
            </div>
        </div>
            
            <!-- Filtros de Alertas -->
        <div class="row">
            <div class="col-md-12">
                <h6 class="mb-2">⚠️ <strong>Alertas de Inventario:</strong></h6>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#" 
                       class="btn btn-filtro filtro-stock {{ request('stock') == 'bajo' ? 'activo' : '' }}"
                       data-stock="bajo">
                        <i class="fas fa-exclamation-circle text-warning"></i> Stock Bajo
                        <span class="contador-filtro">{{ $totals['low_stock'] ?? 0 }}</span>
                    </a>
                    
                    <a href="#" 
                       class="btn btn-filtro filtro-vencimiento {{ request('vencimiento') == 'por_vencer' ? 'activo' : '' }}"
                       data-vencimiento="por_vencer">
                        <i class="fas fa-clock text-info"></i> Por Vencer (30 días)
                        <span class="contador-filtro">{{ $totals['expiring_soon'] ?? 0 }}</span>
                    </a>
                    
                    <a href="#" 
                       class="btn btn-filtro filtro-vencimiento {{ request('vencimiento') == 'vencidos' ? 'activo' : '' }}"
                       data-vencimiento="vencidos">
                        <i class="fas fa-calendar-times text-danger"></i> Ya Vencidos
                        <span class="contador-filtro">{{ $totals['expired'] ?? 0 }}</span>
                    </a>
                    
                    <a href="#" 
                       class="btn btn-filtro filtro-vencimiento {{ request('vencimiento') == 'buenos' ? 'activo' : '' }}"
                       data-vencimiento="buenos">
                        <i class="fas fa-calendar-check text-success"></i> En Buen Estado
                        <span class="contador-filtro">{{ $totals['good'] ?? 0 }}</span>
                    </a>
                </div>
            </div>
            </div>
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
    
    <a href="#" class="btn btn-sm btn-outline-secondary ms-2" onclick="limpiarFiltrosSupply(); return false;">
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
@if($supplies->count() > 0)
<div class="table-responsive">
    <table class="table">
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
            @foreach($supplies as $supply)
                    @php
                        $fechaVencimiento = $supply->expiration_date ? \Carbon\Carbon::parse($supply->expiration_date) : null;
                        $diasRestantes = $fechaVencimiento ? \Carbon\Carbon::now()->diffInDays($fechaVencimiento, false) : null;
                        $vencimientoEstado = 'bueno';
                        if ($diasRestantes !== null) {
                            if ($diasRestantes < 0) {
                                $vencimientoEstado = 'vencido';
                            } elseif ($diasRestantes <= 30) {
                                $vencimientoEstado = 'por_vencer';
                            }
                        }
                    @endphp
                    <tr class="supply-row {{ $supply->status_in_spanish == 'Vencido' ? 'table-danger' : ($supply->current_stock <= $supply->minimum_stock ? 'table-warning' : '') }}"
                        data-nombre="{{ strtolower($supply->name) }}"
                        data-estado="{{ $supply->status_in_spanish }}"
                        data-stock-bajo="{{ $supply->current_stock <= $supply->minimum_stock ? 'true' : 'false' }}"
                        data-vencimiento="{{ $vencimientoEstado }}">
                        <td>{{ $supply->supply_id }}</td>
                        <td>
                            <strong>{{ $supply->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $supply->unit_of_measure }} - Cant: {{ $supply->quantity }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $supply->current_stock > $supply->minimum_stock ? 'success' : 'warning' }}">
                                {{ $supply->current_stock }}
                            </span>
                            <small class="text-muted d-block">Mín: {{ $supply->minimum_stock }}</small>
                            @if($supply->current_stock <= $supply->minimum_stock)
                                <small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Stock bajo</small>
                            @endif
                        </td>
                        <td>₡{{ number_format($supply->price, 0) }}</td>
                        <td>
                            @if($supply->expiration_date)
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
                            @if($supply->suppliers->count() > 0)
                                @foreach($supply->suppliers->take(2) as $supplier)
                                    <span class="badge-insumo">{{ $supplier->name }}</span>
                                @endforeach
                                @if($supply->suppliers->count() > 2)
                                    <span class="badge bg-secondary">+{{ $supply->suppliers->count() - 2 }}</span>
                                @endif
                            @else
                                <span class="text-muted">Sin proveedores</span>
                            @endif
                        </td>
                        <td>
                            @if($supply->status_in_spanish == 'Disponible')
                                <span class="badge bg-success">✅ {{ $supply->status_in_spanish }}</span>
                            @elseif($supply->status_in_spanish == 'Agotado')
                                <span class="badge bg-danger">❌ {{ $supply->status_in_spanish }}</span>
                            @else
                                <span class="badge bg-secondary">💀 {{ $supply->status_in_spanish }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-info btn-sm" title="Ver detalles" 
                                    onclick="openShowModal({{ $supply->supply_id }})"
                                    onmouseenter="preloadShowModal({{ $supply->supply_id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" title="Editar" 
                                    onclick="openEditModal({{ $supply->supply_id }})"
                                    onmouseenter="preloadEditModal({{ $supply->supply_id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('supplies.destroy', $supply->supply_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
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
        <button type="button" class="btn btn-outline-secondary me-2" onclick="limpiarFiltrosSupply()">
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
            
            <form id="createForm" action="{{ route('supplies.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="create_nombre" class="form-label">
                                Nombre del Insumo *
                            </label>
                            <input type="text" class="form-control" id="create_nombre" name="nombre" required 
                                   placeholder="Ej: Harina de Trigo" 
                                   pattern="^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s\-\.]+$"
                                   title="Solo se permiten letras, espacios, guiones y puntos"
                                   maxlength="255">
                            <div class="invalid-feedback"></div>
                            {{-- <small class="form-text text-muted">Solo letras, espacios, guiones y puntos</small> --}}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="create_unidad_medida" class="form-label">
                                Unidad de Medida *
                            </label>
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
                            <label for="create_stock_actual" class="form-label">
                                Stock Actual *
                                <i class="fas fa-info-circle text-info ms-1" 
                                   data-bs-toggle="tooltip" 
                                   data-bs-placement="right" 
                                   title="Cantidad actual disponible en inventario de este insumo."></i>
                            </label>
                            <input type="number" class="form-control" id="create_stock_actual" name="stock_actual" 
                                   required value="0" min="0" max="999999" step="1"
                                   title="Solo números enteros del 0 al 999,999">
                            <div class="invalid-feedback"></div>
                            {{-- <small class="form-text text-muted">Números enteros del 0 al 999,999</small> --}}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="create_stock_minimo" class="form-label">
                                Stock Mínimo *
                            </label>
                            <input type="number" class="form-control" id="create_stock_minimo" name="stock_minimo" 
                                   required value="0" min="0" max="999999" step="1"
                                   title="Solo números enteros del 0 al 999,999">
                            <div class="invalid-feedback"></div>
                            {{-- <small class="form-text text-muted">Números enteros del 0 al 999,999</small> --}}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="create_cantidad" class="form-label">
                                Cantidad *
                            </label>
                            <input type="number" class="form-control" id="create_cantidad" name="cantidad" 
                                   required value="1" min="1" max="999999" step="1"
                                   title="Solo números enteros del 1 al 999,999">
                            <div class="invalid-feedback"></div>
                            {{-- <small class="form-text text-muted">Números enteros del 1 al 999,999</small> --}}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="create_precio" class="form-label">
                                Precio *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">₡</span>
                                <input type="number" step="0.01" class="form-control" id="create_precio" name="precio" 
                                       required min="0.01" max="999999.99" placeholder="0.00"
                                       title="Precio válido entre ₡0.01 y ₡999,999.99">
                            </div>
                            <div class="invalid-feedback"></div>
                            {{-- <small class="form-text text-muted">Precio entre ₡0.01 y ₡999,999.99</small> --}}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="create_fecha_vencimiento" class="form-label">
                                Fecha de Vencimiento
                                <i class="fas fa-info-circle text-info ms-1" 
                                   data-bs-toggle="tooltip" 
                                   data-bs-placement="right" 
                                   title="Fecha en la que el insumo expirará. Recibirá alertas 30 días antes del vencimiento."></i>
                            </label>
                            <input type="date" class="form-control" id="create_fecha_vencimiento" name="fecha_vencimiento"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   title="La fecha debe ser posterior a hoy">
                            <div class="invalid-feedback"></div>
                            <small class="form-text text-muted">Opcional - debe ser posterior a hoy</small>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="create_estado" class="form-label">
                        Estado *
                        <i class="fas fa-info-circle text-info ms-1" 
                           data-bs-toggle="tooltip" 
                           data-bs-placement="right" 
                           title="Estado actual del insumo. Disponible: listo para usar, Agotado: sin stock, Vencido: expirado."></i>
                    </label>
                    <select class="form-select" id="create_estado" name="estado" required>
                        <option value="">Seleccionar estado...</option>
                        <option value="Disponible">✅ Disponible</option>
                        <option value="Agotado">❌ Agotado</option>
                        <option value="Vencido">💀 Vencido</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Proveedores
                        <i class="fas fa-info-circle text-info ms-1" 
                           data-bs-toggle="tooltip" 
                           data-bs-placement="right" 
                           title="Seleccione uno o más proveedores que suministran este insumo. Puede buscar por nombre o teléfono."></i>
                    </label>
                    <!-- Buscador de proveedores -->
                    <div class="mb-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   id="create_buscarProveedor" 
                                   placeholder="Buscar proveedor por nombre o teléfono..."
                                   autocomplete="off">
                        </div>
                    </div>
                    <div class="border p-3 rounded" style="max-height: 250px; overflow-y: auto;" id="create_proveedoresList">
                        @foreach($suppliers as $supplier)
                        <div class="form-check proveedor-item" data-nombre="{{ strtolower($supplier->name) }}" data-telefono="{{ $supplier->phone }}">
                            <input class="form-check-input" type="checkbox" name="proveedores[]" 
                                   value="{{ $supplier->supplier_id }}" id="create_proveedor{{ $supplier->supplier_id }}">
                            <label class="form-check-label" for="create_proveedor{{ $supplier->supplier_id }}">
                                {{ $supplier->name }} - {{ $supplier->phone }}
                            </label>
                        </div>
                        @endforeach
                        @if($suppliers->count() == 0)
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
<script src="{{ asset('js/supply-modals.js') }}"></script>
<script src="{{ asset('js/supply-validations.js') }}"></script>
<script src="{{ asset('js/supply-filters.js') }}"></script>
<!-- Inicializar tooltips de Bootstrap en la página y dentro de los modales -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips presentes en el DOM
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (el) {
        const existing = bootstrap.Tooltip.getInstance(el);
        if (existing) existing.dispose();
        new bootstrap.Tooltip(el);
    });

    // Re-inicializar tooltips cuando cambie el contenido del modal de editar
    const editModalContent = document.getElementById('editModalContent');
    if (editModalContent) {
        const obs = new MutationObserver(() => {
            setTimeout(() => {
                const innerEls = [].slice.call(editModalContent.querySelectorAll('[data-bs-toggle="tooltip"]'));
                innerEls.forEach(function (el) {
                    const existing = bootstrap.Tooltip.getInstance(el);
                    if (existing) existing.dispose();
                    new bootstrap.Tooltip(el);
                });
            }, 100);
        });
        obs.observe(editModalContent, { childList: true, subtree: true });
    }
});
</script>
@endpush