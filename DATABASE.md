# Base de Datos - La Comarca Admin

## Información General
- **Base de datos**: `bdsage`
- **Motor**: MySQL/MariaDB
- **Codificación**: utf8mb4

## Estructura de Tablas

### tbinsumo
Tabla principal de insumos del restaurante.
- **Clave primaria**: `insumo_id` (auto incremento)
- **Sin timestamps**: Esta tabla NO incluye columnas `created_at` y `updated_at`

### tbproveedor
Tabla principal de proveedores del restaurante.
- **Clave primaria**: `proveedor_id` (auto incremento)  
- **Sin timestamps**: Esta tabla NO incluye columnas `created_at` y `updated_at`

### tbproveedor_insumo
Tabla pivote para la relación muchos a muchos entre proveedores e insumos.
- **Claves compuestas**: `proveedor_id`, `insumo_id`
- **Restricciones**: Foreign keys con CASCADE

## Configuración de Modelos Laravel

Los modelos `Insumo` y `Proveedor` tienen configurado:
```php
public $timestamps = false;
```

Esto es **IMPORTANTE** porque las tablas de la base de datos no incluyen las columnas `created_at` y `updated_at` que Laravel espera por defecto.

## Migraciones

Las migraciones han sido actualizadas para reflejar la estructura real de la base de datos, sin incluir `$table->timestamps()`.

## Datos de Prueba

La base de datos incluye datos de prueba:
- 10 insumos de ejemplo
- 10 proveedores de ejemplo  
- Relaciones configuradas en la tabla pivote

## Comandos Útiles

```bash
# Limpiar caché de configuración
php artisan config:clear

# Verificar conexión a la base de datos
php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';"

# Contar registros
php artisan tinker --execute="echo App\Models\Proveedor::count();"
```

## Notas Importantes

1. **No usar timestamps**: Los modelos están configurados sin timestamps para evitar errores SQL
2. **Estructura fija**: La estructura de la base de datos es fija y no debe modificarse sin actualizar los modelos
3. **Relaciones**: Usar siempre las relaciones definidas en los modelos para acceder a datos relacionados