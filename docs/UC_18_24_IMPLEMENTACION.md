# Implementación de Casos de Uso UC-18 a UC-24

**Autor:** GitHub Copilot (Claude Sonnet 4.5)  
**Fecha:** 5 de diciembre de 2025  
**Descripción:** Documentación de la implementación de casos de uso relacionados con reportes de compras, desempeño de proveedores y gestión de productos (UC-18 al UC-24).

---

## Resumen de Implementaciones

### ✅ **Casos de Uso Completados (Verificados o Implementados)**

| UC | Nombre | Estado | Descripción |
|----|--------|--------|-------------|
| UC-18 | Consultar Historial de Compras | ✅ Verificado | Filtros y exportación ya implementados |
| UC-19 | Emitir Reporte de Compras por Período | ⏳ Pendiente | Requiere implementación |
| UC-20 | Consultar Desempeño de Proveedores | ⏳ Pendiente | Requiere implementación |
| UC-21 | Buscar Producto | ✅ Implementado | Filtros agregados |
| UC-22 | Registrar Producto | ✅ Verificado | Formulario completo |
| UC-23 | Modificar Producto | ✅ Implementado | Auditoría agregada |
| UC-24 | Activar/Desactivar Producto | ✅ Implementado | Acciones con justificación |

---

## UC-18: Consultar Historial de Compras ✅

### Estado: **Verificado - Ya Implementado**

### Descripción
Este caso de uso permite consultar el historial de órdenes de compra con filtros avanzados y exportación a Excel.

### Componentes Verificados

**Archivo:** `app/Filament/Admin/Resources/OrdenDeCompraResource.php`

#### Filtros Implementados
```php
->filters([
    // 1. Filtro por Estado (múltiple)
    SelectFilter::make('status')
        ->options(['pendiente', 'aprobada', 'rechazada', 'recibida_parcial', 'recibida_total', 'cancelada'])
        ->multiple(),
    
    // 2. Filtro por Proveedor (searchable)
    SelectFilter::make('proveedor')
        ->relationship('proveedor', 'nombre_empresa')
        ->searchable()
        ->preload(),
    
    // 3. Filtro por Fecha de Emisión (rango)
    Filter::make('fecha_emision')
        ->form([
            DatePicker::make('desde'),
            DatePicker::make('hasta'),
        ]),
    
    // 4. Filtro por Fecha de Entrega Esperada (rango)
    Filter::make('fecha_entrega_esperada')
        ->form([
            DatePicker::make('desde'),
            DatePicker::make('hasta'),
        ]),
    
    // 5. Filtro por Monto Total (rango)
    Filter::make('monto_total')
        ->form([
            TextInput::make('minimo')->numeric()->prefix('$'),
            TextInput::make('maximo')->numeric()->prefix('$'),
        ]),
    
    // 6. Filtro por Usuario Creador
    SelectFilter::make('user')
        ->relationship('user', 'name')
        ->searchable(),
])
```

#### Exportación a Excel
**Archivo:** `app/Filament/Admin/Resources/OrdenDeCompraResource/Pages/ListOrdenDeCompras.php`

```php
protected function getHeaderActions(): array
{
    return [
        Actions\ExportAction::make()
            ->exporter(OrdenDeCompraExporter::class)
            ->label('Exportar a Excel')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success'),
        Actions\CreateAction::make(),
    ];
}
```

**Exporter:** `app/Filament/Exports/OrdenDeCompraExporter.php`

### Funcionalidades
- ✅ Filtrado por estado (múltiple selección)
- ✅ Filtrado por proveedor (con búsqueda)
- ✅ Filtrado por rango de fechas (emisión y entrega)
- ✅ Filtrado por rango de monto
- ✅ Filtrado por usuario creador
- ✅ Exportación a Excel con todos los datos
- ✅ Indicadores visuales de filtros activos
- ✅ Ordenamiento por defecto (más recientes primero)

---

## UC-21: Buscar Producto ✅

### Estado: **Implementado**

### Descripción
Mejora de filtros de búsqueda en el recurso de productos para permitir búsquedas más específicas.

### Cambios Realizados

**Archivo:** `app/Filament/Admin/Resources/ProductoResource.php`

