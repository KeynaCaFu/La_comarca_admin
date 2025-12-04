# 🚀 GUÍA RÁPIDA DE PRUEBA - Sistema de Autenticación

## Paso 1: Iniciar el Servidor Laravel

```bash
cd "c:\xampp\htdocs\Proyectos 2025\La_comarca_admin"
php artisan serve
```

**Resultado esperado:**
```
INFO  Server running on [http://127.0.0.1:8000]
Press Ctrl+C to quit
```

---

## Paso 2: Abrir en el Navegador

Abre tu navegador en: **http://localhost:8000**

---

## Paso 3: Probar Login - Administrador Global

1. Haz clic en **"Iniciar Sesión"**
2. Ingresa:
   - **Email:** `admin@gmail.com`
   - **Contraseña:** `password`
3. Haz clic en **"Iniciar Sesión"**

**Resultado esperado:**
- ✅ Redirige automáticamente a `/eventos`
- ✅ Página de eventos (admin global)
- ✅ En la esquina superior derecha verás: `Admin Global`

---

## Paso 4: Probar Login - Gerente Local

1. Cierra sesión (parte superior derecha)
2. Haz clic en **"Iniciar Sesión"** nuevamente
3. Ingresa:
   - **Email:** `gerente.puntamona@gmail.com`
   - **Contraseña:** `password`
4. Haz clic en **"Iniciar Sesión"**

**Resultado esperado:**
- ✅ Redirige automáticamente a `/insumos`
- ✅ Página de insumos (solo gerente)
- ✅ En la esquina superior derecha verás: `Gerente`

---

## Paso 5: Probar Acceso Denegado

1. Siendo gerente, intenta acceder a:
   - `http://localhost:8000/eventos`
   - `http://localhost:8000/productos`

**Resultado esperado:**
- ❌ Error 403 - Forbidden
- ✅ Mensaje: "Solo administradores globales pueden acceder a esta sección."

---

## Paso 6: Verificar en Base de Datos

### Opción A: Usar phpMyAdmin
```
URL: http://localhost/phpmyadmin
Base de datos: bdsage_hourwonour
Tabla: tbuser
```

### Opción B: Usar Laravel Tinker
```bash
php artisan tinker
>>> App\Models\User::all()
>>> App\Models\Role::all()
>>> $user = App\Models\User::find(1);
>>> $user->role
>>> $user->locals
```

---

## 📋 Tabla de Pruebas

| Acción | Entrada | Resultado Esperado |
|--------|---------|-------------------|
| Login Admin | admin@gmail.com | ✅ Redirige a /eventos |
| Login Gerente 1 | gerente.puntamona@gmail.com | ✅ Redirige a /insumos |
| Login Gerente 2 | gerente.sevichito@gmail.com | ✅ Redirige a /insumos |
| Acceso /eventos como Gerente | Dirección URL directa | ❌ Error 403 |
| Acceso /productos como Gerente | Dirección URL directa | ❌ Error 403 |
| Logout | Botón en esquina superior | ✅ Redirije a / |

---

## 🔍 Verificaciones Importantes

### ✅ Verificar Modelos
```bash
php artisan tinker

# Ver usuario
>>> App\Models\User::find(1)

# Ver rol
>>> App\Models\Role::find(1)

# Ver relaciones
>>> $user = App\Models\User::find(1)
>>> $user->role
>>> $user->locals
```

### ✅ Verificar Middleware
```bash
# El middleware debe validar automáticamente
# Navega a /eventos como gerente y verifica que sea rechazado
```

### ✅ Verificar Sesión
```bash
# Después de login, verifica en:
# - Developer Tools > Application > Cookies
# - XSRF-TOKEN debe estar presente
# - laravel_session debe estar presente
```

---

## 🐛 Solución de Problemas

### ❌ Error: "No matching query results"
**Solución:**
```bash
php artisan db:seed
```

### ❌ Error: "Contraseña incorrecta"
**Solución:** Verifica que la contraseña sea `password` (exactamente)

### ❌ Error: "Usuario no encontrado"
**Solución:** Verifica el email en la tabla `tbuser`

### ❌ Page not found (404)
**Solución:** Asegúrate de usar `http://localhost:8000` (con puerto 8000)

### ❌ "Base de datos no conectada"
**Solución:**
1. Verifica XAMPP Apache y MySQL estén corriendo
2. Verifica `.env` tiene la DB correcta: `DB_DATABASE=bdsage_hourwonour`
3. Ejecuta: `php artisan config:clear`

---

## 📁 Archivos Clave de Referencia

| Archivo | Ubicación | Descripción |
|---------|-----------|-------------|
| Login Form | `/resources/views/auth/login.blade.php` | Vista de login personalizada |
| Modelo User | `/app/Models/User.php` | Modelo User mapeado a tbuser |
| Middleware Admin Global | `/app/Http/Middleware/IsAdminGlobal.php` | Valida admin global |
| Middleware Admin Local | `/app/Http/Middleware/IsAdminLocal.php` | Valida gerente |
| Seeder | `/database/seeders/AuthSeeder.php` | Datos iniciales |
| Rutas | `/routes/web.php` | Rutas protegidas |
| Documentación | `/AUTENTICACION.md` | Guía completa |

---

## ✨ Próximas Pruebas

Después de verificar que todo funciona:

1. **Crear nuevo usuario** en Tinker
2. **Modificar contraseña** de un usuario
3. **Deshabilitar usuario** (cambiar status a 'Inactive')
4. **Asignar diferentes locales** a gerentes
5. **Verificar logs** en `storage/logs/`

---

## 🎯 Conclusión

Si todas las pruebas pasan ✅, el sistema de autenticación está **100% funcional**:

- ✅ Usuarios pueden iniciar sesión
- ✅ Redirección automática según rol
- ✅ Acceso controlado por middleware
- ✅ Sesiones seguras
- ✅ CSRF protection activa

**¡Sistema listo para producción!** 🚀

---

**¿Necesitas ayuda?** Revisa `/AUTENTICACION.md` para documentación completa.
