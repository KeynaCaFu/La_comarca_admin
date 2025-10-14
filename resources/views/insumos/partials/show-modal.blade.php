<div class="detail-section">
    <div class="row mb-4">
        <div class="col-md-8">
            <h4 style="color: #485a1a; margin-bottom: 5px;">{{ $insumo->nombre }}</h4>
            <p class="text-muted">ID: {{ $insumo->insumo_id }}</p>
        </div>
        <div class="col-md-4 text-end">
            <span class="status-badge status-{{ strtolower($insumo->estado) }}">
                {{ $insumo->estado }}
            </span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h5>Información General</h5>
            <table class="detail-table">
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
            <table class="detail-table">
                <tr>
                    <th>Stock Actual:</th>
                    <td>
                        <span class="status-badge {{ $insumo->stock_actual > $insumo->stock_minimo ? 'status-disponible' : 'status-agotado' }}">
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
                        <span class="status-badge {{ ($insumo->stock_actual - $insumo->stock_minimo) >= 0 ? 'status-disponible' : 'status-agotado' }}">
                            {{ $insumo->stock_actual - $insumo->stock_minimo }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <h5>Proveedores</h5>
        @if($insumo->proveedores->count() > 0)
        <div class="row">
            @foreach($insumo->proveedores as $proveedor)
            <div class="col-md-6 mb-2">
                <div class="proveedor-card">
                    <h6>{{ $proveedor->nombre }}</h6>
                    <p>
                        <i class="fas fa-phone"></i> {{ $proveedor->telefono }}<br>
                        <i class="fas fa-envelope"></i> {{ $proveedor->correo }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="alert alert-warning" style="background-color: #fff3cd; border: 1px solid #ffecb5; color: #856404; padding: 15px; border-radius: 8px;">
            <i class="fas fa-exclamation-triangle"></i> Este insumo no tiene proveedores asignados.
        </div>
        @endif
    </div>

    <div class="modal-actions">
        <button type="button" class="btn btn-secondary" onclick="closeModal('showModal')">
            <i class="fas fa-times"></i> Cerrar
        </button>
    </div>
</div>