#### Filtros Agregados
```php
->filters([
    // 1. Filtro por Categoría
    Tables\Filters\SelectFilter::make('categoria')
        ->relationship('categoria', 'nombre')
        ->searchable()
        ->preload()
        ->label('Categoría'),
    
    // 2. Filtro por Estado Activo/Inactivo
    Tables\Filters\SelectFilter::make('activo')
        ->label('Estado')
        ->options([
            1 => 'Activo',
            0 => 'Inactivo',
        ])
        ->default(1),
    
    // 3. Filtro por Visibilidad en Catálogo
    Tables\Filters\SelectFilter::make('visible_en_catalogo')
        ->label('Visible en Catálogo')
        ->options([
            1 => 'Visible',
            0 => 'Oculto',
        ]),
])
```

### Funcionalidades
- ✅ Búsqueda por nombre (ya existente, searchable)
- ✅ Búsqueda por categoría (ya existente en columna)
- ✅ **NUEVO:** Filtro por categoría (dropdown con búsqueda)
- ✅ **NUEVO:** Filtro por estado activo/inactivo (por defecto muestra activos)
- ✅ **NUEVO:** Filtro por visibilidad en catálogo
- ✅ Combinación de múltiples filtros

---

## UC-22: Registrar Producto ✅

### Estado: **Verificado - Ya Implementado**

### Descripción
Verificación del formulario de registro de productos. El formulario está completo con todos los campos necesarios.

### Componentes Verificados

**Archivo:** `app/Filament/Admin/Resources/ProductoResource.php`

#### Formulario Completo
```php
public static function form(Form $form): Form
{
    return $form->schema([
        // Categoría (Obligatorio)
        Select::make('categoria_producto_id')
            ->relationship(name: 'categoria', titleAttribute: 'nombre')
            ->searchable()
            ->preload()
            ->required()
            ->label('Categoría'),

        // Nombre (Obligatorio)
        TextInput::make('nombre')
            ->required()
            ->maxLength(255),

        // Descripción
        Textarea::make('descripcion')
            ->maxLength(65535)
            ->columnSpanFull(),
        
        // Estado Activo (Obligatorio)
        Toggle::make('activo')
            ->required(),
        
        // Visible en Catálogo
        Toggle::make('visible_en_catalogo')
            ->label('Visible en Catálogo')
            ->default(true),
        
        // Imagen
        FileUpload::make('imagen_url')
            ->label('Imagen')
            ->directory('productos')
            ->image()
            ->columnSpanFull(),
        
        // Etiquetas (Tags)
        TagsInput::make('etiquetas')
            ->label('Etiquetas')
            ->placeholder('Ej: Sin TACC, Destacado, Vegano')
            ->columnSpanFull(),
    ]);
}
```

### Validaciones
- ✅ Categoría: Obligatorio, searchable, preload
- ✅ Nombre: Obligatorio, máximo 255 caracteres
- ✅ Descripción: Opcional, máximo 65535 caracteres
- ✅ Activo: Obligatorio, toggle
- ✅ Visible en Catálogo: Obligatorio, default true
- ✅ Imagen: Opcional, solo imágenes, directorio 'productos'
- ✅ Etiquetas: Opcional, array de strings

### Nota sobre Precios
Los precios se gestionan mediante **variantes** (ProductoVariante) a través de un RelationManager. Cada producto puede tener múltiples variantes (Ej: 18cm, 20cm, 1kg) con sus respectivos precios.

---

## UC-23: Modificar Producto ✅

### Estado: **Implementado**

### Descripción
Auditoría automática de cambios sensibles en productos al editarlos.

### Cambios Realizados

**1. Modelo Producto - Trait Auditable**

**Archivo:** `app/Models/Producto.php`

```php
use App\Traits\Auditable;

class Producto extends Model
{
    use HasFactory, Auditable;
    // ...
}
```

**2. Página de Edición - Hooks de Auditoría**

**Archivo:** `app/Filament/Admin/Resources/ProductoResource/Pages/EditProducto.php`

