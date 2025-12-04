# 🧪 Guía Rápida de Prueba - Dashboard y Sidebar

## 1. Acceso a la Aplicación

### URL Base:
```
http://localhost:8000
```

## 2. Credenciales de Prueba

### Admin Global (Administrador Principal):
```
Email:    admin@gmail.com
Password: admin
```
**Esperado:** Redirige automáticamente a `/eventos`

### Gerente (Admin Local) - Opción 1:
```
Email:    gerente.puntamona@gmail.com
Password: admin
```
**Esperado:** Muestra el dashboard con estadísticas

### Gerente (Admin Local) - Opción 2:
```
Email:    gerente.sevichito@gmail.com
Password: admin
```
**Esperado:** Muestra el dashboard con estadísticas

## 3. Verificación del Sidebar

### Para Admin Global:
Después de login, debería ver:
- ✅ Sidebar con fondo verde oscuro (#232c0c)
- ✅ Marca "La Comarca" en el header
- ✅ Menú con opciones:
  - 📅 Eventos
  - 📦 Productos
  - 🚚 Proveedores
  - 🚪 Cerrar sesión
- ✅ Nombre del usuario en el footer del sidebar
- ✅ La página se redirige automáticamente a `/eventos`

### Para Gerente (Admin Local):
Después de login, debería ver:
- ✅ Mismo sidebar pero con opciones diferentes:
  - 🏠 Dashboard
  - 📦 Insumos
  - 🚚 Proveedores
  - 🚪 Cerrar sesión
- ✅ Dashboard con 4 tarjetas de estadísticas
  - Números animados
  - Iconos de Font Awesome
  - Colores degradados

## 4. Verificación del Dashboard

### Tarjetas de Estadísticas (deben animarse):
1. **Total Insumos** - 0 → 24 (color verde)
2. **Total Proveedores** - 0 → 8 (color azul)
3. **Stock Bajo** - 0 → 3 (color naranja)
4. **Valor Total** - ₡0 → ₡2,450,000 (color teal)

### Secciones Adicionales:
- **Acciones Rápidas:** 
  - Nuevo Insumo
  - Nuevo Proveedor
  - Ver Reportes
  - Exportar Datos
  
- **Actividad Reciente:**
  - Sistema iniciado
  - Dashboard cargado
  - BD conectada

- **Alertas:**
  - Stock Bajo
  - Estado del Sistema
  - Consejo del Día

## 5. Pruebas de Navegación

### Desde el Dashboard (Gerente):
- [ ] Click en "Insumos" → debe ir a `/insumos`
- [ ] Click en "Proveedores" → debe ir a `/proveedores`
- [ ] Click en "Dashboard" → debe volver a `/dashboard`
- [ ] Link activo debe estar resaltado

### Click en Botones de Acciones Rápidas:
- [ ] "Nuevo Insumo" → va a `/insumos`
- [ ] "Nuevo Proveedor" → va a `/suppliers`
- [ ] "Ver Reportes" → muestra notificación (coming soon)
- [ ] "Exportar Datos" → muestra notificación (coming soon)

### Cierre de Sesión:
- [ ] Click en "Cerrar sesión" → redirecciona a `/`
- [ ] Intenta acceder a `/dashboard` sin login → redirige a `/login`

## 6. Verificación Visual

### Colores esperados:
| Elemento | Color | Hex |
|----------|-------|-----|
| Sidebar | Verde oscuro | #232c0c |
| Iconos estadísticas primarias | Verde | #485a1a |
| Botones hover | Verde | #485a1a |
| Naranja acentos | Naranja | #ff9900 |

### Responsividad:
- [ ] Desktop (1200px+) - 2 columnas (sidebar + contenido)
- [ ] Tablet (768px-991px) - debe ajustarse
- [ ] Mobile (< 768px) - sidebar puede colapsar

## 7. Errores Comunes

### Si ves error "Undefined variable $slot":
❌ **NO debería pasar** - Ya está corregido
✅ El layout ahora usa `@yield('content')` en lugar de `{{ $slot }}`

### Si no ves los estilos:
```bash
# Asegúrate de que los assets están compilados
npm run build
```

### Si el sidebar no se ve:
- Verifica que `public/css/app.css` exista
- Verifica que `public/css/fixes.css` exista
- Abre DevTools (F12) → Console para ver errores

### Si los números no se animan:
- Abre DevTools (F12) → Console
- Verifica que no haya errores de JavaScript
- Los números deberían contar desde 0 hasta el valor final

## 8. Acciones Posteriores

Después de verificar que todo funciona:

1. **Crear nuevo usuario test:**
   ```sql
   -- Ejecutar en phpMyAdmin si es necesario
   -- Ver: NUEVO_USUARIO.md
   ```

2. **Verificar acceso por rol:**
   - Admin NO puede ver `/dashboard` (redirige a `/eventos`)
   - Gerente NO puede ver `/eventos` (403 Forbidden)

3. **Filtrado por local:**
   - ⏳ *Próxima fase:* Gerentes solo ven insumos de sus locales asignados

---

## 📝 Reporte de Problemas

Si encuentras algún problema, verifica:
1. Que hayas ejecutado `npm run build` recientemente
2. Que la base de datos esté accesible
3. Que el archivo `.env` esté configurado correctamente
4. La consola del navegador (F12) para mensajes de error

**¡Listo para probar!** 🚀
