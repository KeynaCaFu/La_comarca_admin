@extends('layouts.app')

@section('title', 'Editar Insumo')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-edit"></i> Editar Insumo: {{ $insumo->nombre }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('insumos.update', $insumo->insumo_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Insumo *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required 
                                       value="{{ old('nombre', $insumo->nombre) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unidad_medida" class="form-label">Unidad de Medida *</label>
                                <input type="text" class="form-control" id="unidad_medida" name="unidad_medida" required 
                                       value="{{ old('unidad_medida', $insumo->unidad_medida) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock_actual" class="form-label">Stock Actual *</label>
                                <input type="number" class="form-control" id="stock_actual" name="stock_actual" required 
                                       value="{{ old('stock_actual', $insumo->stock_actual) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock_minimo" class="form-label">Stock Mínimo *</label>
                                <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" required 
                                       value="{{ old('stock_minimo', $insumo->stock_minimo) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cantidad" class="form-label">Cantidad *</label>
                                <input type="number" class="form-control" id="cantidad" name="cantidad" required 
                                       value="{{ old('cantidad', $insumo->cantidad) }}" min="1">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio *</label>
                                <div class="input-group">
                                    <span class="input-group-text">₡</span>
                                    <input type="number" step="0.01" class="form-control" id="precio" name="precio" required 
                                           value="{{ old('precio', $insumo->precio) }}" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                                <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" 
                                       value="{{ old('fecha_vencimiento', $insumo->fecha_vencimiento ? $insumo->fecha_vencimiento->format('Y-m-d') : '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado *</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="Disponible" {{ old('estado', $insumo->estado) == 'Disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="Agotado" {{ old('estado', $insumo->estado) == 'Agotado' ? 'selected' : '' }}>Agotado</option>
                            <option value="Vencido" {{ old('estado', $insumo->estado) == 'Vencido' ? 'selected' : '' }}>Vencido</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Proveedores</label>
                        <div class="border p-3 rounded">
                            @foreach($proveedores as $proveedor)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="proveedores[]" 
                                       value="{{ $proveedor->proveedor_id }}" id="proveedor{{ $proveedor->proveedor_id }}"
                                       {{ in_array($proveedor->proveedor_id, old('proveedores', $insumo->proveedores->pluck('proveedor_id')->toArray())) ? 'checked' : '' }}>
                                <label class="form-check-label" for="proveedor{{ $proveedor->proveedor_id }}">
                                    {{ $proveedor->nombre }} - {{ $proveedor->telefono }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('insumos.index') }}" class="btn btn-secondary me-md-2">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Insumo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection