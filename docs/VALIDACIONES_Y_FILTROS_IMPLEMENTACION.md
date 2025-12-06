# Implementación de Validaciones de Negocio y Filtros Avanzados

## Resumen de Implementación

Se han implementado exitosamente las siguientes funcionalidades de prioridad media:

---

## 1. Validaciones de Negocio

### 1.1 Middleware de Validación de Estados de Pedidos
**Archivo:** `app/Http/Middleware/ValidatePedidoStatusTransition.php`

**Funcionalidad:**
- Valida que los cambios de estado de pedidos sigan un flujo lógico
- Transiciones válidas definidas:
  - `pendiente` → `en_produccion` o `cancelado`
  - `en_produccion` → `listo` o `cancelado`
  - `listo` → `entregado` o `cancelado`
  - `entregado` → (estado final, sin transiciones)
  - `cancelado` → (estado final, sin transiciones)

**Uso:**
El middleware intercepta solicitudes PUT/PATCH y valida que el cambio de estado sea permitido antes de procesarlo.

---

### 1.2 Form Requests con Validaciones Personalizadas

#### StorePedidoRequest
**Archivo:** `app/Http/Requests/StorePedidoRequest.php`

**Validaciones incluidas:**
- ✅ **Fecha de entrega:** Mínimo 24 horas en el futuro
- ✅ **Días laborables:** No permite entregas los domingos
- ✅ **Dirección obligatoria:** Cuando la forma de entrega es "envío"
- ✅ **Validación de seña:** Mínimo 30% del total cuando el método es "seña"
- ✅ **Validación de pago total:** El monto debe ser igual al total
- ✅ **Límite de monto:** No puede exceder el total del pedido

#### UpdatePedidoRequest
**Archivo:** `app/Http/Requests/UpdatePedidoRequest.php`

**Validaciones adicionales:**
- ✅ **Validación de transiciones de estado** (integrada en el request)
- ✅ **Protección de pedidos entregados:** No permite cambiar fecha
- ✅ **Tiempo reducido:** 12 horas mínimas para pedidos en curso
- ✅ **Mismas validaciones de pago que el Store**

#### StoreOrdenDeCompraRequest
**Archivo:** `app/Http/Requests/StoreOrdenDeCompraRequest.php`

**Validaciones incluidas:**
- ✅ **Fecha de emisión:** No puede ser futura
- ✅ **Fecha de entrega:** Mínimo 2 días después de la emisión
- ✅ **Días laborables:** No permite entregas los domingos
- ✅ **Items obligatorios:** Mínimo 1 insumo en la orden
- ✅ **Validación de cantidades y precios**

---

### 1.3 Servicio de Validación de Agenda de Producción
**Archivo:** `app/Services/AgendaProduccionService.php`

**Métodos implementados:**

#### `validarCapacidadProduccion()`
Valida si se puede agendar un pedido en una fecha específica considerando:
- Capacidad máxima de 15 pedidos por día
- No permite producción los domingos
- Alerta cuando quedan ≤3 espacios disponibles

#### `obtenerFechasDisponibles()`
Retorna un array de fechas con capacidad disponible en los próximos 14 días.

#### `obtenerEstadisticasAgenda()`
Genera estadísticas de ocupación para un rango de fechas:
- Total de pedidos por día
- Capacidad disponible
- Porcentaje de ocupación
- Estado: bajo (0-49%), medio (50-79%), alto (80-99%), completo (100%)

#### `validarTiempoAnticipacion()`
Valida tiempo mínimo de anticipación según tipo de pedido:
- Simple: 12 horas
- Normal: 24 horas
- Complejo: 48 horas

---

## 2. Filtros Avanzados en Filament

### 2.1 Filtros en PedidoResource
**Archivo:** `app/Filament/Admin/Resources/PedidoResource.php`

**Filtros agregados:**
- ✅ **Estado (múltiple):** Permite seleccionar varios estados a la vez
- ✅ **Forma de entrega:** Retiro o Envío
- ✅ **Método de pago:** Total o Seña
- ✅ **Rango de fechas de entrega:** Desde/Hasta con indicadores
- ✅ **Rango de montos:** Mínimo/Máximo con indicadores
- ✅ **Con saldo pendiente:** Toggle para filtrar pedidos con deuda
- ✅ **Cliente:** Búsqueda por cliente
- ✅ **Vendedor:** Búsqueda por vendedor

