# Script para actualizar todas las vistas de español a inglés
# Ejecutar: .\update_views.ps1

Write-Host "=== Actualizando Vistas de Español a Inglés ===" -ForegroundColor Cyan
Write-Host ""

# Obtener todos los archivos .blade.php
$bladeFiles = Get-ChildItem -Path "resources\views" -Filter "*.blade.php" -Recurse

$totalFiles = $bladeFiles.Count
$currentFile = 0

foreach ($file in $bladeFiles) {
    $currentFile++
    Write-Host "[$currentFile/$totalFiles] Procesando: $($file.FullName)" -ForegroundColor Yellow
    
    $content = Get-Content $file.FullName -Raw -Encoding UTF8
    $originalContent = $content
    
    # ========== RUTAS ==========
    # Insumos
    $content = $content -replace "route\('insumos\.index'", "route('supplies.index'"
    $content = $content -replace "route\('insumos\.show'", "route('supplies.show'"
    $content = $content -replace "route\('insumos\.store'", "route('supplies.store'"
    $content = $content -replace "route\('insumos\.edit'", "route('supplies.edit'"
    $content = $content -replace "route\('insumos\.update'", "route('supplies.update'"
    $content = $content -replace "route\('insumos\.destroy'", "route('supplies.destroy'"
    $content = $content -replace "route\('insumos\.create'", "route('supplies.create'"
    
    # Proveedores
    $content = $content -replace "route\('proveedores\.index'", "route('suppliers.index'"
    $content = $content -replace "route\('proveedores\.show'", "route('suppliers.show'"
    $content = $content -replace "route\('proveedores\.store'", "route('suppliers.store'"
    $content = $content -replace "route\('proveedores\.edit'", "route('suppliers.edit'"
    $content = $content -replace "route\('proveedores\.update'", "route('suppliers.update'"
    $content = $content -replace "route\('proveedores\.destroy'", "route('suppliers.destroy'"
    $content = $content -replace "route\('proveedores\.create'", "route('suppliers.create'"
    
    # ========== ROUTEIS ==========
    $content = $content -replace "routeIs\('insumos\*'", "routeIs('supplies*'"
    $content = $content -replace "routeIs\('proveedores\*'", "routeIs('suppliers*'"
    
    # ========== VARIABLES DE COLECCIÓN ==========
    $content = $content -replace '\$insumos\b', '$supplies'
    $content = $content -replace '\$proveedores\b', '$suppliers'
    $content = $content -replace '@foreach\s*\(\s*\$insumos', '@foreach($supplies'
    $content = $content -replace '@foreach\s*\(\s*\$proveedores', '@foreach($suppliers'
    
    # ========== VARIABLES SINGULARES ==========
    # Necesitamos ser cuidadosos para no reemplazar dentro de strings
    $content = $content -replace '\$insumo->', '$supply->'
    $content = $content -replace '\$proveedor->', '$supplier->'
    $content = $content -replace '\(\$insumo\)', '($supply)'
    $content = $content -replace '\(\$proveedor\)', '($supplier)'
    $content = $content -replace 'as\s+\$insumo\)', 'as $supply)'
    $content = $content -replace 'as\s+\$proveedor\)', 'as $supplier)'
    
    # ========== PROPIEDADES ==========
    # IDs
    $content = $content -replace '->insumo_id\b', '->supply_id'
    $content = $content -replace '->proveedor_id\b', '->supplier_id'
    
    # Propiedades comunes
    $content = $content -replace '->nombre\b', '->name'
    $content = $content -replace '->stock_actual\b', '->current_stock'
    $content = $content -replace '->stock_minimo\b', '->minimum_stock'
    $content = $content -replace '->fecha_vencimiento\b', '->expiration_date'
    $content = $content -replace '->unidad_medida\b', '->unit_of_measure'
    $content = $content -replace '->cantidad\b', '->quantity'
    $content = $content -replace '->precio\b', '->price'
    $content = $content -replace '->telefono\b', '->phone'
    $content = $content -replace '->correo\b', '->email'
    $content = $content -replace '->direccion\b', '->address'
    $content = $content -replace '->total_compras\b', '->total_purchases'
    
    # Estado - usar accessor para mostrar en español
    $content = $content -replace '->estado\b', '->status_in_spanish'
    
    # Timestamps
    $content = $content -replace '->fecha_creacion\b', '->created_at'
    $content = $content -replace '->fecha_actualizacion\b', '->updated_at'
    
    # ========== NOMBRES DE INPUTS EN FORMULARIOS ==========
    # Mantener los nombres en español para compatibilidad con el mapeo del controlador
    # El controlador ya hace el mapeo de español a inglés
    
    # Si el contenido cambió, guardar el archivo
    if ($content -ne $originalContent) {
        Set-Content -Path $file.FullName -Value $content -Encoding UTF8 -NoNewline
        Write-Host "  ✓ Archivo actualizado" -ForegroundColor Green
    } else {
        Write-Host "  - Sin cambios" -ForegroundColor Gray
    }
}

Write-Host ""
Write-Host "=== Actualización Completada ===" -ForegroundColor Green
Write-Host "Total de archivos procesados: $totalFiles" -ForegroundColor Cyan
Write-Host ""
Write-Host "NOTA IMPORTANTE:" -ForegroundColor Yellow
Write-Host "- Las propiedades ahora usan nombres en inglés (->name, ->current_stock, etc.)" -ForegroundColor White
Write-Host "- Los estados usan ->status_in_spanish para mostrar en español al usuario" -ForegroundColor White
Write-Host "- Los nombres de inputs en formularios se mantienen en español" -ForegroundColor White
Write-Host "- El controlador hace el mapeo automático de español a inglés" -ForegroundColor White
Write-Host ""
Write-Host "Próximo paso: Limpiar caché de Laravel" -ForegroundColor Yellow
Write-Host "  php artisan view:clear" -ForegroundColor Cyan
Write-Host "  php artisan cache:clear" -ForegroundColor Cyan
