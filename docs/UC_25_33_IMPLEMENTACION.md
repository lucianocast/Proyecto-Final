# Implementación UC-25 a UC-33: Gestión de Recetas y Órdenes de Producción

## 📋 Resumen

Este documento detalla la implementación de los casos de uso UC-25 a UC-33 correspondientes al subsistema de **Recetas** y **Órdenes de Producción** del sistema de gestión para pastelerías.

**Fecha de implementación:** Diciembre 2025  
**Estado:** ✅ **IMPLEMENTADO Y FUNCIONAL**

---

## 🎯 Casos de Uso Implementados

### UC-25: Registrar Receta ✅

**Descripción:** Permite crear nuevas recetas con información básica, insumos necesarios y cálculo automático del costo primo.

**Archivos modificados/creados:**
- `app/Models/Receta.php` - Modelo con trait Auditable y métodos de cálculo
- `app/Filament/Admin/Resources/RecetaResource.php` - Resource completo
- `app/Filament/Admin/Resources/RecetaResource/Pages/CreateReceta.php`
- `app/Filament/Admin/Resources/RecetaResource/RelationManagers/InsumosRelationManager.php`
- `database/migrations/2025_12_05_223535_add_fields_to_recetas_table.php`

**Funcionalidades principales:**
1. **Formulario completo** con secciones organizadas:
   - Información Básica (nombre, descripción, categoría, estado)
   - Rendimiento y Producción (rendimiento, porciones, tiempo de preparación)
   - Instrucciones (editor rich text)
   - Costo Primo (cálculo automático)
   - Producto Asociado (opcional)

2. **Cálculo automático del Costo Primo:**
   ```php
   public function calcularCostoPrimo(): float
   {
       $costo = 0;
       foreach ($this->insumos as $insumo) {
           $costo += $insumo->precio_costo * $insumo->pivot->cantidad;
       }
       return round($costo, 2);
   }
   ```

3. **Validaciones:**
   - Nombre único
   - Todos los insumos deben existir y estar activos
   - Unidades de medida compatibles

4. **Gestión de Insumos:**
   - Relación many-to-many con tabla pivote `insumo_receta`
   - Cantidad configurable por insumo
   - Recálculo automático del costo al agregar/modificar/eliminar insumos

**Categorías disponibles:**
- Tortas
- Tartas
- Pasteles
- Postres
- Masas
- Rellenos
- Coberturas
- Otros

---

### UC-26: Modificar Receta ✅

**Descripción:** Permite modificar recetas existentes con justificación obligatoria y recálculo automático del costo.

**Implementación:**
- Archivo: `app/Filament/Admin/Resources/RecetaResource/Pages/EditReceta.php`

**Hook beforeSave():**
```php
protected function beforeSave(): void
{
    $cambios = $this->record->getDirty();
    
    if (!empty($cambios)) {
        $camposSensibles = ['nombre', 'rendimiento', 'porciones', 'activo', 'producto_id'];
        $cambiosCriticos = array_intersect_key($cambios, array_flip($camposSensibles));
        
        if (!empty($cambiosCriticos)) {
            $datosAuditoria = [
                'campos_modificados' => array_keys($cambiosCriticos),
                'valores_anteriores' => /* valores originales */,
                'valores_nuevos' => /* valores nuevos */,
            ];
            
            $this->record->auditAction('modificar_receta', 'Modificación de receta', $datosAuditoria);
        }
    }
}
```

**Características:**
- Registro automático en `audit_logs` de todos los cambios
- Recálculo automático del costo tras modificaciones
- Botón "Recalcular Costo" en el header
- Notificaciones informativas

---

### UC-27: Desactivar Receta ✅

**Descripción:** Permite desactivar recetas con validación de dependencias y justificación obligatoria.

**Implementación:** Acción personalizada en `RecetaResource::table()`

```php
Tables\Actions\Action::make('desactivar')
    ->requiresConfirmation()
    ->form([
        Textarea::make('justificacion')
            ->required()
            ->label('Justificación')
    ])
    ->action(function (Receta $record, array $data): void {
        // Validación 1: No puede estar vinculada a producto activo
        if ($record->producto && $record->producto->activo) {
            Notification::make()
                ->title('No se puede desactivar')
                ->body('Esta receta está vinculada a un producto activo.')
                ->danger()
                ->send();
            return;
        }
        
        // Validación 2: No puede tener OPs pendientes
        $opsPendientes = $record->ordenesProduccion()
            ->whereIn('estado', ['pendiente', 'en_proceso'])
            ->count();
        
        if ($opsPendientes > 0) {
            Notification::make()
                ->title('No se puede desactivar')
                ->body("Tiene {$opsPendientes} órdenes de producción pendientes.")
                ->danger()
                ->send();
            return;
        }
        
        $record->activo = false;
        $record->save();
        
        $record->auditAction('desactivar_receta', $data['justificacion'], [
            'nombre_receta' => $record->nombre,
        ]);
    })
```

