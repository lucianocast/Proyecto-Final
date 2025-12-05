# 📐 Sistema de Conversión de Unidades - Guía Rápida

## ✅ Implementación Completa

### Archivos Creados
- ✅ `app/Helpers/ConversionHelper.php` - Helper de conversiones
- ✅ `tests/Unit/ConversionHelperTest.php` - 14 tests unitarios
- ✅ `tests/Feature/OrdenDeCompraConversionTest.php` - 5 tests de integración

### Archivos Modificados
- ✅ `app/Filament/Admin/Resources/OrdenDeCompraResource.php` - Usa ConversionHelper
- ✅ `app/Filament/Admin/Resources/ProveedorResource/RelationManagers/CatalogoInsumosRelationManager.php` - Validación automática
- ✅ `app/Filament/Admin/Pages/RegistrarProduccion.php` - Mensajes de error con unidades

---

## 🎯 Cómo Funciona

### 1. Unidad Base (en `insumos`)
```php
Insumo::create([
    'nombre' => 'Harina 0000',
    'unidad_de_medida' => UnidadMedida::GRAMO, // ← Unidad BASE
]);
```

### 2. Unidad de Compra (en `insumo_proveedor`)
```php
$proveedor->insumos()->attach($insumo->id, [
    'precio' => 1500,
    'unidad_compra' => UnidadMedida::KILOGRAMO->value, // ← Proveedor vende en kg
    'cantidad_por_bulto' => 1, // ← 1 bulto = 1 kg
]);
```

### 3. Conversión Automática
```php
use App\Helpers\ConversionHelper;

// Convertir 10 kg a gramos
$cantidadEnGramos = ConversionHelper::convertirABase(
    cantidad: 10,
    unidadCompra: UnidadMedida::KILOGRAMO,
    unidadBase: UnidadMedida::GRAMO
);
// Resultado: 10,000 gramos
```

---

## 📊 Conversiones Soportadas

| De          | A          | Factor |
|-------------|------------|--------|
| Kilogramo   | Gramo      | × 1000 |
| Gramo       | Kilogramo  | ÷ 1000 |
| Litro       | Mililitro  | × 1000 |
| Mililitro   | Litro      | ÷ 1000 |
| Unidad      | Unidad     | × 1    |

---

## 🛡️ Validaciones Automáticas

### ❌ Conversiones Incompatibles
```php
// Esto lanzará una excepción
ConversionHelper::convertirABase(
    cantidad: 1,
    unidadCompra: UnidadMedida::KILOGRAMO, // Peso
    unidadBase: UnidadMedida::LITRO        // Volumen ❌
);
// Exception: "Conversión incompatible: no se puede convertir..."
```

### ✅ Validación en UI
- **En CatalogoInsumosRelationManager**: Si seleccionas una unidad incompatible, muestra notificación y limpia el campo
- **En OrdenDeCompraResource**: Valida antes de crear lotes

---

## 🚀 Uso en el Sistema

### 1️⃣ Registrar Insumo con Proveedor
```
1. Ir a Admin → Proveedores → Seleccionar proveedor
2. Tab "Catálogo de Insumos" → Agregar Insumo
3. Seleccionar insumo (ej: Harina 0000)
4. Ver "Unidad Base": Gramos (g) [automático]
5. Seleccionar "Unidad de Compra": Kilogramo (kg)
6. Campo "Cantidad por Bulto" se autocompleta con 1000 ✨
7. Guardar
```

### 2️⃣ Crear Orden de Compra
```
1. Admin → Órdenes de Compra → Nueva
2. Seleccionar Proveedor
3. Agregar Item: Harina 0000
4. Cantidad: 10 [kg] ← El sistema muestra la unidad
5. Precio se autocompleta
6. Guardar orden
```

### 3️⃣ Recibir Stock
```
1. Abrir orden "Aprobada"
2. Click "Recibir Stock"
3. Llenar datos de lote (fecha vencimiento, código)
4. Confirmar
5. ✨ Sistema convierte automáticamente: 10 kg → 10,000 g
6. Lote creado con 10,000 gramos
```

### 4️⃣ Usar en Recetas
```
1. Admin → Recetas → Editar receta
2. Tab "Insumos" → Agregar Insumo
3. Seleccionar Harina 0000
4. Campo muestra suffix: [g] ← Unidad base
5. Ingresar cantidad: 500
6. Guardar
7. ✨ Sistema usa 500g directamente (ya está en unidad base)
```

---

## 🧪 Tests de Validación

### Ejecutar tests
```bash
php artisan test --filter=Conversion
```

### Cobertura
- ✅ 14 tests unitarios (ConversionHelper)
- ✅ 5 tests de integración (flujo completo)
- ✅ 33 aserciones totales
- ✅ Validación de conversiones incompatibles
- ✅ Validación de factores automáticos