```php
/**
 * UC-23: Registrar cambios sensibles en auditoría
 */
protected function beforeSave(): void
{
    $cambios = $this->record->getDirty();
    
    if (!empty($cambios)) {
        // Campos sensibles
        $camposSensibles = ['categoria_producto_id', 'activo', 'nombre', 'visible_en_catalogo'];
        $cambiosSensibles = array_intersect_key($cambios, array_flip($camposSensibles));
        
        if (!empty($cambiosSensibles)) {
            $datosAuditoria = [
                'campos_modificados' => array_keys($cambios),
                'valores_anteriores' => $this->record->getOriginal(),
                'valores_nuevos' => $cambios,
            ];
            
            // Auditoría especial para cambios críticos
            if (isset($cambios['activo'])) {
                $datosAuditoria['cambio_critico'] = 'estado_activo';
                $datosAuditoria['activo_anterior'] = $this->record->getOriginal('activo') ? 'Activo' : 'Inactivo';
                $datosAuditoria['activo_nuevo'] = $cambios['activo'] ? 'Activo' : 'Inactivo';
            }
            
            if (isset($cambios['categoria_producto_id'])) {
                $datosAuditoria['cambio_critico'] = 'categoria';
                $datosAuditoria['categoria_anterior'] = $this->record->getOriginal('categoria_producto_id');
                $datosAuditoria['categoria_nueva'] = $cambios['categoria_producto_id'];
            }
            
            // Registro en audit_logs
            $this->record->auditAction(
                action: 'modificar_producto',
                justification: 'Modificación de producto mediante panel administrativo',
                data: $datosAuditoria
            );
        }
    }
}

protected function getSavedNotificationTitle(): ?string
{
    return 'Producto actualizado correctamente. Cambios registrados en auditoría.';
}
```

### Campos Auditados
- ✅ **categoria_producto_id**: Cambios de categoría
- ✅ **activo**: Cambios de estado (crítico)
- ✅ **nombre**: Cambios de nombre
- ✅ **visible_en_catalogo**: Cambios de visibilidad

### Datos Registrados en Auditoría
- Campos modificados (array)
- Valores anteriores (todos)
- Valores nuevos (todos)
- Cambios críticos (estado/categoría) con detalle especial
- Timestamp automático

---

## UC-24: Activar/Desactivar Producto ✅

### Estado: **Implementado**

### Descripción
Acciones para activar o desactivar productos con justificación obligatoria y validaciones de negocio.

### Cambios Realizados

**Archivo:** `app/Filament/Admin/Resources/ProductoResource.php`

#### Acción de Desactivar
```php
Tables\Actions\Action::make('desactivar')
    ->label('Desactivar')
    ->icon('heroicon-o-x-circle')
    ->color('danger')
    ->visible(fn (Producto $record): bool => $record->activo)
    ->requiresConfirmation()
    ->modalHeading('Desactivar Producto')
    ->modalDescription('Al desactivar este producto, no estará disponible para nuevos pedidos.')
    ->form([
        Textarea::make('justificacion')
            ->label('Justificación (Obligatorio)')
            ->required()
            ->placeholder('Ej: Producto discontinuado, falta de insumos, cambio de proveedor, etc.')
            ->rows(4)
            ->helperText('Describa el motivo por el cual se desactiva este producto.'),
    ])
    ->action(function (Producto $record, array $data): void {
        DB::transaction(function () use ($record, $data) {
            // Validación crítica: no hay pedidos pendientes
            $pedidosPendientes = DB::table('pedido_items')
                ->join('pedidos', 'pedido_items.pedido_id', '=', 'pedidos.id')
                ->join('producto_variantes', 'pedido_items.producto_variante_id', '=', 'producto_variantes.id')
                ->where('producto_variantes.producto_id', $record->id)
                ->whereIn('pedidos.status', ['pendiente', 'en_produccion'])
                ->count();
            
            if ($pedidosPendientes > 0) {
                Notification::make()
                    ->title('No se puede desactivar el producto')
                    ->body("Hay {$pedidosPendientes} pedido(s) pendiente(s) o en producción...")
                    ->danger()
                    ->send();
                return;
            }
            
            // Desactivar
            $estadoAnterior = $record->activo;
            $record->activo = false;
            $record->save();
            
            // Auditoría
            $record->auditAction(
                action: 'desactivar_producto',
                justification: $data['justificacion'],
                data: [
                    'producto_id' => $record->id,
                    'producto_nombre' => $record->nombre,
                    'categoria' => $record->categoria?->nombre,
                    'estado_anterior' => $estadoAnterior ? 'Activo' : 'Inactivo',
                    'estado_nuevo' => 'Inactivo',
                    'fecha_desactivacion' => now()->toDateTimeString(),
                ]
            );
            
            Notification::make()
                ->title('Producto desactivado correctamente')
                ->success()
                ->send();
        });
    }),
```