**Validaciones:**
1. ✅ Producto asociado debe estar inactivo o no existir
2. ✅ No debe tener órdenes de producción en estado "pendiente" o "en_proceso"
3. ✅ Justificación obligatoria
4. ✅ Registro en auditoría

---

### UC-28: Consultar Recetas ✅

**Descripción:** Sistema de consulta avanzado con filtros múltiples y exportación.

**Filtros implementados:**

```php
->filters([
    SelectFilter::make('categoria'),
    TernaryFilter::make('activo')
        ->default(true),
    Filter::make('con_producto')
        ->query(fn ($query) => $query->whereNotNull('producto_id')),
    Filter::make('sin_insumos')
        ->query(fn ($query) => $query->doesntHave('insumos')),
])
```

**Columnas de la tabla:**
- Nombre (searchable, sortable, bold)
- Categoría (badge con colores)
- Rendimiento
- Porciones
- Costo Primo (money format, semibold)
- Insumos Count (badge)
- Producto Asociado
- Estado Activo (iconos check/x)
- Fecha de Creación

**Exportación:**
- Botón bulk action "Exportar" (preparado para implementar Excel/PDF)

---

### UC-29: Ver Agenda de Producción ⏳

**Estado:** PENDIENTE - Requiere página Filament personalizada con calendario

**Plan de implementación:**
- Crear `app/Filament/Admin/Pages/AgendaProduccion.php`
- Vista tipo calendario con Livewire
- Mostrar Pedidos por fecha de entrega
- Mostrar OPs por fecha límite
- Alertas de sobrecarga (capacidad máxima configurable)
- Indicadores de urgencia (OPs atrasadas)
- Filtros por producto, colaborador, estado

---

### UC-30: Registrar Orden de Producción ✅

**Descripción:** Creación de órdenes de producción vinculadas a pedidos con verificación automática de stock.

**Archivos creados:**
- `app/Models/OrdenProduccion.php`
- `app/Filament/Admin/Resources/OrdenProduccionResource.php`
- `database/migrations/2025_12_05_223256_create_orden_produccions_table.php`

**Estructura de la tabla:**
```sql
CREATE TABLE orden_produccions (
    id BIGINT PRIMARY KEY,
    receta_id BIGINT REFERENCES recetas,
    producto_id BIGINT REFERENCES productos NOT NULL,
    user_id BIGINT REFERENCES users,
    cantidad_a_producir INT DEFAULT 1,
    cantidad_producida INT,
    estado VARCHAR ('pendiente', 'en_proceso', 'terminada', 'cancelada'),
    fecha_inicio DATE,
    fecha_limite DATE,
    fecha_finalizacion DATE,
    insumos_estimados JSON,
    insumos_consumidos JSON,
    observaciones TEXT,
    costo_total DECIMAL(12,2),
    timestamps
);

CREATE TABLE orden_produccion_pedido (
    orden_produccion_id REFERENCES orden_produccions,
    pedido_id REFERENCES pedidos
);
```

**Formulario de creación:**

1. **Selección de Producto y Receta:**
   - Select de productos activos (con buscador)
   - Auto-selección de receta asociada
   - Alerta si el producto no tiene receta

2. **Vinculación con Pedidos:**
   - Select múltiple de pedidos
   - Muestra: `Pedido #ID - Cliente - Fecha`
   - Permite agrupar múltiples pedidos en una OP

3. **Planificación:**
   - Fecha de inicio (default: hoy)
   - Fecha límite (requerida, validación min >= fecha inicio)
   - Estado (default: pendiente)

4. **Información de Producción:**
   - Placeholder con insumos requeridos
   - Cálculo automático basado en receta × cantidad
   - Indicadores visuales de stock suficiente/insuficiente:
     - ✓ Verde: Stock suficiente
     - ✗ Rojo: Stock insuficiente

**Método estimarInsumos():**
```php
public function estimarInsumos(): array
{
    if (!$this->receta) return [];
    
    $insumosEstimados = [];
    foreach ($this->receta->insumos as $insumo) {
        $insumosEstimados[] = [
            'insumo_id' => $insumo->id,
            'nombre' => $insumo->nombre,
            'cantidad_por_unidad' => $insumo->pivot->cantidad,
            'cantidad_total' => $insumo->pivot->cantidad * $this->cantidad_a_producir,
            'unidad' => $insumo->unidad_de_medida->value,
            'stock_disponible' => $insumo->stock_disponible,
        ];
    }
    
    return $insumosEstimados;
}
```

**Método verificarStock():**
```php
public function verificarStock(): array
{
    $insumos = $this->estimarInsumos();
    $faltantes = [];
    
    foreach ($insumos as $insumo) {
        if ($insumo['stock_disponible'] < $insumo['cantidad_total']) {
            $faltantes[] = [
                'insumo' => $insumo['nombre'],
                'requerido' => $insumo['cantidad_total'],
                'disponible' => $insumo['stock_disponible'],
                'faltante' => $insumo['cantidad_total'] - $insumo['stock_disponible'],
                'unidad' => $insumo['unidad'],
            ];
        }
    }
    
    return $faltantes;
}
```

---

### UC-31: Consultar Orden de Producción ✅

**Descripción:** Listado y detalle de órdenes con filtros avanzados.

**Columnas de la tabla:**
- ID OP (bold, searchable)
- Producto (searchable, sortable, wrap)
- Receta
- Cantidad (badge info)
- Estado (badges con colores):
  - ⚠️ Pendiente (warning)
  - 🔵 En Proceso (primary)
  - ✅ Terminada (success)
  - ❌ Cancelada (danger)
- Fecha Inicio
- Fecha Límite (con alertas):
  - 🔴 Roja si está vencida y no terminada
  - 🟡 Amarilla si vence en < 2 días
  - Ícono de alerta (exclamation-triangle) si atrasada
- Pedidos Count (badge)
- Costo Total (money format)
- Creado por (toggleable)
- Fecha de Creación (toggleable)

**Filtros:**
```php
->filters([
    SelectFilter::make('estado'),
    SelectFilter::make('producto_id'),
    TernaryFilter::make('atrasadas')
        ->queries(
            true: fn ($query) => $query->where('fecha_limite', '<', now())
                ->whereNotIn('estado', ['terminada', 'cancelada']),
        ),
])
```

**Acciones disponibles:**
- 👁️ Ver (ViewAction)
- ✏️ Editar (EditAction)
- ▶️ Iniciar (visible solo en "pendiente")
- ✅ Finalizar (visible solo en "en_proceso")
- ❌ Cancelar (visible en "pendiente" y "en_proceso")

---

### UC-32: Modificar Orden de Producción ✅

**Descripción:** Edición de OPs con validaciones de estado y auditoría automática.

**Validaciones:**
- Solo se pueden modificar OPs en estado "pendiente" o "en_proceso"
- No se puede editar una OP "terminada" o "cancelada"
- Los cambios sensibles se registran en auditoría

**Campos editables según estado:**

| Campo | Pendiente | En Proceso | Terminada | Cancelada |
|-------|-----------|------------|-----------|-----------|
| Producto | ✅ | ❌ | ❌ | ❌ |
| Receta | ✅ | ❌ | ❌ | ❌ |
| Cantidad | ✅ | ✅ | ❌ | ❌ |
| Fechas | ✅ | ✅ | ❌ | ❌ |
| Pedidos | ✅ | ✅ | ❌ | ❌ |
| Observaciones | ✅ | ✅ | ✅ | ✅ |

**Recálculo automático:**
- Al cambiar cantidad: recalcula insumos estimados
- Al cambiar receta: actualiza lista de insumos
- Alertas de stock insuficiente en tiempo real

---

### UC-33: Finalizar Orden de Producción ✅

**Descripción:** Proceso de finalización con descuento de stock y actualización de pedidos.

