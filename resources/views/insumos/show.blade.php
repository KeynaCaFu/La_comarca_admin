@extends('layouts.app')

@section('title', 'Detalles del Insumo')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-info-circle"></i> Detalles del Insumo</h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h4>{{ $insumo->nombre }}</h4>
                        <p class="text-muted">ID: {{ $insumo->insumo_id }}</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge bg-{{ $insumo->estado == 'Disponible' ? 'success' : ($insumo->estado == 'Agotado' ? 'danger' : 'secondary') }} fs-6">
                            {{ $insumo->estado }}
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h5>Información General</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Unidad de Medida:</th>
                                <td>{{ $insumo->unidad_medida }}</td>
                            </tr>
                            <tr>
                                <th>Cantidad:</th>
                                <td>{{ $insumo->cantidad }}</td>
                            </tr>
                            <tr>
                                <th>Precio:</th>
                                <td>₡{{ number_format($insumo->precio, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Fecha Vencimiento:</th>
                                <td>{{ $insumo->fecha_vencimiento ? $insumo->fecha_vencimiento->format('d/m/Y') : 'No especificada' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Inventario</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Stock Actual:</th>
                                <td>
                                    <span class="badge bg-{{ $insumo->stock_actual > $insumo->stock_minimo ? 'success' : 'warning' }}">
                                        {{ $insumo->stock_actual }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Stock Mínimo:</th>
                                <td>{{ $insumo->stock_minimo }}</td>
                            </tr>
                            <tr>
                                <th>Diferencia:</th>
                                <td>
                                    <span class="badge bg-{{ ($insumo->stock_actual - $insumo->stock_minimo) >= 0 ? 'success' : 'danger' }}">
                                        {{ $insumo->stock_actual - $insumo->stock_minimo }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mb-4">
                    <h5>Proveedores</h5>
                    @if($insumo->proveedores->count() > 0)
                    <div class="row">
                        @foreach($insumo->proveedores as $proveedor)
                        <div class="col-md-6 mb-2">
                            <div class="card">
                                <div class="card-body py-2">
                                    <h6 class="card-title mb-1">{{ $proveedor->nombre }}</h6>
                                    <p class="card-text small mb-1">
                                        <i class="fas fa-phone"></i> {{ $proveedor->telefono }}<br>
                                        <i class="fas fa-envelope"></i> {{ $proveedor->correo }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Este insumo no tiene proveedores asignados.
                    </div>
                    @endif
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('insumos.index') }}" class="btn btn-secondary me-md-2">
                        <i class="fas fa-arrow-left"></i> Volver a la lista
                    </a>
                    <a href="{{ route('insumos.edit', $insumo->insumo_id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Editar Insumo
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection