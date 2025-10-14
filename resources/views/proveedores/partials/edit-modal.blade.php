<form id="editProveedorForm" action="{{ route('proveedores.update', $proveedor->proveedor_id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="mb-3">
        <label for="edit_proveedor_nombre" class="form-label">Nombre del Proveedor *</label>
        <input type="text" class="form-control" id="edit_proveedor_nombre" name="nombre" required value="{{ $proveedor->nombre }}">
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="edit_proveedor_telefono" class="form-label">Teléfono *</label>
                <input type="text" class="form-control" id="edit_proveedor_telefono" name="telefono" required value="{{ $proveedor->telefono }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="edit_proveedor_correo" class="form-label">Correo Electrónico *</label>
                <input type="email" class="form-control" id="edit_proveedor_correo" name="correo" required value="{{ $proveedor->correo }}">
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="edit_proveedor_direccion" class="form-label">Dirección *</label>
        <textarea class="form-control" id="edit_proveedor_direccion" name="direccion" required placeholder="Ingrese la dirección completa del proveedor">{{ $proveedor->direccion }}</textarea>
    </div>

    <div class="section-divider"></div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="edit_proveedor_total_compras" class="form-label">Total de Compras *</label>
                <div class="input-group">
                    <span class="input-group-text">₡</span>
                    <input type="number" step="0.01" class="form-control" id="edit_proveedor_total_compras" name="total_compras" required value="{{ $proveedor->total_compras }}" min="0">
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="edit_proveedor_estado" class="form-label">Estado *</label>
                <select class="form-select" id="edit_proveedor_estado" name="estado" required>
                    <option value="Activo" {{ $proveedor->estado == 'Activo' ? 'selected' : '' }}>Activo</option>
                    <option value="Inactivo" {{ $proveedor->estado == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
        </div>
    </div>

    <div class="section-divider"></div>
    
    <div class="mb-3">
        <label class="form-label">Insumos que Provee <span class="info-tooltip" data-tooltip="Seleccione los insumos que este proveedor puede suministrar">ℹ️</span></label>
        
        <div class="border p-3 rounded" style="background-color: white; border-radius: 10px; max-height: 200px; overflow-y: auto;">
            @foreach($insumos as $insumo)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="insumos[]" value="{{ $insumo->insumo_id }}" id="edit_proveedor_insumo{{ $insumo->insumo_id }}"
                       {{ in_array($insumo->insumo_id, $proveedor->insumos->pluck('insumo_id')->toArray()) ? 'checked' : '' }}>
                <label class="form-check-label" for="edit_proveedor_insumo{{ $insumo->insumo_id }}">
                    <strong>{{ $insumo->nombre }}</strong> - ₡{{ number_format($insumo->precio, 2) }}
                    <br><small class="text-muted">{{ $insumo->unidad_medida }} | Stock: {{ $insumo->stock_actual }}</small>
                </label>
            </div>
            @endforeach
            @if($insumos->count() == 0)
            <div class="text-center p-3">
                <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                <p class="text-muted">No hay insumos disponibles.</p>
            </div>
            @endif
        </div>
        <small class="text-muted mt-2 d-block">
            <i class="fas fa-info-circle"></i> 
            Actualmente seleccionados: {{ $proveedor->insumos->count() }} insumos
        </small>
    </div>

    <div class="modal-actions">
        <button type="button" class="btn btn-secondary" onclick="closeProveedorModal('editProveedorModal')">
            <i class="fas fa-times"></i> Cancelar
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Actualizar Proveedor
        </button>
    </div>
</form>