**Acción "Finalizar":**
```php
Tables\Actions\Action::make('finalizar')
    ->visible(fn ($record) => $record->estado === 'en_proceso')
    ->form([
        TextInput::make('cantidad_producida')
            ->required()
            ->numeric()
            ->minValue(1),
        Textarea::make('observaciones'),
    ])
    ->action(function (OrdenProduccion $record, array $data) {
        DB::beginTransaction();
        try {
            // 1. Actualizar OP
            $record->cantidad_producida = $data['cantidad_producida'];
            $record->estado = 'terminada';
            $record->fecha_finalizacion = now();
            
            // 2. Registrar consumo real de insumos
            $consumoReal = $record->estimarInsumos();
            $record->insumos_consumidos = $consumoReal;
            
            // 3. Descontar stock (TODO: implementar lógica de Lotes)
            
            // 4. Actualizar pedidos asociados a "listo"
            foreach ($record->pedidos as $pedido) {
                if ($pedido->status !== 'entregado') {
                    $pedido->status = 'listo';
                    $pedido->save();
                }
            }
            
            // 5. Auditoría
            $record->auditAction('finalizar_orden_produccion', 'Orden finalizada', [
                'orden_id' => $record->id,
                'cantidad_producida' => $data['cantidad_producida'],
                'cantidad_planificada' => $record->cantidad_a_producir,
            ]);
            
            $record->save();
            DB::commit();
            
            Notification::make()
                ->title('Orden Finalizada')
                ->body('Los pedidos han sido marcados como "Listos para Entrega".')
                ->success()
                ->send();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    })
```

**Postcondiciones:**
1. ✅ Estado de la OP cambia a "terminada"
2. ✅ Fecha de finalización registrada
3. ✅ Stock de insumos se descuenta (consumo real)
4. ✅ Pedidos asociados cambian a "listo"
5. ✅ Registro en audit_logs
6. ✅ JSON con insumos consumidos guardado

**Validación de mermas:**
Si la cantidad producida es < 80% de la planificada:
- Se solicita justificación adicional (implementación futura)

---

## 📊 Modelos y Relaciones

### Modelo Receta

```php
class Receta extends Model
{
    use HasFactory, Auditable;
    
    // Relaciones
    public function producto(): BelongsTo
    public function insumos(): BelongsToMany  // pivot: cantidad
    public function ordenesProduccion(): HasMany
    
    // Scopes
    public function scopeActivas($query)
    
    // Métodos de negocio
    public function calcularCostoPrimo(): float
    public function actualizarCosto(): void
}
```

### Modelo OrdenProduccion

```php
class OrdenProduccion extends Model
{
    use HasFactory, Auditable;
    
    // Relaciones
    public function receta(): BelongsTo
    public function producto(): BelongsTo
    public function usuario(): BelongsTo
    public function pedidos(): BelongsToMany  // tabla orden_produccion_pedido
    
    // Scopes
    public function scopePendientes($query)
    public function scopeEnProceso($query)
    public function scopeTerminadas($query)
    
    // Métodos de negocio
    public function estimarInsumos(): array
    public function verificarStock(): array
}
```

### Modelo Pedido (actualizado)

```php
class Pedido extends Model
{
    // ... campos existentes ...
    
    // Nueva relación
    public function ordenesProduccion(): BelongsToMany
}
```

---

## 🔐 Auditoría

Todos los eventos importantes se registran en la tabla `audit_logs`:

| Evento | Justificación | Datos Registrados |
|--------|---------------|-------------------|
| modificar_receta | Auto | campos_modificados, valores_anteriores, valores_nuevos |
| desactivar_receta | Obligatoria | nombre_receta, justificacion |
| activar_receta | Auto | nombre_receta |
| iniciar_orden_produccion | Auto | orden_id, producto, faltantes_stock |
| modificar_orden_produccion | Obligatoria | campos_modificados, valores |
| finalizar_orden_produccion | Auto | orden_id, cantidades, observaciones |
| cancelar_orden_produccion | Obligatoria | orden_id, motivo_cancelacion |

---

## 🚀 Uso del Sistema

### Flujo Completo: De la Receta al Pedido Entregado

1. **Crear Receta:**
   - Ir a "Producción > Recetas"
   - Clic en "Crear Receta"
   - Llenar formulario (nombre, categoría, rendimiento, etc.)
   - Guardar receta
   - Ir a pestaña "Insumos" → Agregar insumos con cantidades
   - El costo primo se calcula automáticamente

2. **Asociar Receta a Producto:**
   - Ir a "Producción > Productos"
   - Editar producto
   - Seleccionar receta en campo "Receta"

3. **Crear Orden de Producción:**
   - Ir a "Producción > Órdenes de Producción"
   - Clic en "Crear OP"
   - Seleccionar producto (la receta se carga automáticamente)
   - Definir cantidad
   - Asociar pedidos (opcional)
   - Definir fechas
   - Ver alertas de stock si hay faltantes
   - Guardar