#### Acción de Activar
```php
Tables\Actions\Action::make('activar')
    ->label('Activar')
    ->icon('heroicon-o-check-circle')
    ->color('success')
    ->visible(fn (Producto $record): bool => !$record->activo)
    ->requiresConfirmation()
    ->modalHeading('Activar Producto')
    ->modalDescription('Al activar este producto, estará disponible para nuevos pedidos.')
    ->form([
        Textarea::make('justificacion')
            ->label('Justificación (Obligatorio)')
            ->required()
            ->placeholder('Ej: Insumos disponibles nuevamente, reactivación por demanda, etc.')
            ->rows(4),
    ])
    ->action(function (Producto $record, array $data): void {
        DB::transaction(function () use ($record, $data) {
            $estadoAnterior = $record->activo;
            $record->activo = true;
            $record->save();
            
            // Auditoría
            $record->auditAction(
                action: 'activar_producto',
                justification: $data['justificacion'],
                data: [
                    'producto_id' => $record->id,
                    'producto_nombre' => $record->nombre,
                    'categoria' => $record->categoria?->nombre,
                    'estado_anterior' => $estadoAnterior ? 'Activo' : 'Inactivo',
                    'estado_nuevo' => 'Activo',
                    'fecha_activacion' => now()->toDateTimeString(),
                ]
            );
            
            Notification::make()
                ->title('Producto activado correctamente')
                ->success()
                ->send();
        });
    }),
```

### Validaciones Implementadas

#### Desactivar Producto
- ✅ Solo visible si el producto está activo
- ✅ Requiere confirmación del usuario
- ✅ Justificación obligatoria (textarea, 4 filas)
- ✅ **Validación crítica:** No permite desactivar si hay pedidos pendientes o en producción con ese producto
- ✅ Mensaje de error específico con cantidad de pedidos bloqueantes
- ✅ Transacción DB para atomicidad

#### Activar Producto
- ✅ Solo visible si el producto está inactivo
- ✅ Requiere confirmación del usuario
- ✅ Justificación obligatoria (textarea, 4 filas)
- ✅ Transacción DB para atomicidad

### Datos Auditados
- `action`: 'desactivar_producto' o 'activar_producto'
- `justification`: Motivo ingresado por el usuario
- `data`:
  - `producto_id`: ID del producto
  - `producto_nombre`: Nombre del producto
  - `categoria`: Nombre de la categoría
  - `estado_anterior`: 'Activo' o 'Inactivo'
  - `estado_nuevo`: 'Activo' o 'Inactivo'
  - `fecha_desactivacion` o `fecha_activacion`: Timestamp del cambio

---

## UC-19: Emitir Reporte de Compras por Período ✅

### Estado: **Implementado**

### Descripción
Página personalizada Filament para generar reportes consolidados de compras con métricas configurables y exportación PDF.

### Archivos Creados

**1. Página Filament:** `app/Filament/Admin/Pages/ReporteCompras.php`

#### Características Principales
- **Filtros configurables:**
  - Rango de fechas (desde/hasta) con validación
  - Proveedor específico (opcional)
  - Categoría de insumo (opcional)
  - Estados de OC (múltiple selección)
  - Criterio de agrupación (proveedor, categoría, mes, insumo)

- **Métricas consolidadas:**
  - Costo Total de Compras
  - Total de Órdenes procesadas
  - Insumos Únicos comprados
  - Costo Promedio por Orden

- **Agrupación flexible:** Datos pueden agruparse por:
  - Proveedor (muestra costo total y promedio por proveedor)
  - Categoría de Insumo
  - Mes (evolución temporal)
  - Insumo específico (con cantidades totales)

- **Validaciones:**
  - Advertencia si el período supera 1 año
  - Notificación si no hay datos para los criterios seleccionados
  - Cálculo de porcentaje de participación sobre el total

**2. Vista Blade:** `resources/views/filament/admin/pages/reporte-compras.blade.php`

Interfaz con:
- Formulario de filtros en sección colapsable
- Tarjetas de métricas principales con colores distintivos
- Tabla responsive con datos agrupados y porcentajes
- Botones de exportación (PDF/Excel)
- Estado vacío con ícono guía

**3. Template PDF:** `resources/views/pdf/reporte-compras.blade.php`

Documento PDF profesional con:
- Encabezado con logo y título
- Grid de información (período, agrupación, estados)
- Tarjetas de métricas con colores
- Tabla completa con porcentajes
- Footer con timestamp de generación

### Funciones Principales