### 2.2 Filtros en OrdenDeCompraResource
**Archivo:** `app/Filament/Admin/Resources/OrdenDeCompraResource.php`

**Filtros agregados:**
- ✅ **Estado (múltiple):** Todos los estados de órdenes
- ✅ **Proveedor:** Búsqueda por proveedor
- ✅ **Rango de fechas de emisión:** Con indicadores
- ✅ **Rango de fechas de entrega esperada:** Con indicadores
- ✅ **Rango de montos totales:** Mínimo/Máximo con indicadores
- ✅ **Usuario creador:** Filtro por quien creó la orden

**Características de los filtros:**
- Indicadores visuales que muestran los filtros activos
- Formato de fechas en español (d/m/Y)
- Formato de montos con símbolo $ y separadores de miles

---

## 3. Exportación a Excel

### 3.1 Exporters Creados

#### PedidoExporter
**Archivo:** `app/Filament/Exports/PedidoExporter.php`

**Columnas exportadas:**
- ID, Cliente (nombre, email, teléfono)
- Estado, Fecha de entrega, Forma de entrega
- Dirección de envío, Método de pago
- Total, Monto abonado, Saldo pendiente
- Vendedor, Observaciones
- Fechas de creación y actualización

#### OrdenDeCompraExporter
**Archivo:** `app/Filament/Exports/OrdenDeCompraExporter.php`

**Columnas exportadas:**
- ID, Proveedor (empresa, contacto, email, teléfono)
- Estado, Fecha de emisión, Fecha entrega esperada
- Total, Usuario creador, Fecha de creación

#### ProductoExporter
**Archivo:** `app/Filament/Exports/ProductoExporter.php`

**Columnas exportadas:**
- ID, Nombre, Descripción, Categoría
- Precio base, Activo (Sí/No)
- URL imagen, Tiempo de preparación
- Fechas de creación y actualización

### 3.2 Integración en Filament

**Botones de exportación agregados en:**
- `ListPedidos.php` - Exportar lista de pedidos
- `ListOrdenDeCompras.php` - Exportar lista de órdenes
- `ListProductos.php` - Exportar lista de productos

**Características:**
- Botón verde con ícono de descarga
- Procesa filtros activos
- Notificación de progreso
- Descarga automática del archivo Excel

---

## 4. Exportación a PDF

### 4.1 Servicio de Generación de PDFs
**Archivo:** `app/Services/PdfReportService.php`

**Métodos implementados:**

#### `generarPdfPedido(Pedido $pedido)`
Genera PDF individual de un pedido con:
- Información del cliente
- Detalles del pedido
- Items con variantes
- Información de pago
- Historial de pagos
- Observaciones

#### `generarPdfOrdenCompra(OrdenDeCompra $orden)`
Genera PDF individual de una orden de compra con:
- Información del proveedor
- Detalles de la orden
- Items con cantidades y precios
- Total calculado

#### `generarReportePedidos(array $filtros)`
Genera reporte consolidado de múltiples pedidos con:
- Filtros aplicables (fecha, estado, cliente)
- Totales generales: Total, Abonado, Pendiente
- Lista completa de pedidos

#### `generarReporteOrdenesCompra(array $filtros)`
Genera reporte consolidado de órdenes con:
- Filtros aplicables (fecha, estado, proveedor)
- Total general
- Lista de órdenes

#### `generarReporteVentas(Carbon $inicio, Carbon $fin)`
Genera reporte de ventas por período con:
- Estadísticas: Total ventas, Total cobrado, Cantidad de pedidos
- Ticket promedio
- Top 10 productos más vendidos
- Análisis por cantidad y monto

### 4.2 Vistas PDF Creadas

#### pedido.blade.php
**Archivo:** `resources/views/pdf/pedido.blade.php`