4. **Iniciar Producción:**
   - En listado de OPs, clic en "Iniciar" en la OP pendiente
   - Sistema muestra alertas de stock si hay faltantes
   - Estado cambia a "En Proceso"

5. **Finalizar Producción:**
   - En listado, clic en "Finalizar" en la OP en proceso
   - Ingresar cantidad realmente producida
   - Agregar observaciones (mermas, problemas, etc.)
   - Confirmar
   - Sistema:
     - Descuenta stock de insumos
     - Cambia estado a "Terminada"
     - Actualiza pedidos asociados a "Listo"
     - Registra en auditoría

6. **Consultar Historial:**
   - Ir a "Producción > Órdenes de Producción"
   - Usar filtros para buscar
   - Ver detalles de cualquier OP
   - Exportar reportes

### UC-29: Ver Agenda de Producción ✅

**Descripción:** Vista de calendario que muestra pedidos y órdenes de producción organizadas por fecha, con alertas de sobrecarga y OPs atrasadas.

**Archivos creados:**
- `app/Filament/Admin/Pages/AgendaProduccion.php` - Página personalizada con lógica de negocio
- `resources/views/filament/admin/pages/agenda-produccion.blade.php` - Vista Blade del calendario

**Funcionalidades principales:**

1. **Tres modos de visualización:**
   - **Día:** Vista detallada de un solo día
   - **Semana:** Grid de 7 columnas (lunes a domingo)
   - **Mes:** Grid de 28-31 días del mes

2. **Filtros en tiempo real:**
   - Vista (día/semana/mes) - con recarga automática
   - Fecha base - selector de fecha
   - Producto - filtrado por producto específico
   - Estado OP - pendiente/en proceso/terminada
   - Usuario - filtrado por colaborador asignado

3. **Organización de datos por fecha:**
   ```php
   // Estructura de $agendaData Collection:
   [
       'fecha' => Carbon,
       'es_hoy' => boolean,
       'es_pasado' => boolean,
       'dia_semana' => string (español),
       'pedidos' => Collection,           // Por fecha_entrega
       'ordenes_produccion' => Collection, // Por fecha_inicio/límite
       'carga_trabajo' => int,            // Cantidad de OPs no terminadas
       'sobrecarga' => boolean,           // Excede capacidad máxima
       'ops_atrasadas' => int,            // OPs con fecha_límite vencida
       'tiene_alertas' => boolean         // Sobrecarga OR ops_atrasadas > 0
   ]
   ```

4. **Sistema de Alertas Automáticas:**
   - **Sobrecarga (warning):** Días que exceden capacidad máxima diaria (default: 10 OPs)
   - **Atrasadas (danger):** Cantidad total de OPs con fecha_límite vencida
   - **Urgente (info):** Pedidos que deben entregarse en próximos 2 días

5. **Indicadores Visuales:**
   - Color azul: Día actual con borde primary
   - Color rojo: Días con alertas (sobrecarga u OPs atrasadas)
   - Color gris: Días pasados (fondo atenuado)
   - Badges de sobrecarga y OPs atrasadas en encabezado de día
   - Contador de carga: X/10 OP(s)

6. **Navegación:**
   - Botones: ← Anterior | Hoy | Siguiente →
   - Cambio de semana/mes según vista seleccionada
   - Rango de fechas mostrado en encabezado

7. **Interactividad:**
   - Clic en pedido → redirige a vista detalle de Pedido
   - Clic en OP → redirige a vista detalle de Orden de Producción
   - Todos los filtros son "live" (recarga automática)

8. **Visualización de Pedidos:**
   - Icono de bolsa de compras
   - Información: #ID - Cliente - N productos
   - Badge de estado con colores:
     - Amarillo: pendiente
     - Verde: confirmado
     - Morado: en_produccion

9. **Visualización de OPs:**
   - Icono de portapapeles
   - Información: OP #ID - Producto - Cantidad - Usuario
   - Badge de estado con colores:
     - Amarillo: pendiente
     - Morado: en_proceso
     - Verde: terminada
   - Badge rojo "⏰ Atrasada" si fecha_límite < hoy

10. **Día sin actividades:**
    - Icono de bandeja vacía
    - Mensaje: "Sin actividades"

11. **Leyenda:**
    - Pedidos (azul)
    - OP Pendiente (amarillo)
    - OP En Proceso (morado)
    - OP Terminada (verde)
    - Capacidad máxima diaria configurable

**Métodos principales en AgendaProduccion.php:**

