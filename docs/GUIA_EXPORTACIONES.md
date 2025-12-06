# Guía de Uso: Exportaciones en Filament

## ✅ Sistema Configurado

Las exportaciones a Excel ya están funcionando correctamente en tu sistema. Los archivos se generan en segundo plano usando el sistema de colas de Laravel.

## 📋 Requisitos

Para que las exportaciones funcionen correctamente, **DEBES** tener el queue worker corriendo:

### Opción 1: Usar el script de desarrollo (RECOMENDADO)
```bash
composer dev
```
Este comando arranca automáticamente:
- Servidor web (`php artisan serve`)
- **Queue worker** (`php artisan queue:listen`)
- Logs en tiempo real (`php artisan pail`)
- Compilador de assets (`npm run dev`)

### Opción 2: Arrancar manualmente solo el queue worker
```bash
php artisan queue:work
```

## 🔄 Cómo Exportar

### 1. Desde la Lista de Registros
1. Navega a Pedidos, Órdenes de Compra o Productos
2. Click en el botón verde **"Exportar a Excel"** (arriba a la derecha)
3. Se abrirá un modal confirmando la exportación
4. Click en **"Exportar"**

### 2. ¿Qué Sucede Después?
- ✅ La exportación se registra en la base de datos
- ✅ Los datos se procesan en segundo plano (queue)
- ✅ Se genera un archivo Excel (.xlsx)
- ✅ Recibes una **notificación en Filament** cuando está listo
- ✅ La notificación incluye un **botón de descarga**

### 3. Descargar el Archivo
Cuando la exportación termina:
1. Verás una notificación en la esquina superior derecha
2. Click en la notificación
3. Click en el botón **"Descargar"**
4. El archivo se descargará automáticamente

## 📁 Ubicación de los Archivos

Los archivos exportados se guardan en:
```
storage/app/private/filament_exports/{export_id}/
```

Cada exportación contiene:
- `export-{id}-{nombre}.xlsx` - Archivo Excel final
- `0000000000000001.csv` - Datos en CSV
- `headers.csv` - Encabezados de columnas

## 🔍 Verificar Exportaciones

### Ver exportaciones en la base de datos:
```bash
php artisan tinker --execute="DB::table('exports')->get();"
```

### Ver trabajos pendientes en cola:
```bash
php artisan queue:work --once
```

### Procesar todos los trabajos pendientes:
```bash
php artisan queue:work --stop-when-empty
```

### Ver trabajos fallidos:
```bash
php artisan queue:failed
```

### Reintentar trabajos fallidos:
```bash
php artisan queue:retry all
```

## 📊 Exportaciones Disponibles

### 1. Exportación de Pedidos
**Columnas incluidas:**
- ID, Cliente (nombre, email, teléfono)
- Estado, Fecha de entrega
- Forma de entrega, Dirección
- Método de pago
- Total, Monto abonado, Saldo pendiente
- Vendedor, Observaciones
- Fechas de creación y actualización

### 2. Exportación de Órdenes de Compra
**Columnas incluidas:**
- ID, Proveedor (empresa, contacto, email, teléfono)
- Estado
- Fecha de emisión, Fecha entrega esperada
- Total
- Usuario creador, Fecha de creación

### 3. Exportación de Productos
**Columnas incluidas:**
- ID, Nombre, Descripción
- Categoría
- Precio base, Activo
- URL imagen, Tiempo de preparación
- Fechas de creación y actualización

## 🎯 Filtros y Exportación

**IMPORTANTE:** Los filtros que apliques en la tabla se respetan en la exportación.

Por ejemplo:
1. Filtrar pedidos por estado "Pendiente"
2. Filtrar por rango de fechas
3. Click en "Exportar a Excel"
4. **Solo se exportarán los pedidos que coincidan con esos filtros**

## 🐛 Solución de Problemas

### Problema: No aparece el botón de descarga
**Solución:** Asegúrate de que el queue worker esté corriendo (`composer dev`)

### Problema: La exportación se queda en "procesando"
**Solución:** 
```bash
php artisan queue:work --stop-when-empty
```

### Problema: No veo notificaciones
**Solución:** Verifica que la tabla `notifications` exista:
```bash
php artisan migrate
```

### Problema: Error "tabla exports no existe"
**Solución:** Ya fue resuelto. Las tablas necesarias ya están creadas.

## 📝 Notas Técnicas

- Las exportaciones usan el sistema de colas de Laravel (`QUEUE_CONNECTION=database`)
- Los archivos se generan en formato CSV y luego se convierten a Excel
- Las exportaciones grandes se procesan en bloques (chunks) para optimizar memoria
- El sistema usa `maatwebsite/excel` para la generación de archivos Excel

## ⚙️ Configuración en Producción

Cuando despliegues a producción:

1. **Usar un supervisor para el queue worker:**
```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

2. **Configurar un cronjob para procesar trabajos fallidos:**
```bash
php artisan schedule:work
```

3. **Opcional: Cambiar a Redis para mejor rendimiento:**
```env
QUEUE_CONNECTION=redis
```

## 🎉 ¡Todo Listo!

Tu sistema de exportaciones está completamente configurado y funcional. Solo recuerda mantener el queue worker corriendo con `composer dev`.