**Diseño:**
- Header con logo y branding "ContuCocina Pastelería"
- Secciones organizadas con colores naranja pastel (#ff8c42)
- Badges de estado con colores distintivos
- Tablas estilizadas para items y pagos
- Footer con fecha de generación

#### orden-compra.blade.php
**Archivo:** `resources/views/pdf/orden-compra.blade.php`

**Diseño:**
- Similar al de pedido, adaptado a órdenes de compra
- Información del proveedor destacada
- Items con unidades de medida
- Diseño profesional para documentos comerciales

### 4.3 Integración en Filament

**Botones PDF agregados en:**
- **PedidoResource:** Acción "PDF" en cada fila de la tabla
- **OrdenDeCompraResource:** Acción "PDF" en cada fila

**Características:**
- Botón azul info con ícono de documento
- Genera y descarga PDF inmediatamente
- Nombres de archivo descriptivos con ID

---

## 5. Cómo Usar las Nuevas Funcionalidades

### Validaciones Automáticas
Las validaciones se ejecutan automáticamente al crear o editar pedidos y órdenes. No requiere configuración adicional.

### Filtros en Filament
1. Navegar a Pedidos u Órdenes de Compra
2. Usar el panel de filtros en la parte superior de la tabla
3. Los filtros se aplican en tiempo real
4. Los indicadores muestran los filtros activos

### Exportar a Excel
1. En la lista de Pedidos/Órdenes/Productos
2. Click en el botón verde "Exportar a Excel"
3. El sistema procesa los registros (respeta filtros activos)
4. Descarga automática del archivo

### Generar PDF Individual
1. En la lista, click en el botón "PDF" de la fila deseada
2. Se genera y descarga el PDF automáticamente

### Reportes Consolidados (Para desarrollar en el futuro)
El servicio `PdfReportService` incluye métodos para reportes consolidados que pueden ser invocados desde:
- Comandos de consola
- Controladores personalizados
- Tareas programadas

---

## 6. Configuración Requerida

### Dependencias Ya Instaladas
- ✅ `maatwebsite/excel` - Para exportación a Excel
- ✅ `barryvdh/laravel-dompdf` - Para generación de PDFs

### Variables de Entorno
No se requieren cambios en `.env` para estas funcionalidades.

### Permisos
Asegurarse de que el directorio `storage/app/public` tenga permisos de escritura para las exportaciones.

---

## 7. Próximas Mejoras Sugeridas

### Validaciones
- [ ] Agregar validación de stock antes de aprobar pedidos
- [ ] Validar conflictos en la agenda de producción en tiempo real
- [ ] Integrar validaciones de agenda en el formulario de creación

### Filtros
- [ ] Agregar filtros guardados (favoritos)
- [ ] Exportar con filtros aplicados
- [ ] Filtros por rango de stock en productos

### Reportes
- [ ] Dashboard con gráficos de ventas
- [ ] Reporte de productos más vendidos
- [ ] Análisis de proveedores
- [ ] Alertas automáticas de baja capacidad
- [ ] Envío de reportes por email automáticamente

---

## 8. Testing Sugerido

### Tests Unitarios
```php
// Validar transiciones de estado
test_pedido_puede_cambiar_de_pendiente_a_en_produccion()
test_pedido_no_puede_cambiar_de_entregado_a_pendiente()

// Validar agenda
test_no_permite_pedidos_domingos()
test_alerta_cuando_capacidad_baja()
```

### Tests de Integración
```php
// Exportación
test_exportar_pedidos_a_excel()
test_generar_pdf_pedido()

// Filtros
test_filtrar_pedidos_por_rango_fechas()
test_filtrar_con_multiples_estados()
```

---

## 9. Notas Técnicas

### Capacidad de Producción
El límite de 15 pedidos por día está definido en:
```php
AgendaProduccionService::CAPACIDAD_MAXIMA_PEDIDOS_DIA
```
Este valor puede ajustarse según necesidades del negocio.

### Tiempo de Anticipación
Los tiempos mínimos están en:
```php
StorePedidoRequest - 24 horas
UpdatePedidoRequest - 12 horas (pedidos en curso)
```

### Días No Laborables
Actualmente solo los domingos están bloqueados. Se puede extender para feriados.

---

## Autor
Implementación realizada el 5 de diciembre de 2025
Sistema de Gestión de Pastelería - ContuCocina