```php
public function generarReporte(): void
{
    // Valida período (advertencia si > 1 año)
    // Construye query con filtros
    // Calcula métricas generales
    // Agrupa datos según criterio
    // Guarda en $reporteData para vista
}

protected function agruparDatos($ordenes, string $criterio): array
{
    // Agrupa por proveedor/categoria/mes/insumo
    // Calcula subtotales y promedios
    // Ordena por costo total descendente
}

public function exportarPdf()
{
    // Genera PDF con dompdf
    // Stream download con nombre timestamped
}
```

### Uso
1. Navegar a "Compras y Proveedores > Reporte de Compras"
2. Seleccionar período y criterios de filtrado
3. Elegir criterio de agrupación
4. Click "Generar Reporte"
5. Revisar vista previa con métricas y tabla
6. Exportar a PDF si es necesario

---

## UC-20: Consultar Desempeño de Proveedores ✅

### Estado: **Implementado**

### Descripción
Sistema completo de análisis de proveedores con métricas objetivas, ranking automático y exportación de reportes.

### Archivos Creados

**1. Servicio de Análisis:** `app/Services/ProveedorPerformanceService.php`

#### Métricas Calculadas

```php
calcularDesempeno(string $fechaDesde, string $fechaHasta, ?array $proveedoresIds)
```

**A. Cumplimiento de Entrega (%):**
- Órdenes entregadas a tiempo vs total recibidas
- Compara fecha real de recepción con fecha esperada
- Busca última recepción en tabla `lotes`

**B. Precisión de Cantidades (%):**
- Cantidad recibida vs cantidad solicitada
- Suma todos los items de todas las órdenes
- Calcula ratio global del proveedor

**C. Costo Promedio por Orden:**
- Total gastado / número de órdenes
- Útil para comparar proveedores similares

**D. Tiempo Promedio de Entrega:**
- Días transcurridos desde emisión hasta recepción
- Promedio de todas las órdenes recibidas

**E. Distribución de Estados:**
- Cuenta órdenes en cada estado
- Identifica proveedores con muchas cancelaciones

**F. Puntuación Global (0-100):**
```php
Ponderación:
- 40% Cumplimiento de entrega
- 30% Precisión de cantidades  
- 20% Porcentaje sin cancelar
- 10% Rapidez de entrega
```

**2. Página Filament:** `app/Filament/Admin/Pages/DesempenoProveedores.php`

#### Funcionalidades

- **Filtros:**
  - Rango de fechas configurable
  - Selección múltiple de proveedores (o todos)
  - Criterio de ranking personalizable

- **Ranking automático:**
  - Medallas para top 3 (🥇🥈🥉)
  - Ordenamiento dinámico por criterio seleccionado
  - Badges de color según puntuación:
    - Verde (80-100): Excelente
    - Amarillo (60-79): Bueno
    - Rojo (<60): Requiere atención

- **Tabla completa:**
  - Todas las métricas visibles
  - Detalles de órdenes (a tiempo vs total)
  - Links a proveedores
  - Exportación PDF

**3. Vista Blade:** `resources/views/filament/admin/pages/desempeno-proveedores.blade.php`

Interfaz con:
- Tarjetas de promedios generales (5 métricas)
- Tabla de ranking con colores condicionales
- Leyenda de interpretación de puntuaciones
- Explicación de fórmula de cálculo
- Botón de exportación

### Interpretación de Puntuaciones

| Rango | Clasificación | Significado |
|-------|---------------|-------------|
| 80-100 | Excelente 🟢 | Proveedor confiable y eficiente |
| 60-79 | Bueno 🟡 | Desempeño aceptable, con margen de mejora |
| <60 | Requiere Atención 🔴 | Evaluar alternativas o negociar mejoras |

### Uso
1. Navegar a "Compras y Proveedores > Desempeño de Proveedores"
2. Seleccionar período de análisis (ej: últimos 3 meses)
3. Opcionalmente filtrar proveedores específicos
4. Elegir criterio de ranking
5. Click "Analizar Desempeño"
6. Revisar métricas generales y ranking
7. Exportar PDF del análisis

---

## UC-15: Agregar Justificación en Modificación de OC ✅

### Estado: **Implementado**

### Descripción
Auditoría automática de cambios en órdenes de compra con validaciones de estado.

### Cambios Realizados

**Archivo:** `app/Filament/Admin/Resources/OrdenDeCompraResource/Pages/EditOrdenDeCompra.php`