---

## 🔍 Métodos Disponibles

### `ConversionHelper::convertirABase()`
Convierte cantidad de unidad de compra a unidad base.

```php
ConversionHelper::convertirABase(
    cantidad: 5,
    unidadCompra: UnidadMedida::KILOGRAMO,
    unidadBase: UnidadMedida::GRAMO
); // 5000
```

### `ConversionHelper::sonCompatibles()`
Valida si dos unidades pueden convertirse.

```php
ConversionHelper::sonCompatibles(
    UnidadMedida::KILOGRAMO,
    UnidadMedida::GRAMO
); // true

ConversionHelper::sonCompatibles(
    UnidadMedida::KILOGRAMO,
    UnidadMedida::LITRO
); // false
```

### `ConversionHelper::calcularFactorConversion()`
Calcula el factor de conversión entre dos unidades.

```php
ConversionHelper::calcularFactorConversion(
    UnidadMedida::KILOGRAMO,
    UnidadMedida::GRAMO
); // 1000
```

### `ConversionHelper::getTipoUnidad()`
Obtiene el tipo de unidad (peso, volumen, unidad).

```php
ConversionHelper::getTipoUnidad(UnidadMedida::KILOGRAMO); // 'peso'
ConversionHelper::getTipoUnidad(UnidadMedida::LITRO);     // 'volumen'
ConversionHelper::getTipoUnidad(UnidadMedida::UNIDAD);    // 'unidad'
```

---

## 💡 Casos de Uso Reales

### Caso 1: Comprar Harina en Bolsas de 25kg
```php
// En insumo_proveedor
'unidad_compra' => 'kg',
'cantidad_por_bulto' => 25, // ← 1 bolsa = 25 kg

// Al comprar "2 bolsas"
$cantidadReal = ConversionHelper::convertirABase(
    cantidad: 2 * 25, // = 50 kg
    unidadCompra: UnidadMedida::KILOGRAMO,
    unidadBase: UnidadMedida::GRAMO
);
// Resultado: 50,000 gramos
```

### Caso 2: Comprar Esencia en Frascos de 100ml
```php
// En insumo_proveedor
'unidad_compra' => 'ml',
'cantidad_por_bulto' => 100, // ← 1 frasco = 100 ml

// Al comprar "5 frascos"
$cantidadReal = ConversionHelper::convertirABase(
    cantidad: 5 * 100, // = 500 ml
    unidadCompra: UnidadMedida::MILILITRO,
    unidadBase: UnidadMedida::MILILITRO
);
// Resultado: 500 ml (misma unidad, sin conversión)
```

### Caso 3: Comprar Huevos por Maple de 30
```php
// En insumo_proveedor
'unidad_compra' => 'u',
'cantidad_por_bulto' => 30, // ← 1 maple = 30 unidades

// Al comprar "3 maples"
$cantidadReal = ConversionHelper::convertirABase(
    cantidad: 3 * 30, // = 90 unidades
    unidadCompra: UnidadMedida::UNIDAD,
    unidadBase: UnidadMedida::UNIDAD
);
// Resultado: 90 unidades
```

---

## ⚠️ Errores Comunes

### Error 1: "Conversión incompatible"
```
Causa: Intentas convertir peso a volumen (o viceversa)
Solución: Verifica que la unidad_compra sea del mismo tipo que unidad_de_medida
```

### Error 2: "Object of class UnidadMedida could not be converted to string"
```
Causa: Intentas concatenar el Enum directamente
Solución: Usa ->value o ->getLabel()
Ejemplo: $insumo->unidad_de_medida->value
```

### Error 3: Stock negativo después de conversión
```
Causa: cantidad_por_bulto incorrecto
Solución: Verifica el factor:
  - 1 kg → 1000 g (cantidad_por_bulto = 1, factor automático = 1000)
  - 1 L → 1000 ml (cantidad_por_bulto = 1, factor automático = 1000)
```

---

## ✅ Checklist de Implementación

- [x] ConversionHelper creado con 4 métodos
- [x] Tests unitarios (14 tests) ✅
- [x] Tests de integración (5 tests) ✅
- [x] OrdenDeCompraResource actualizado
- [x] CatalogoInsumosRelationManager con validación
- [x] RegistrarProduccion con mensajes correctos
- [x] Documentación completa

---

## 🎓 Próximos Pasos (Opcional)

1. **Agregar más unidades**: Onzas, libras, galones, etc.
2. **Dashboard de conversiones**: Mostrar factores configurados
3. **Auditoría**: Log de conversiones en órdenes
4. **Reportes**: Stock en múltiples unidades (kg y g)

---

**✨ Sistema completamente funcional y testeado**
