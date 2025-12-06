# 🎬 GUIÓN PARA VIDEO DE DEMOSTRACIÓN (20-30 minutos)

## 📌 ESTRUCTURA GENERAL

1. **Introducción** (2 min)
2. **Caso de Uso UC-18: Gestión de Pedidos** (5 min)
3. **Caso de Uso UC-30→UC-21: Proceso de Compras** (5 min)
4. **Procesos Inteligentes Autónomos** (8 min)
5. **CRUDs con Validaciones** (7 min)
6. **Testing y Calidad** (3 min)
7. **Cierre** (1 min)

---

## 🎙️ GUIÓN DETALLADO

### 1. INTRODUCCIÓN (2 min)

**[Pantalla: Escritorio limpio]**

> "Hola, bienvenidos a la demostración del Sistema de Gestión para Pastelería, desarrollado como proyecto final de Ingeniería de Software."

> "Este sistema implementa gestión completa de ventas, compras, inventario y tres procesos inteligentes autónomos que ayudan al negocio a operar de manera más eficiente."

> "La demo está dividida en seis secciones:"
> - Dos casos de uso principales
> - Tres procesos inteligentes autónomos
> - Demostración de CRUDs con validaciones
> - Evidencia de testing

**[Acción: Abrir navegador en http://127.0.0.1:8000]**

> "Empecemos."

---

### 2. UC-18: GESTIÓN DE PEDIDOS (5 min)

**[Pantalla: Login admin]**

> "Primero voy a iniciar sesión como administrador."

**[Acción: Login con admin@test.com / password]**

---

#### **2.1 Crear Nuevo Pedido (2 min)**

**[Navegar: Admin → Pedidos → Create]**

> "Vamos a crear un pedido nuevo. Como pueden ver, el formulario permite seleccionar el cliente, agregar productos, definir forma de entrega y método de pago, todo en una sola pantalla."

**[Acción: Seleccionar cliente]**

> "Primero selecciono el cliente... noten que solo aparecen clientes activos, los clientes dados de baja no se muestran aquí."

**[Acción: Agregar productos]**

> "Ahora agrego productos al pedido. Selecciono 'Torta de Chocolate'..."

> "El sistema automáticamente trae el precio desde la variante del producto. Si cambio la cantidad, el subtotal se recalcula al instante."

**[Acción: Agregar segundo producto]**

> "Puedo agregar más productos haciendo clic en 'Agregar Producto'... agrego 'Cupcakes de Vainilla'..."

> "Observen que el total del pedido se actualiza automáticamente sumando todos los items."

**[Acción: Configurar entrega y pago]**

> "Selecciono fecha de entrega para dentro de 5 días, forma de entrega 'Retiro en local', método de pago 'Seña', y registro un monto abonado de, digamos, 3000 pesos."

> "El saldo pendiente se calcula automáticamente: total menos lo abonado."

**[Acción: Guardar]**

> "Guardo el pedido... y listo, el sistema lo crea con todos los items asociados."

---

#### **2.2 Ver Detalle del Pedido (1 min)**

**[Acción: Click en el pedido recién creado]**

> "En la vista de detalle puedo ver toda la información: cliente, items, totales, estado del pago..."

> "También hay acciones disponibles como generar PDF del pedido, cambiar estados, registrar pagos adicionales..."

---

#### **2.3 Generar PDF (1 min)**

**[Acción: Click en botón PDF]**

> "Generemos el PDF del pedido..."

**[Se abre PDF en nueva pestaña]**

> "Como ven, el PDF incluye toda la información profesional: datos de la pastelería, información del cliente, detalle de productos con precios, totales, y notas importantes."

> "Este PDF se puede enviar al cliente o imprimir para la producción."

---

#### **2.4 Cambiar Estado (1 min)**

**[Volver a la lista de pedidos]**

> "Desde la tabla puedo cambiar el estado del pedido..."

**[Acción: Cambiar de 'Pendiente' a 'En Producción']**

> "Lo marco como 'En Producción' para que el área de elaboración sepa que debe prepararlo."

> "Estos cambios de estado quedan registrados en la auditoría automática del sistema."

---

### 3. UC-30 + UC-21: PROCESO DE COMPRAS (5 min)

**[Navegar: Admin → Órdenes de Compra]**

> "Ahora veamos el flujo completo de compras, desde detectar necesidades hasta recepcionar mercadería."

---

#### **3.1 Análisis de Necesidades (1 min)**

**[Navegar: Admin → Insumos]**

> "Primero revisamos el inventario de insumos..."

**[Mostrar tabla con stock actual vs mínimo]**

> "El sistema muestra en colores los niveles de stock:"
> - Verde: stock suficiente
> - Amarillo: acercándose al mínimo
> - Rojo: stock crítico, requiere compra urgente

> "Por ejemplo, veo que 'Harina 0000' está en nivel crítico con solo 2,500 gramos disponibles de un mínimo de 5,000."

---

#### **3.2 Crear Orden de Compra (2 min)**

**[Navegar: Órdenes de Compra → Create]**

> "Voy a crear una orden de compra para reponer harina..."

**[Acción: Seleccionar proveedor]**

> "Selecciono el proveedor 'Distribuidora La Central' que es quien nos provee harinas..."

**[Acción: Agregar insumo]**

> "Agrego 'Harina 0000', cantidad: 20 kilogramos..."

> "El sistema convierte automáticamente las unidades. Noten que el proveedor vende por 'bultos de 25kg', pero yo puedo comprar 20kg y el sistema calcula que necesito 0.8 bultos."

> "El precio por kilo es de $150, entonces 20kg × $150 = $3,000 de subtotal."

**[Acción: Agregar más insumos si quieres]**

> "Puedo agregar más insumos a la misma orden... agrego 'Azúcar', 10kg..."

> "El total de la orden se actualiza automáticamente."

**[Acción: Guardar]**

> "Guardo la orden... Estado inicial es 'Borrador', porque todavía no la enviamos al proveedor."

---

#### **3.3 Confirmar y Enviar Orden (1 min)**

**[Acción: Cambiar estado a 'Enviada']**

> "Ahora cambio el estado a 'Enviada' para indicar que ya se envió al proveedor..."

**[Acción: Click en PDF]**

> "Puedo generar el PDF de la orden de compra para enviarla por email al proveedor..."

**[Se abre PDF]**

> "El PDF incluye: fecha, número de orden, datos del proveedor, items con cantidades y precios, y el total."

---

#### **3.4 Recepcionar Mercadería (1 min)**

**[Volver a la orden]**

> "Cuando llega la mercadería del proveedor, registro la recepción..."

**[Acción: Click en 'Recepcionar Mercadería']**

> "Marco los insumos recibidos, verifico cantidades, ingreso número de lote, fecha de vencimiento..."

**[Acción: Confirmar recepción]**

> "Al confirmar la recepción, el sistema:"
> - Actualiza automáticamente el stock de cada insumo
> - Crea los lotes con trazabilidad
> - Cambia el estado de la orden a 'Recibida'

**[Navegar a Insumos y mostrar stock actualizado]**

> "Si volvemos a la lista de insumos, vemos que el stock de 'Harina 0000' ahora es de 22,500 gramos, exactamente 20kg más que antes."

---

### 4. PROCESOS INTELIGENTES AUTÓNOMOS (8 min)

> "Ahora veamos las tres funcionalidades más innovadoras del sistema: los procesos inteligentes que funcionan de manera autónoma."

---

#### **4.1 Proceso #1: Planificación Automática de Compras (3 min)**

**[Pantalla: Terminal o comando artisan]**

> "El primer proceso inteligente analiza el inventario, detecta insumos en nivel crítico, proyecta demanda futura, evalúa proveedores, y genera órdenes de compra automáticamente."

**[Acción: Ejecutar comando]**

```bash
php artisan inteligente:procesar-compras
```

> "Ejecuto el comando del proceso inteligente..."

**[Mostrar output del comando]**

> "Como pueden ver, el proceso analizó 13 insumos, detectó 8 en nivel crítico, y generó 7 órdenes de compra automáticamente."

> "Por cada insumo crítico:"
> - Calculó cuánto comprar basándose en demanda proyectada
> - Evaluó qué proveedor ofrece mejor precio y cumplimiento
> - Generó la orden al mejor proveedor
> - Envió notificación por email

**[Navegar: Admin → Órdenes de Compra]**

> "Si revisamos las órdenes de compra, vemos las 7 órdenes nuevas generadas automáticamente por el sistema."

**[Click en una orden generada automáticamente]**

> "Noten que en las observaciones dice 'Orden generada automáticamente por el sistema inteligente de compras'."

**[Abrir Mailpit en http://127.0.0.1:8025]**

> "Y si revisamos el buzón de emails de prueba, vemos que se enviaron 7 notificaciones a los administradores informando sobre cada orden generada."

**[Mostrar un email]**

> "El email incluye detalles de la orden: qué insumo, cuánta cantidad, qué proveedor, y el análisis que hizo el sistema."

---

#### **4.2 Proceso #2: Promociones Inteligentes (2.5 min)**

**[Volver a terminal]**

> "El segundo proceso analiza los pedidos, identifica productos poco demandados o con ingredientes cerca de vencer, y genera promociones automáticamente."

**[Acción: Ejecutar comando]**

```bash
php artisan inteligente:generar-promociones
```

**[Mostrar output]**

> "El sistema analizó el histórico de ventas, identificó productos con baja rotación, calculó descuentos óptimos, y generó 4 promociones."

**[Navegar: Admin → Promociones]**

> "En el panel de promociones vemos las 4 promociones creadas automáticamente."

**[Click en una promoción]**

> "Por ejemplo, esta promoción ofrece 15% de descuento en 'Brownie con Nueces' porque el sistema detectó que:"
> - Tiene bajo volumen de ventas este mes
> - Utiliza chocolate que tiene un lote próximo a vencer en 10 días
> - El descuento incentiva ventas sin generar pérdida

> "Esta inteligencia permite reducir desperdicios y aumentar rotación de productos estratégicamente."

---

#### **4.3 Proceso #3: Análisis Comercial (2.5 min)**

**[Volver a terminal]**

> "El tercer proceso analiza patrones de compra, segmenta clientes, y genera reportes comerciales accionables."

**[Acción: Ejecutar comando]**

```bash
php artisan inteligente:analizar-comercial
```

**[Mostrar output]**

> "El sistema analizó el comportamiento de 10 clientes, segmentó por valor y frecuencia, y generó 3 insights comerciales."

> "Por ejemplo:"
> - "Cliente 'María González' es VIP con $45,000 en compras, recomendar programa de fidelización"
> - "3 clientes activos no compran hace 30 días, enviar campaña de reactivación"
> - "Producto 'Torta de Chocolate' representa 35% de ingresos, asegurar stock prioritario"

**[Navegar: Admin → Reportes o Dashboard]**

> "Estos insights se pueden visualizar en dashboards para que el gerente tome decisiones informadas."

> "Lo interesante de estos tres procesos es que corren automáticamente mediante tareas programadas. El dueño de la pastelería no necesita hacer nada, el sistema trabaja 24/7 optimizando el negocio."

---

### 5. CRUDs CON VALIDACIONES (7 min)

> "Ahora voy a demostrar las validaciones implementadas en los CRUDs. Voy a intentar realizar acciones inválidas para mostrar cómo el sistema las detecta y previene."

---

#### **5.1 Validaciones en Cliente (2 min)**

**[Navegar: Admin → Clientes → Create]**

> "Primero, creación de clientes..."

**[Acción: Intentar email duplicado]**

> "Intento crear un cliente con email 'maria.gonzalez@email.com' que ya existe..."

**[Mostrar error]**

> "El sistema rechaza el registro mostrando: 'Este email ya está registrado'."

**[Acción: Intentar teléfono inválido]**

> "Ahora pruebo con un teléfono inválido, por ejemplo '123'..."

**[Mostrar error]**

> "Valida que el teléfono tenga formato correcto y longitud mínima."

**[Acción: Crear cliente válido]**

> "Creo un cliente válido con datos correctos... funciona perfectamente."

**[Acción: Intentar modificar sin justificación]**

> "Ahora intento modificar el email del cliente sin ingresar justificación..."

**[Mostrar error]**

> "El sistema requiere justificación obligatoria para cambios importantes en datos de clientes, esto garantiza trazabilidad y cumplimiento de protección de datos."

**[Acción: Modificar con justificación]**

> "Ingreso justificación 'El cliente solicitó actualizar su email'... ahora sí permite el cambio."

---

#### **5.2 Validaciones en Proveedor (1.5 min)**

**[Navegar: Admin → Proveedores → Create]**

**[Acción: Intentar CUIT duplicado]**

> "Intento crear un proveedor con CUIT '20-12345678-9' que ya existe..."

**[Mostrar error]**

> "Rechaza por CUIT duplicado."

**[Acción: CUIT con formato inválido]**

> "Pruebo con CUIT mal formado '12-345'..."

**[Mostrar error]**

> "Valida formato estándar de CUIT argentino."

---

#### **5.3 Validaciones en Producto (1.5 min)**

**[Navegar: Admin → Productos → Create]**

**[Acción: Nombre duplicado]**

> "Intento crear producto 'Torta de Chocolate' que ya existe..."

**[Mostrar error]**

> "No permite nombres duplicados."

**[Acción: Precio en 0 o negativo]**

> "Intento precio $0..."

**[Mostrar error]**

> "Valida que el precio sea mayor a cero."

---

#### **5.4 Validaciones en Pedido (1 min)**

**[Navegar: Pedidos → Create]**

**[Acción: Fecha de entrega en el pasado]**

> "Intento fecha de entrega para ayer..."

**[Mostrar error]**

> "No permite fechas pasadas."

**[Acción: Pedido sin items]**

> "Intento guardar pedido sin agregar productos..."

**[Mostrar error]**

> "Requiere al menos un producto en el pedido."

---

#### **5.5 Validaciones en Insumo (1 min)**

**[Navegar: Admin → Insumos → Create]**

**[Acción: Stock mínimo mayor a máximo]**

> "Intento insumo con stock mínimo 1000 y máximo 500..."

**[Mostrar error]**

> "Valida que mínimo sea menor que máximo."

> "Estas son solo algunas de las 50+ validaciones implementadas en el sistema. Cada CRUD tiene sus reglas de negocio específicas."

---

### 6. TESTING Y CALIDAD (3 min)

**[Pantalla: Terminal o IDE]**

> "Finalmente, evidencia de testing automatizado."

**[Acción: Ejecutar tests]**

```bash
php artisan test
```

**[Mostrar output de tests corriendo]**

> "El sistema incluye tests automatizados que validan:"
> - Tests unitarios de lógica de negocio
> - Tests de feature para flujos completos
> - Tests de validaciones
> - Tests de conversión de unidades

**[Esperar a que terminen]**

> "Como pueden ver, corrieron [X] tests exitosos..."

**[Mostrar resumen verde de tests pasados]**

> "Todos los tests pasan, lo que da confianza en la estabilidad del sistema."

**[Mostrar coverage si está disponible]**

> "El coverage de código es de [X]%, cubriendo las funcionalidades críticas."

---

### 7. CIERRE (1 min)

**[Volver a vista general del sistema]**

> "Para resumir, este sistema implementa:"

> "✅ Gestión completa de ventas con pedidos, productos y clientes"
> "✅ Gestión de compras e inventario con trazabilidad"
> "✅ Tres procesos inteligentes autónomos que optimizan el negocio 24/7"
> "✅ Más de 50 validaciones robustas en todos los CRUDs"
> "✅ Generación de PDFs profesionales"
> "✅ Auditoría automática de todas las operaciones"
> "✅ Tests automatizados para calidad continua"

> "El código fuente está disponible en el repositorio Git compartido, junto con documentación técnica completa."

> "Gracias por su atención."

---

## 📝 NOTAS PARA LA GRABACIÓN

### Timing sugerido:
- Introducción: 2 min
- UC-18 Pedidos: 5 min
- UC-30/21 Compras: 5 min
- Procesos Inteligentes: 8 min
- CRUDs con validaciones: 7 min
- Testing: 3 min
- Cierre: 1 min
- **TOTAL: ~31 minutos**

### Tips:
1. **Habla claro y pausado** - No corras, el video puede durar 30 min
2. **Muestra, no solo digas** - Cada acción debe verse en pantalla
3. **Usa frases de transición**: "Ahora veamos...", "Como pueden observar...", "Esto demuestra que..."
4. **Si hay un error inesperado**: Di "Esto es un bug conocido que..." o "Voy a reintentar..." (no te detengas)
5. **Practica antes** - Haz un ensayo completo para calcular tiempos
6. **Ten el script a la vista** - Pero no lo leas robóticamente, úsalo como guía
7. **Sonríe ocasionalmente** - Transmite confianza en tu trabajo

### Preparación previa:
- ✅ Ejecutar `preparar_demo.bat`
- ✅ Tener todas las tabs/ventanas listas
- ✅ Cerrar notificaciones y apps innecesarias
- ✅ Configurar resolución 1920x1080
- ✅ Fuente/zoom adecuado para que se vea bien
- ✅ Micrófono testeado y sin ruido ambiente

### Contingencias:
- Si un comando falla: "Voy a ejecutarlo nuevamente..." o sigue con el siguiente punto
- Si el sistema está lento: "Esto puede tomar unos segundos..." y espera sin pánico
- Si olvidas algo: Puedes editarlo después o mencionar "Como vimos anteriormente..."

¡Éxito con tu demo! 🎉