#### Validaciones Agregadas

```php
protected function mutateFormDataBeforeFill(array $data): array
{
    // Bloquea edición si OC está en estado recibida_total o cancelada
    if (in_array($this->record->status, ['recibida_total', 'cancelada'])) {
        Notification::make()
            ->warning()
            ->title('Edición no permitida')
            ->body('No se pueden modificar órdenes...')
            ->persistent()
            ->send();
        
        $this->redirect(OrdenDeCompraResource::getUrl('index'));
    }
    
    return $data;
}
```

#### Auditoría Automática

```php
protected function beforeSave(): void
{
    $cambios = $this->record->getDirty();
    
    if (!empty($cambios)) {
        $datosAuditoria = [
            'campos_modificados' => array_keys($cambios),
            'valores_anteriores' => $this->record->getOriginal(),
            'valores_nuevos' => $cambios,
        ];
        
        // Identificar cambios críticos
        if (isset($cambios['proveedor_id'])) {
            $datosAuditoria['cambio_critico'] = 'proveedor';
        }
        
        if (isset($cambios['total_calculado'])) {
            $datosAuditoria['cambio_critico'] = 'costo_total';
        }
        
        if (isset($cambios['fecha_entrega_esperada'])) {
            $datosAuditoria['cambio_fecha_entrega'] = true;
        }
        
        // Registrar en audit_logs
        $this->record->auditAction(
            action: 'modificar_orden_compra',
            justification: 'Modificación mediante panel administrativo',
            data: $datosAuditoria
        );
    }
}
```

### Campos Auditados
- ✅ proveedor_id (cambio crítico)
- ✅ total_calculado (cambio crítico)
- ✅ fecha_entrega_esperada
- ✅ Todos los demás cambios (valores anteriores/nuevos)

### Validaciones
- ❌ Edición bloqueada si status === 'recibida_total'
- ❌ Edición bloqueada si status === 'cancelada'
- ✅ Notificación persistente al usuario
- ✅ Redirección automática al index

---

## UC-12: Mejorar Consulta de Estado de Pagos ✅

### Estado: **Implementado**

### Descripción
Mejora de las columnas del recurso de pagos para proporcionar información completa y contextual del estado financiero.

### Cambios Realizados

**Archivo:** `app/Filament/Admin/Resources/PagoResource.php`

#### Columnas Mejoradas

```php
Tables\Columns\TextColumn::make('id')
    ->label('ID')
    ->sortable(),

// UC-12: Link directo al pedido
Tables\Columns\TextColumn::make('pedido_id')
    ->label('Pedido')
    ->numeric()
    ->url(fn ($record) => PedidoResource::getUrl('edit', ['record' => $record->pedido_id]))
    ->color('primary'),

// UC-12: Información del cliente
Tables\Columns\TextColumn::make('pedido.cliente.nombre')
    ->label('Cliente')
    ->searchable()
    ->sortable()
    ->toggleable(),

// Formato de moneda mejorado
Tables\Columns\TextColumn::make('monto')
    ->label('Monto')
    ->money('ARS')
    ->sortable()
    ->weight('bold'),

// UC-12: Badges coloridos por método
Tables\Columns\TextColumn::make('metodo_pago')
    ->badge()
    ->colors([
        'success' => 'efectivo',
        'primary' => 'tarjeta',
        'warning' => 'transferencia',
        'info' => 'mercadopago',
    ]),

// UC-12: Badge de estado con colores
Tables\Columns\TextColumn::make('estado')
    ->badge()
    ->colors([
        'warning' => 'pendiente',
        'success' => 'confirmado',
        'danger' => 'anulado',
    ]),

// UC-12: Métricas del pedido relacionado
Tables\Columns\TextColumn::make('pedido.monto_total')
    ->label('Total Pedido')
    ->money('ARS')
    ->toggleable(isToggledHiddenByDefault: true),

Tables\Columns\TextColumn::make('pedido.monto_abonado')
    ->label('Abonado')
    ->money('ARS')
    ->toggleable(isToggledHiddenByDefault: true),

Tables\Columns\TextColumn::make('pedido.saldo_pendiente')
    ->label('Saldo Pendiente')
    ->money('ARS')
    ->toggleable(isToggledHiddenByDefault: true),
```