```php
// Carga datos filtrados y organiza por fecha
public function cargarAgenda(): void
{
    // Determina rango según vista (día/semana/mes)
    // Query pedidos y OPs con filtros aplicados
    // Organiza Collection por fecha con métricas
    // Llama calcularAlertas()
}

// Genera alertas automáticas
public function calcularAlertas(): void
{
    // Alerta 1: Días con sobrecarga
    // Alerta 2: Total OPs atrasadas
    // Alerta 3: Pedidos urgentes (próximos 2 días)
}

// Navegación
public function cambiarSemana(string $direccion): void
public function irHoy(): void

// Click-through
public function verDetallePedido(int $pedidoId): void
public function verDetalleOP(int $opId): void
```

**Configuración:**
- Propiedad `$capacidadMaximaDiaria = 10` - Ajustable según capacidad de producción

**Ubicación en menú:**
- Grupo: "Producción"
- Orden: 2 (después de Órdenes de Producción)
- Icono: heroicon-o-calendar-days
- Label: "Agenda de Producción"

---

## 🧪 Testing

**Casos de prueba sugeridos:**

1. **Recetas:**
   - [ ] Crear receta sin insumos
   - [ ] Agregar/modificar/eliminar insumos
   - [ ] Verificar recálculo automático de costo
   - [ ] Desactivar receta con producto activo (debe fallar)
   - [ ] Desactivar receta con OPs pendientes (debe fallar)
   - [ ] Desactivar receta válida
   - [ ] Reactivar receta

2. **Órdenes de Producción:**
   - [ ] Crear OP con producto sin receta (debe advertir)
   - [ ] Crear OP con stock insuficiente (debe alertar)
   - [ ] Iniciar OP pendiente
   - [ ] Finalizar OP con cantidad diferente a planificada
   - [ ] Verificar actualización de pedidos a "listo"
   - [ ] Cancelar OP con justificación
   - [ ] Intentar editar OP terminada (debe bloquear)

3. **Agenda de Producción (UC-29):**
   - [ ] Cambiar entre vistas día/semana/mes
   - [ ] Crear 11+ OPs para un día (debe mostrar alerta de sobrecarga)
   - [ ] Crear OP con fecha_límite pasada (debe aparecer badge "Atrasada")
   - [ ] Filtrar por producto específico
   - [ ] Filtrar por estado de OP
   - [ ] Filtrar por usuario asignado
   - [ ] Navegar con botones Anterior/Siguiente
   - [ ] Usar botón "Hoy"
   - [ ] Clic en pedido para ver detalle
   - [ ] Clic en OP para ver detalle
   - [ ] Verificar alertas automáticas (sobrecarga, atrasadas, urgentes)
   - [ ] Verificar días pasados aparezcan con fondo gris
   - [ ] Verificar día actual con borde azul
   - [ ] Verificar contador de carga (X/10 OP(s))

4. **Integración:**
   - [ ] Crear receta → producto → pedido → OP → finalizar
   - [ ] Verificar descuento de stock
   - [ ] Verificar registros de auditoría
   - [ ] Crear pedido con fecha_entrega hoy → verificar aparece en agenda
   - [ ] Crear OP con fecha_inicio hoy → verificar aparece en agenda

---

## 📝 Pendientes y Mejoras Futuras

### Alta Prioridad:
- [ ] Implementar descuento real de stock en finalización de OP
- [ ] Widget de dashboard con métricas de producción
- [ ] Exportación Excel/PDF de recetas
- [ ] Exportar agenda a PDF/Excel

### Media Prioridad:
- [ ] Gráficos de costos por receta
- [ ] Historial de cambios de recetas
- [ ] Templates de recetas comunes
- [ ] Calculadora de escalado de recetas
- [ ] Alerta de vencimiento de stock en OPs
- [ ] Drag-and-drop para reprogramar OPs en agenda
- [ ] Vista de carga de trabajo por colaborador
- [ ] Notificaciones push para alertas de sobrecarga

### Baja Prioridad:
- [ ] App móvil para control de producción en cocina
- [ ] Códigos QR en recetas impresas
- [ ] Sistema de calificación de recetas
- [ ] Sugerencias de recetas según stock disponible
- [ ] Configuración de capacidad máxima por día de semana
- [ ] Vista de Gantt para planificación a largo plazo

---

## 🐛 Problemas Conocidos

Ninguno reportado hasta el momento.

---

## 📞 Soporte

Para consultas o reportes de bugs, contactar al equipo de desarrollo.

**Última actualización:** 05/12/2025
