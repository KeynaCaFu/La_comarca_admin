@extends('layouts.app')

@section('title', 'Crear Nuevo Proveedor')

@push('styles')
    <link href="{{ asset('css/pages/proveedores.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="form-container form-proveedores">
            <div class="d-flex align-items-center mb-4">
                <h3><i class="fas fa-plus"></i> Crear Nuevo Proveedor</h3>
            </div>
            
            <form action="{{ route('proveedores.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Proveedor *</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required 
                           value="{{ old('nombre') }}" placeholder="Ej: Distribuidora Alimentos Frescos">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono *</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required 
                                   value="{{ old('telefono') }}" placeholder="Ej: 3001234567">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo Electrónico *</label>
                            <input type="email" class="form-control" id="correo" name="correo" required 
                                   value="{{ old('correo') }}" placeholder="Ej: contacto@proveedor.com">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección *</label>
                    <textarea class="form-control" id="direccion" name="direccion" required 
                              placeholder="Ej: Calle 123 #45-67, Bogotá">{{ old('direccion') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="total_compras" class="form-label">Total de Compras *</label>
                            <div class="input-group price-input-group">
                                <span class="input-group-text">₡</span>
                                <input type="number" step="0.01" class="form-control" id="total_compras" name="total_compras" required 
                                       value="{{ old('total_compras', 0) }}" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado *</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="Activo" {{ old('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                                <option value="Inactivo" {{ old('estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Insumos que Provee</label>
                    <div class="checkbox-insumos">
                        @foreach($insumos as $insumo)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="insumos[]" 
                                   value="{{ $insumo->insumo_id }}" id="insumo{{ $insumo->insumo_id }}"
                                   {{ in_array($insumo->insumo_id, old('insumos', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="insumo{{ $insumo->insumo_id }}">
                                {{ $insumo->nombre }} - ₡{{ number_format($insumo->precio, 2) }}
                            </label>
                        </div>
                        @endforeach
                        @if($insumos->count() == 0)
                        <p class="text-muted p-3">No hay insumos disponibles. <a href="{{ route('insumos.create') }}">Crear insumo</a></p>
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save"></i> Guardar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection