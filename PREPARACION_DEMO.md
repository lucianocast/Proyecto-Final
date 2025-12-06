# 📋 Guía de Preparación para Demo - Entrega Final

## 🎯 Preparación Rápida (5 minutos)

### Opción 1: Script Automático (Recomendado)
```bash
# Ejecutar script de preparación
preparar_demo.bat
```

Este script:
1. ✅ Limpia la base de datos
2. ✅ Crea todos los datos de prueba
3. ✅ Ejecuta tests de verificación
4. ✅ Inicia el servidor Laravel
5. ✅ Inicia Mailpit para emails

### Opción 2: Manual
```bash
# 1. Limpiar y migrar BD
php artisan migrate:fresh --force

# 2. Crear datos de prueba
php artisan db:seed --class=DatosPruebaEntregaSeeder

# 3. Iniciar servidor
php artisan serve

# 4. Iniciar Mailpit (en otra terminal)
mailpit
```

---

## 📦 Datos Creados por el Seeder

### 👤 Usuarios
| Rol | Email | Password | URL Acceso |
|-----|-------|----------|------------|
| Admin | admin@test.com | password | http://127.0.0.1:8000/admin |
| Vendedor | vendedor@test.com | password | http://127.0.0.1:8000 |
| Cliente Web | cliente@test.com | password | http://127.0.0.1:8000 |

### 👥 Clientes (8 totales)
- María González (maria.gonzalez@email.com)
- Juan Pérez (juan.perez@email.com)
- Laura Martínez (laura.martinez@email.com)
- Carlos Rodríguez (carlos.rodriguez@email.com)
- Ana Fernández (ana.fernandez@email.com)
- Diego Sánchez (diego.sanchez@email.com)
- Lucía López (lucia.lopez@email.com)
- Miguel Torres (miguel.torres@email.com)

### 🏢 Proveedores (3 totales)
1. **Distribuidora La Central** (CUIT: 20-12345678-9)
   - Contacto: Roberto Gómez
   - Vende: Harinas, Azúcar, Polvo de Hornear

2. **Mayorista El Buen Precio** (CUIT: 20-87654321-0)
   - Contacto: Sandra Díaz
   - Vende: Manteca, Huevos, Leche, Crema

3. **Insumos Premium SA** (CUIT: 30-11223344-5)
   - Contacto: Jorge Morales
   - Vende: Chocolate, Esencia de Vainilla, Cacao

### 📦 Insumos (10 totales con stock)
| Insumo | Stock Actual | Stock Mínimo | Proveedor |
|--------|--------------|--------------|-----------|
| Harina 0000 | 15,000 g | 5,000 g | La Central |
| Azúcar | 10,000 g | 3,000 g | La Central |
| Manteca | 5,000 g | 2,000 g | Buen Precio |
| Huevos | 120 u | 50 u | Buen Precio |
| Chocolate | 3,000 g | 1,000 g | Premium |
| Leche Entera | 5,000 ml | 2,000 ml | Buen Precio |
| Esencia Vainilla | 500 ml | 100 ml | Premium |
| Cacao en Polvo | 2,000 g | 500 g | Premium |
| Crema de Leche | 3,000 ml | 1,000 ml | Buen Precio |
| Polvo de Hornear | 800 g | 200 g | La Central |

### 🍰 Productos (6 totales con recetas)
| Producto | Precio | Tiempo Prep | Categoría |
|----------|--------|-------------|-----------|
| Torta de Chocolate | $5,500 | 120 min | Tortas |
| Cupcakes de Vainilla (x6) | $2,400 | 60 min | Cupcakes |
| Brownie con Nueces | $3,200 | 45 min | Postres |
| Torta de Vainilla | $4,800 | 100 min | Tortas |
| Mousse de Chocolate | $1,800 | 30 min | Postres |
| Galletas de Chocolate (x12) | $1,500 | 40 min | Galletas |

### 📝 Pedidos (3 confirmados listos para producir)

**Pedido #1** - Cliente: María González
- Torta de Chocolate x1 ($5,500)
- Cupcakes de Vainilla x2 ($4,800)
- **Total: $10,300**
- Entrega: +3 días desde hoy
- Estado: **Confirmado** ✅

**Pedido #2** - Cliente: Juan Pérez
- Brownie con Nueces x2 ($6,400)
- Mousse de Chocolate x4 ($7,200)
- **Total: $13,600**
- Entrega: +4 días desde hoy
- Estado: **Confirmado** ✅

**Pedido #3** - Cliente: Laura Martínez
- Torta de Vainilla x1 ($4,800)
- Galletas de Chocolate x3 ($4,500)
- **Total: $9,300**
- Entrega: +5 días desde hoy
- Estado: **Confirmado** ✅

---

## 🎬 Checklist Pre-Grabación

### Antes de iniciar la grabación:

#### ✅ Sistema
- [ ] Base de datos limpia y con datos de prueba
- [ ] Servidor Laravel corriendo (`php artisan serve`)
- [ ] Mailpit corriendo (localhost:8025)
- [ ] Tests ejecutados sin errores críticos

#### ✅ Accesos Verificados
- [ ] Login Admin funciona (admin@test.com / password)
- [ ] Login Vendedor funciona (vendedor@test.com / password)
- [ ] Catálogo público visible (http://127.0.0.1:8000)

#### ✅ Datos Listos
- [ ] 8 clientes creados y visibles en Admin/Clientes
- [ ] 6 productos con recetas completas
- [ ] 10 insumos con stock > stock mínimo
- [ ] 3 pedidos en estado "Confirmado"
- [ ] 3 proveedores con catálogos completos

#### ✅ Validaciones a Demostrar (preparar casos)
**Cliente:**
- Email duplicado → `maria.gonzalez@email.com` ya existe
- Teléfono inválido → `123` (muy corto)
- Modificar sin justificación → Debe dar error

**Proveedor:**
- CUIT duplicado → `20-12345678-9` ya existe
- CUIT inválido → `12-345` (formato incorrecto)

**Producto:**
- Nombre duplicado → `Torta de Chocolate` ya existe
- Precio en 0 → Debe alertar

**Insumo:**
- Stock mínimo > stock actual → Debe alertar
- Unidad de medida no seleccionada → Error

**Pedido:**
- Cliente inactivo → Crear cliente inactivo de prueba
- Fecha de entrega pasada → Intentar ayer
- Producto sin stock suficiente → Reducir stock de un insumo

**Orden de Compra:**
- Sin proveedor seleccionado → Error
- Cantidad en 0 → Error
- Recibir stock → Muestra conversión de unidades

---

## 🎥 Flujo de Demostración Sugerido

### 1. Casos de Uso Principales (10 min)

**UC-18: Registrar Pedido**
```
1. Admin → Pedidos → Create
2. Cliente: Seleccionar "Carlos Rodríguez"
3. Agregar: Torta de Chocolate x1
4. Fecha entrega: [HOY + 3 días]
5. Guardar → Estado "Confirmado"

VALIDACIONES:
- Intentar cliente inactivo → Error
- Intentar fecha pasada → Error
```

**UC-30 a UC-21: Flujo Completo Producción**
```
1. Agenda Producción → Ver Pedido #1 (María González)
2. Crear OP desde pedido
3. Sistema verifica stock ✓
4. Guardar OP estado "Pendiente"
5. Acción "Iniciar" → "En Proceso"
6. Acción "Finalizar" → Stock se descuenta automáticamente
7. Pedido → Estado "Listo"
8. Acción "Entregar" → Estado "Entregado"
9. Acción "Registrar Pago" → Efectivo → "Completado"
```

### 2. Módulos Inteligentes (7 min)

**Terminal 1: Compras Inteligentes**
```bash
php artisan inteligente:procesar-compras
```
- Muestra análisis de 10 insumos
- Genera OC si hay críticos
- Abre Mailpit → Ver email enviado
- Admin/Órdenes de Compra → Ver OC automática

**Terminal 2: Promociones**
```bash
php artisan inteligente:generar-promociones
```
- Analiza próximos 15 días
- Muestra días con baja producción
- Admin/Promociones → Ver promociones creadas

**Terminal 3: Análisis Comercial**
```bash
php artisan inteligente:analizar-comercial
```
- Analiza 6 productos
- Destaca productos estrella
- Oculta baja rotación
- Admin/AuditLogs → Ver registros

### 3. CRUDs (10 min)

Por cada CRUD: **Create con error → Fix → Edit → Delete/Anular**

**Orden sugerida:**
1. Clientes (email duplicado, teléfono inválido)
2. Proveedores (CUIT duplicado, anular con OC pendiente)
3. Insumos (stock mínimo, desactivar)
4. Productos (sin receta, precio 0)
5. Recetas (modificar sin justificación)
6. Órdenes de Compra (recibir stock con conversión)

### 4. Testing (5 min)

```bash
# Ejecutar tests
php artisan test

# Explicar casos específicos
php artisan test --filter=ConversionHelperTest
php artisan test --filter=PagoTest
```

---

## 🚨 Troubleshooting

### Si los datos no se crean:
```bash
php artisan migrate:fresh --force
php artisan db:seed --class=DatosPruebaEntregaSeeder
```

### Si hay error de permisos:
```bash
php artisan cache:clear
php artisan config:clear
```

### Si Mailpit no funciona:
- Verificar puerto 1025 libre
- Verificar .env: `MAIL_PORT=1025`
- Reinstalar: `choco install mailpit` (Windows)

### Si tests fallan:
- Normal, algunos pueden fallar
- Mostrar los que pasen
- Explicar qué prueban

---

## 📞 Datos de Contacto Rápido

**Para dudas durante la demo:**
- Clientes: 8 disponibles, emails en formato `nombre.apellido@email.com`
- Proveedores: CUIT formato `XX-XXXXXXXX-X`
- Productos: Todos tienen recetas completas
- Stock: Todos los insumos tienen stock suficiente para 1-2 producciones

**URLs Importantes:**
- Admin: http://127.0.0.1:8000/admin
- Catálogo Público: http://127.0.0.1:8000
- Mailpit: http://localhost:8025

---

## ✅ Verificación Final

Antes de grabar, ejecuta:
```bash
php artisan tinker --execute="
echo '=== VERIFICACION SISTEMA ===' . PHP_EOL;
echo 'Clientes: ' . \App\Models\Cliente::count() . ' (esperado: 8)' . PHP_EOL;
echo 'Productos: ' . \App\Models\Producto::count() . ' (esperado: 6)' . PHP_EOL;
echo 'Insumos: ' . \App\Models\Insumo::count() . ' (esperado: 10)' . PHP_EOL;
echo 'Pedidos Confirmados: ' . \App\Models\Pedido::where('status', 'confirmado')->count() . ' (esperado: 3)' . PHP_EOL;
echo 'Proveedores: ' . \App\Models\Proveedor::count() . ' (esperado: 3)' . PHP_EOL;
echo 'Lotes con Stock: ' . \App\Models\Lote::where('cantidad_actual', '>', 0)->count() . ' (esperado: 10+)' . PHP_EOL;
"
```

Si todos los números coinciden → **¡Listo para grabar!** 🎬