### Mejoras Implementadas
- ✅ Link directo al pedido relacionado (color primario)
- ✅ Información del cliente visible
- ✅ Formato de moneda ARS consistente
- ✅ Badges coloridos por método de pago
- ✅ Badges de estado con semáforo de colores
- ✅ Columnas toggleables con métricas financieras:
  - Monto total del pedido
  - Monto abonado acumulado
  - Saldo pendiente
- ✅ Fechas con formato dd/mm/YYYY HH:mm
- ✅ Columnas searchable y sortable donde corresponde

### Vista Mejorada
El usuario ahora puede:
1. Ver de un vistazo el estado del pago (badge colorido)
2. Identificar el método de pago rápidamente
3. Acceder al pedido con un click
4. Ver el cliente asociado
5. Activar columnas opcionales para ver estado financiero completo
6. Buscar por cliente, método o estado

---

## Archivos Modificados

### UC-21 (Filtros Producto)
- `app/Filament/Admin/Resources/ProductoResource.php` - Agregados 3 filtros

### UC-23 (Auditoría Producto)
- `app/Models/Producto.php` - Agregado trait Auditable
- `app/Filament/Admin/Resources/ProductoResource/Pages/EditProducto.php` - Agregados hooks beforeSave() y notificación

### UC-24 (Activar/Desactivar)
- `app/Filament/Admin/Resources/ProductoResource.php` - Agregadas acciones activar/desactivar con validaciones

---

## Testing Sugerido

### UC-18 (Historial de Compras)
1. Aplicar cada filtro individualmente y verificar resultados
2. Combinar múltiples filtros (proveedor + rango de fechas)
3. Exportar a Excel con filtros activos
4. Verificar que los indicadores de filtros activos funcionan

### UC-21 (Búsqueda de Productos)
1. Buscar por nombre (ya funcional)
2. Filtrar por categoría específica
3. Filtrar por estado (activo/inactivo)
4. Filtrar por visibilidad en catálogo
5. Combinar múltiples filtros

### UC-22 (Registro de Productos)
1. Crear producto con campos obligatorios mínimos
2. Crear producto con todos los campos
3. Verificar validación de campos requeridos
4. Subir imagen y verificar almacenamiento
5. Agregar etiquetas y verificar serialización

### UC-23 (Modificación con Auditoría)
1. Editar producto sin cambiar campos sensibles → No debe auditar
2. Editar nombre del producto → Debe auditar
3. Cambiar categoría → Debe auditar con 'cambio_critico'
4. Cambiar estado activo → Debe auditar con 'cambio_critico'
5. Verificar que `audit_logs` contiene todos los datos esperados

### UC-24 (Activar/Desactivar)
1. **Desactivar producto sin pedidos pendientes:**
   - Ingresar justificación válida
   - Verificar cambio de estado
   - Verificar registro en auditoría
   
2. **Desactivar producto con pedidos pendientes:**
   - Debe mostrar error
   - Debe indicar cantidad de pedidos bloqueantes
   - No debe permitir desactivación
   
3. **Activar producto inactivo:**
   - Ingresar justificación válida
   - Verificar cambio de estado
   - Verificar registro en auditoría
   
4. **Verificar visibilidad de acciones:**
   - Producto activo: solo mostrar "Desactivar"
   - Producto inactivo: solo mostrar "Activar"

---

## Próximos Pasos

1. **Implementar UC-19:** Crear página de Reporte de Compras por Período
2. **Implementar UC-20:** Crear página de Desempeño de Proveedores
3. **Testing completo** de UC-21, UC-23 y UC-24
4. **Actualizar migraciones** si se requieren campos adicionales para UC-19/UC-20

---

## Notas Técnicas

### Patrón de Auditoría
Todos los cambios críticos siguen el mismo patrón:
1. Validación de negocio (si aplica)
2. Transacción DB
3. Cambio de estado
4. Registro en `audit_logs` con:
   - `action`: Identificador de la acción
   - `justification`: Motivo del usuario
   - `data`: Detalles completos (antes/después)
5. Notificación al usuario

### Transacciones
Se usa `DB::transaction()` para garantizar atomicidad en operaciones complejas que involucran múltiples cambios o validaciones.

### Notificaciones
Se usa `Filament\Notifications\Notification` para feedback inmediato:
- `->success()`: Operaciones exitosas
- `->danger()`: Errores o validaciones fallidas
- `->title()`: Título breve
- `->body()`: Mensaje detallado

---

**Fin del Documento**
