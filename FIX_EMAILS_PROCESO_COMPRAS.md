# FIX: Correos no se enviaban en Proceso Inteligente de Compras

## Problema Identificado

El proceso inteligente `inteligente:procesar-compras` no estaba enviando notificaciones por email a los administradores cuando se generaban órdenes de compra automáticas.

## Causa Raíz

**Desajuste en el nombre del rol:**

El servicio `PlanificacionComprasService.php` buscaba usuarios con el rol `'admin'`:

```php
$administradores = \App\Models\User::whereHas('roles', function($q) {
    $q->where('name', 'admin');  // ❌ INCORRECTO
})->get();
```

Pero en la base de datos (usando Spatie Permission), el rol se llama `'administrador'`:

```
Roles disponibles:
- super_admin
- encargado
- administrador  ← Este es el correcto
- vendedor
- cliente
- proveedor
```

### Resultado del problema
- La colección `$administradores` estaba vacía
- El `foreach` no se ejecutaba
- No se enviaban correos a nadie

## Solución Aplicada

### 1. Corregido el servicio (PlanificacionComprasService.php)

**Línea 401:** Cambiado de `'admin'` a `'administrador'`

```php
$administradores = \App\Models\User::whereHas('roles', function($q) {
    $q->where('name', 'administrador');  // ✅ CORRECTO
})->get();
```

### 2. Actualizado el seeder (DatosPruebaEntregaSeeder.php)

**Problema secundario:** El seeder asignaba un campo `role` directo (sistema antiguo) en lugar de usar Spatie Permission.

**Solución:** Modificado para usar `assignRole()` correctamente:

```php
// ANTES (incorrecto)
User::firstOrCreate(
    ['email' => 'admin@test.com'],
    [
        'name' => 'Admin Test',
        'password' => Hash::make('password'),
        'role' => 'administrador',  // ❌ Campo directo (no funciona con Spatie)
    ]
);

// DESPUÉS (correcto)
$admin = User::firstOrCreate(
    ['email' => 'admin@test.com'],
    [
        'name' => 'Admin Test',
        'password' => Hash::make('password'),
    ]
);

if (!$admin->hasRole('administrador')) {
    $admin->assignRole('administrador');  // ✅ Usa Spatie Permission
}
```

## Verificación del Fix

### Script de verificación creado: `verificar_fix_roles.php`

Resultado:
```
✅ Búsqueda con 'administrador' (CORRECTO):
   ✓ Encontrados 1 administrador(es):
     - Administrador (admin@test.com)

❌ Búsqueda con 'admin' (INCORRECTO - antes del fix):
   ⚠️ NO se encuentran usuarios (por eso no se enviaban emails)
```

### Prueba del proceso

Comando ejecutado:
```bash
php artisan inteligente:procesar-compras
```

Resultado:
```
✅ Análisis completado:
- Insumos analizados: 13
- Insumos en nivel crítico: 13
- Órdenes de compra generadas: 12

📧 Notificaciones enviadas vía Mailtrap a los administradores.
```

### Verificación en Mailpit

- Mailpit ejecutándose en: http://127.0.0.1:8025
- Se recibieron 12 correos (uno por cada orden generada)
- Destinatario: admin@test.com

## Archivos Modificados

1. **app/Services/PlanificacionComprasService.php**
   - Línea 401: `'admin'` → `'administrador'`

2. **database/seeders/DatosPruebaEntregaSeeder.php**
   - Método `crearUsuarios()`: Usa `assignRole()` en lugar de campo `role`

3. **Archivos de verificación creados:**
   - `verificar_roles.php` - Diagnóstico general de roles
   - `verificar_fix_roles.php` - Verificación específica del fix

## Impacto

✅ **Resuelto completamente:**
- Los correos ahora se envían correctamente
- Los administradores reciben notificaciones de órdenes automáticas
- El proceso inteligente funciona end-to-end según especificación

## Recomendaciones

1. **Estandarizar nombres de roles:** Usar siempre `'administrador'` (no `'admin'`)
2. **Documentar roles disponibles:** Crear constantes o enum para evitar typos
3. **Testing:** Agregar test que verifique envío de notificaciones

## Demostración para Entrega

Para la demo en video, ejecutar:

```bash
# 1. Verificar que hay administradores
php verificar_fix_roles.php

# 2. Ejecutar proceso inteligente
php artisan inteligente:procesar-compras

# 3. Abrir Mailpit para mostrar emails recibidos
start http://127.0.0.1:8025
```

Esto demuestra:
- ✅ Análisis automático de stock crítico
- ✅ Generación automática de órdenes
- ✅ Notificación por email a administradores
- ✅ Sistema completo funcionando end-to-end

---

**Fix aplicado:** 5 de diciembre de 2025  
**Estado:** ✅ Resuelto y verificado
