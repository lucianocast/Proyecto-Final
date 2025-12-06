# 🍰 Sistema de Gestión para Pastelerías

## 📋 Documento de Requisitos del Sistema

| **Campo** | **Valor** |
|-----------|-----------|
| **Versión** | 01.00 |
| **Fecha** | 01/09/2025 |
| **Realizado por** | Castillo Mazo Andrés Luciano |
| **Realizado para** | Cliente |

---

## 🎯 Objetivos de la Iteración

Se debe hacer una lista con los objetivos que se esperan alcanzar con el software a desarrollar.


### OBJ–01: Gestionar insumos y materiales

| **Atributo** | **Detalle** |
|--------------|-------------|
| **Descripción** | El sistema deberá permitir registrar y controlar los insumos y materiales utilizados en la producción de pastelería (harinas, huevos, frutas, decoraciones, packaging), manteniendo la trazabilidad de sus movimientos, cantidades disponibles y fechas de vencimiento. |
| **Estabilidad** | ![Alta](https://img.shields.io/badge/Estabilidad-Alta-success) |
| **Comentarios** | Base fundamental para garantizar continuidad en la producción y reducir pérdidas por desperdicio o faltantes. |


OBJ–02 
Gestionar recetas y costos de producción
Descripción 
El sistema permitirá registrar y organizar las recetas de productos, asociándolas con sus insumos, cantidades y procesos de elaboración. A partir de esta información, calculará costos de producción y márgenes de rentabilidad de cada producto.
Estabilidad 
Media
Comentarios 
Debe ser flexible para adaptarse a distintos tipos de productos (tortas, cheesecakes, tartas, postres clásicos).


OBJ–03 
Administrar pedidos y ventas
Descripción 
El sistema deberá gestionar la toma, confirmación y seguimiento de pedidos de clientes, permitiendo definir fecha y hora de entrega, modalidad (retiro en local o delivery), forma de pago (seña, pago completo, contraentrega) y estado del pedido (pendiente, en preparación, listo, entregado, cancelado).
Estabilidad 
Alta
Comentarios 
ninguno


OBJ–04 
Gestionar agenda y planificación de producción
Descripción 
El sistema deberá disponer de una agenda digital para organizar turnos y fechas de entrega, registrar bloqueos por días no laborables y asignar pedidos a la producción disponible.
Estabilidad 
Alta
Comentarios 
Permite equilibrar carga de trabajo y mejorar la organización del personal en pastelerías individuales o franquicias.


OBJ–06 
Gestionar proveedores y compras
Descripción 
El sistema deberá registrar proveedores de insumos y materiales, mantener actualizado su historial de precios, condiciones comerciales y cumplimiento de entregas, y facilitar la generación y seguimiento de órdenes de compra.
Estabilidad 
Alta
Comentarios 
ninguno


Requisitos del Sistema
Requisitos de Información
Debe tener una lista de requisitos de almacenamientos y de restricciones de información que se haya identificado. 

IRQ–01 
Información de Insumos y Materiales
Objetivos asociados 
OBJ–01 – Gestionar insumos y materiales
OBJ–02 – Gestionar recetas y costos de producción
OBJ–04 – Gestionar agenda y planificación de producción
Requisitos asociados 
UC–02 – Gestionar Compras
UC–03 – Gestionar Producción
UC-03 - Gestionar Stock
Descripción 
El sistema deberá almacenar y mantener actualizada la información correspondiente a los insumos y materiales utilizados en la pastelería, garantizando la trazabilidad de su uso en producción y ventas.
Datos específicos 
Nombre del insumo o material.
Categoría (materia prima, material no comestible, empaque).
Unidad de medida (kg, litros, unidades, etc.).
Cantidad disponible.
Stock mínimo (umbral definido por el sistema o el usuario).
Fecha de vencimiento (si corresponde).
Proveedor habitual.
Precio unitario histórico.
Fecha de última compra.
Estado (disponible, reservado, vencido, dañado).
Observaciones.


Estabilidad 
alta 


Comentarios 
Este requisito es esencial para el control de inventarios y la optimización de la producción. Permite asegurar la disponibilidad de insumos en función de la demanda y mantener una trazabilidad completa desde la compra hasta su utilización en pedidos.





IRQ–02 
Información de Recetas y Costos de Producción
Objetivos asociados 
OBJ–02 – Gestionar recetas y costos de producción
OBJ–03 – Administrar pedidos y ventas
OBJ–04 – Gestionar agenda y planificación de producción
Requisitos asociados 
UC–01 – Gestionar Ventas
UC-03 - Gestionar Producción
UC–04 – Gestionar Stock
Descripción 
El sistema deberá almacenar y mantener actualizada la información de las recetas utilizadas en la elaboración de productos, así como sus costos asociados. Cada receta deberá vincularse con los insumos correspondientes para calcular consumos, costos y rendimientos de manera automatizada.
Datos específicos 
Nombre de la receta o producto asociado.
Lista de insumos requeridos (ingredientes y materiales auxiliares).
Cantidades y unidades de medida de cada insumo.
Costo unitario de insumos vinculados.
Costo total estimado de la receta.
Rendimiento (cantidad de porciones o productos que se obtienen).
Tamaños o moldes alternativos y sus variaciones de insumo.
Tiempo estimado de elaboración.
Archivos adjuntos (PDF, imágenes, enlaces).
Observaciones.


Estabilidad 
alta 


Comentarios 
Este requisito es fundamental para calcular costos de producción, controlar consumos de insumos y asegurar la correcta planificación de la producción. Permite además adaptar recetas a diferentes tamaños de pedidos y mantener trazabilidad de procesos.




IRQ–03
Información de Pedido y Ventas
Objetivos asociados 
OBJ–03 – Administrar pedidos y ventas
OBJ–05 – Gestionar clientes y fidelización
OBJ-04 Gestionar agenda y planificación de producción
Requisitos asociados 
UC–03 – Gestionar Ventas
Descripción 
El sistema deberá almacenar y mantener actualizada la información de los pedidos realizados por los clientes, incluyendo sus detalles, estados, pagos y entregas. Esta información servirá como base para la gestión de ventas, la organización de la producción y la fidelización de clientes.
Datos específicos 
Identificación del pedido.
Cliente asociado (datos básicos y contacto).
Productos solicitados (nombre, cantidad, personalización si corresponde).
Estado del pedido (pendiente, en producción, listo, entregado, cancelado).
Fecha y hora de entrega pactada.
Forma de entrega (retiro en local o envío).
Método de pago (total, seña, saldo pendiente).
Fecha de pago y monto abonado.
Observaciones del pedido.
Historial de modificaciones o cancelaciones.


Estabilidad 
alta 


Comentarios 
Este requisito es esencial para la operatividad del sistema, ya que permite registrar y seguir los pedidos de los clientes, controlar los pagos y organizar la producción en función de la demanda. Aporta trazabilidad completa en el ciclo de ventas.




IRQ–04
Información de Agenda y Planificación de Producción
Objetivos asociados 
OBJ–04 – Gestionar agenda y planificación de producción
OBJ–02 – Gestionar recetas y costos de producción
OBJ–03 – Administrar pedidos y ventas
Requisitos asociados 
UC–03 – Gestionar Producción
Descripción 
El sistema deberá almacenar y mantener actualizada la información relacionada con la agenda y la planificación de la producción, vinculando pedidos confirmados, disponibilidad de insumos y tiempos de elaboración. Permitirá además registrar bloqueos de calendario para días no laborables o sin disponibilidad de producción.
Datos específicos 
Identificación del pedido y productos asociados.
Fecha y hora programada de entrega.
Tiempo estimado de producción por pedido.
Relación con recetas vinculadas y sus consumos de insumos.
Bloqueos de calendario (motivo, fecha, responsable).
Estado de avance de producción (pendiente, en proceso, listo).
Capacidad estimada de carga de trabajo por día.
Observaciones y comentarios de planificación.


Estabilidad 
alta 


Comentarios 
Este requisito es fundamental para garantizar una organización eficiente de la producción y asegurar el cumplimiento de entregas. Permite coordinar insumos, recetas y pedidos en un cronograma unificado, reduciendo riesgos de incumplimientos.




IRQ–06
Información de Proveedores y Compras
Objetivos asociados 
OBJ–06 – Gestionar proveedores y compras
OBJ–01 – Gestionar insumos y materiales
OBJ–04 – Gestionar agenda y planificación de producción
Requisitos asociados 
UC–02 – Gestionar Compras
Descripción 
El sistema deberá almacenar y mantener actualizada la información de proveedores y de las compras realizadas, asegurando la trazabilidad del abastecimiento de insumos y materiales no comestibles. Permitirá registrar órdenes de compra, recepciones y condiciones comerciales.
Datos específicos 
Identificación del proveedor (nombre, razón social).
Datos de contacto (teléfono, correo electrónico, dirección).
Tipo de insumos o materiales que provee.
Historial de precios y condiciones de compra.
Órdenes de compra registradas (número, fecha, monto, estado).
Fechas de recepción de insumos y cantidades entregadas.
Relación con insumos comprados (detalle de productos adquiridos).
Estado de la compra (pendiente, recibida parcial, recibida total, cancelada).
Observaciones y notas sobre desempeño del proveedor.


Estabilidad 
alta 


Comentarios 
Este requisito es esencial para garantizar la trazabilidad del abastecimiento y la confiabilidad en la relación con los proveedores. Permite optimizar compras, controlar precios históricos y planificar la producción de acuerdo con la disponibilidad de insumos.




IRQ–10
Información de Productos
Objetivos asociados 
OBJ–02 – Gestionar recetas y costos de producción
OBJ–03 – Administrar pedidos y ventas
OBJ–04 – Gestionar agenda y planificación de producción
Requisitos asociados 
UC–01 – Gestionar Ventas
UC–03 – Gestionar Producción
Descripción 
El sistema deberá almacenar y mantener actualizada la información de los productos ofrecidos en el catálogo de la pastelería, permitiendo su vinculación con recetas, precios, variaciones de tamaño y estado de disponibilidad. Esta información servirá como base para la gestión de ventas, el control de producción y el cálculo de costos.
Datos específicos 
Nombre del producto.
Categoría (tortas, tartas, cheesecakes, postres, combos).
Descripción breve.
Precio de venta.
Tamaño/medida (ejemplo: 20 cm, 1 kg, porción).
Receta asociada (referencia a RI–02).
Imagen o archivo ilustrativo.
Estado (activo, inactivo, en oferta).
Etiquetas opcionales (destacado, promoción, sin TACC, etc.).
Observaciones.


Estabilidad 
alta 


Comentarios 
Este requisito es esencial para mantener un catálogo actualizado y confiable, garantizando que los pedidos de clientes estén vinculados a productos disponibles y correctamente valorados. Permite también realizar análisis de ventas y márgenes de rentabilidad basados en productos concretos.




Requisitos Funcionales
Debe tener una lista de los requisitos funcionales, expresado en forma tradicional o mediante casos de usos 

RF-01 
Gestionar insumos y materiales
OBJ asociados
OBJ–01, OBJ–02
RI asociados 
RI–01
Descripción
El sistema deberá permitir registrar, modificar, desactivar y consultar los insumos y materiales utilizados en la producción de pastelería, incluyendo materias primas, elementos de decoración y materiales de empaque.
Estabilidad
Alta
Comentarios 
ninguno 


RF-02
Gestionar recetas
OBJ asociados
OBJ–02
RI asociados 
RI–02
Descripción
El sistema deberá permitir registrar, modificar, desactivar y consultar recetas, vinculando cada una con los insumos correspondientes para calcular costos y rendimientos.
Estabilidad
Alta
Comentarios 
ninguno 


RF-03
Gestionar pedidos y pagos de clientes
OBJ asociados
OBJ–03
RI asociados 
RI–03
Descripción
El sistema deberá permitir registrar, modificar/cancelar, consultar pedidos y gestionar pagos (totales o parciales), con estados actualizados.
Estabilidad
Alta
Comentarios 
ninguno 


RF-04
Gestionar proveedores y compras
OBJ asociados
OBJ–06
RI asociados 
RI–06
Descripción
El sistema deberá registrar, modificar, desactivar y consultar proveedores, así como registrar y consultar compras realizadas, vinculandose con insumos adquiridos.
Estabilidad
Alta
Comentarios 
ninguno 


RF-05
Gestionar stock de insumos y materiales 
OBJ asociados
OBJ–01
RI asociados 
RI–01
Descripción
El sistema deberá permitir consultar el stock disponible, ajustar manualmente cantidades y consultar el historial de movimientos de stock (entradas, salidas y ajustes).
Estabilidad
Alta
Comentarios 
ninguno 


RF-06
Gestionar productos
OBJ asociados
OBJ–02 y OBJ-03
RI asociados 
RI–02 y RI-10
Descripción
El sistema deberá gestionar los productos del catálogo, incluyendo el registro de nuevos productos, la modificación de datos existentes, la desactivación de productos no disponibles y la consulta de información detallada.
Estabilidad
Alta
Comentarios 
ninguno 


Diagrama de Casos de Usos



Figura 2 : Diagrama de Caso de Uso del Subsistema Gestión de Socios

Definición de Actores

ACT–01 
Administrador 
Descripción 
Representa al usuario con el máximo nivel de acceso, probablemente la dueña o pastelera principal. Es responsable de la configuración general del sistema, la gestión de la seguridad (usuarios y roles), la supervisión de la auditoría y la consulta de reportes y análisis estratégicos.
Comentarios 
Ninguno
 



ACT–02
Encargado
Descripción 
Este actor representa al rol responsable de la gestión de la producción y el abastecimiento. Sus tareas incluyen gestionar el stock de insumos , registrar y modificar recetas, planificar la producción , administrar los proveedores y generar las órdenes de compra.
Comentarios 
Ninguno
 



ACT–03 
Vendedor
Descripción 
Representa al rol que gestiona la interacción directa con el cliente. Se encarga de tomar, modificar y consultar pedidos, registrar clientes y gestionar el proceso de venta, incluyendo los pagos.
Comentarios 
Ninguno
 



ACT–04 
Cliente
Descripción 
Actor externo al sistema. Representa a la persona que realiza los pedidos. El sistema gestiona su información personal, su historial de pedidos y las promociones que se le aplican.
Comentarios 
Ninguno



ACT–05 
Proveedor
Descripción 
Actor externo al sistema. Representa a la entidad o persona que abastece de insumos y materiales a la pastelería. El sistema gestiona su información de contacto y su historial de compras.
Comentarios 
Ninguno



ACT–06 
Mercado Pago
Descripción 
Representa un sistema externo (actor no humano). Es la pasarela de pagos que se integra con el "Subsistema de Ventas" para procesar y registrar los pagos de los pedidos.
Comentarios 
Ninguno
 

Caso de Usos del Sistema

UC–01
Registrar pedido  
Objetivos 
asociados 
OBJ–03 – Administrar pedidos y ventas.
OBJ–04 – Gestionar agenda y planificación de producción.
OBJ–05 – Gestionar clientes y fidelización
Requisitos 
asociados 
IRQ–03 – Información de Pedido y Ventas.
IRQ–04 – Información de Agenda y Planificación de Producción
IRQ–06 – Información de Clientes.
IRQ–10 – Información de Productos.
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite registrar un pedido.
Precondición 
El usuario debe estar registrado en el sistema.
El producto solicitado debe estar registrado y activo en el catálogo.
Debe existir disponibilidad de fecha y hora en la agenda de producción.
Secuencia 
Paso 
Acción 
normal 
1 
El usuario solicita al sistema comenzar el proceso de registrar un pedido.








2 
El usuario carga los datos del pedido, contemplando:
– Cliente asociado (UC–39 Buscar cliente)
– Productos solicitados (UC–44 Ver catálogo de producto)
– Cantidad por producto
– Precio unitario acordado
– Observaciones por ítem / personalización
– Modalidad de entrega (retiro o envío)
– Fecha/hora prevista de entrega o retiro
– Seña/anticipo (si corresponde)
– Estado del pedido (pendiente o confirmado).


3 
El sistema valida la información ingresada y crea una nueva instancia de pedido con los datos proporcionados.


4 
El sistema informa al usuario que el proceso ha finalizado con éxito y registra el pedido en el historial.






Postcondición 
El pedido queda registrado en el sistema con estado inicial “pendiente” o “confirmado”.
El pedido queda vinculado a la agenda de producción y al historial del cliente.
Excepciones 
Paso 
Acción 


2
Si el pedido ya se encuentra registrado, el sistema informa al usuario y este decide si continuar o cancelar la operación.
Si el producto no está en catálogo, el sistema informa la situación y finaliza el proceso.
Si no hay disponibilidad en la fecha/hora seleccionada, el sistema solicita modificar los datos antes de confirmar.




Rendimiento 
Paso 
Cota de tiempo 


4 
1 segundo 
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 
La información impacta en producción, ventas y stock.




UC–02
Modificar pedido  
Objetivos 
asociados 
OBJ–03 – Administrar pedidos y ventas.
OBJ–04 – Gestionar agenda y planificación de producción.
OBJ–05 – Gestionar clientes y fidelización
Requisitos 
asociados 
IRQ–03 – Información de Pedido y Ventas.
IRQ–04 – Información de Agenda y Planificación de Producción
IRQ–06 – Información de Clientes.
IRQ–10 – Información de Productos.
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite registrar un pedido.
Precondición 
El cliente debe estar registrado en el sistema.
El producto solicitado debe estar registrado y activo en el catálogo.
Debe existir disponibilidad de fecha y hora en la agenda de producción.
Secuencia 
Paso 
Acción 
normal 
1 
El usuario solicita al sistema comenzar el proceso de modificar un pedido.


2
Se ejecuta UC–04 Consultar pedidos para localizar el pedido a modificar.


3
El sistema muestra los datos del pedido seleccionado.


4
El usuario actualiza la información requerida, pudiendo modificar:
– Productos solicitados (UC–44 Ver catálogo de producto)
– Cantidad por producto
– Precio unitario acordado
– Observaciones por ítem
– Modalidad de entrega (retiro/envío)
– Fecha/hora prevista de entrega
– Seña/anticipo (si corresponde)
– Estado del pedido (pendiente/confirmado).


5
El sistema valida los cambios, actualiza la instancia del pedido y guarda la modificación.


6 
El sistema informa al usuario que la modificación se ha realizado con éxito.






Postcondición 
El pedido queda actualizado en el sistema con los nuevos datos registrados.
El historial de modificaciones del pedido se conserva para trazabilidad.
Excepciones 
Paso 
Acción 


2
Si el pedido no existe, el sistema informa al usuario y el proceso queda sin efecto.






4
Si el pedido se encuentra cancelado, entregado o en producción, el sistema informa que no es posible modificarlo.
Si la nueva fecha/hora de entrega no está disponible en la agenda, el sistema solicita elegir otra alternativa.
Rendimiento 
Paso 
Cota de tiempo 


2
2 segundos 


4
1 segundo
Frecuencia 
Media
Estabilidad 
alta 
Comentarios 
Este caso de uso permite corregir errores o cambios solicitados por clientes. Garantiza la trazabilidad al conservar un historial de modificaciones.



UC–03
Cancelar pedido  
Objetivos 
asociados 
OBJ–03 – Administrar pedidos y ventas.
OBJ–04 – Gestionar agenda y planificación de producción.
OBJ–05 – Gestionar clientes y fidelización
Requisitos 
asociados 
IRQ–03 – Información de Pedido y Ventas.
IRQ–04 – Información de Agenda y Planificación de Producción
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite registrar un pedido.
Precondición 
El pedido debe estar registrado en el sistema
El pedido no debe estar entregado
El pedido no debe estar en estado de listo para entregar 
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema comenzar el proceso de cancelar un pedido.








2 
Se ejecuta UC-04 Consultar pedidos para localizar el pedido a cancelar.


3 
El sistema muestra la información del pedido seleccionado.


4 
El usuario confirma la cancelación de pedido


5
El sistema cambia el estado del pedido a “cancelado” y registra la fecha y motivo de cancelación.


6
El sistema informa al usuario que el pedido ha sido cancelado con éxito.






Postcondición 
El pedido queda registrado en el sistema como “cancelado”
La agenda de producción y el stock asociado se actualizan en consecuencia. 
Excepciones 
Paso 




2
Si el pedido no existe, el sistema informa al usuario y finaliza el caso de uso.






4
Si el pedido está en ya fue entregado, el sistema informa que no es posible cancelarlo


5
Si la cancelación implica devolución de dinero, se ejecuta UC-15 devolución
Rendimiento 
Paso 




5 
1 segundo
Frecuencia 
Media
Estabilidad 
alta 
Comentarios 
La cancelación de un pedido es crítica para mantener la consistencia de la planificación de producción y control financiero. Puede activar procesos relacionados como devoluciones o reasignaciones de turnos de producción.


UC–04
Consultar pedido  
Objetivos 
asociados 
OBJ–03 – Administrar pedidos y ventas.
OBJ–04 – Gestionar agenda y planificación de producción.
OBJ–05 – Gestionar clientes y fidelización
Requisitos 
asociados 
IRQ–03 – Información de Pedido y Ventas.
IRQ–04 – Información de Agenda y Planificación de Producción
IRQ–06 – Información de Clientes.
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite registrar un pedido.
Precondición 
Deben existir pedidos registrados en el sistema.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema comenzar el proceso de consultar pedidos








2 
El usuario define criterios de búsqueda y/o filtros, pudiendo seleccionar:
Cliente asociado (UC 36 - Buscar cliente)
– Estado del pedido (pendiente, confirmado, en proceso, listo, cancelado, entregado)
– Rango de fechas de entrega
– Modalidad de entrega (retiro/envío).


3 
El sistema busca en la base de datos y muestra los pedidos que cumplen con los criterios ingresados.


4 
El usuario selecciona un pedido para visualizar sus detalles.


5
El sistema devuelve toda la información del pedido seleccionado (productos, cantidades, precios, pagos, estado, historial de cambios).
Postcondición 
El usuario visualiza la información completa del pedido seleccionado.
El pedido puede ser modificado o cancelado desde esta consulta, de acuerdo con otros casos de uso.
Excepciones 
Paso 




3
Si no existen pedidos que coincidan con los criterios de búsqueda, el sistema informa al usuario que no se encontraron resultados.




Rendimiento 
Paso 




4 


Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 
La información impacta en producción, ventas y stock.


UC–05
Registrar venta  
Objetivos 
asociados 
OBJ–03 – Administrar pedidos y ventas.
OBJ–05 – Gestionar clientes y fidelización
Requisitos 
asociados 
IRQ–03 – Información de Pedidos y Ventas.
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite registrar una venta.
Precondición 
El usuario debe estar registrado en el sistema.
El producto solicitado debe estar registrado y activo en el catálogo.
El Pedido asociado debe estar registrado y listo para el cobro.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Registrar una venta 


2
El usuario selecciona el pedido asociado (UC-04 Consultar pedidos).


3 
El sistema muestra los datos del pedido: cliente, productos, cantidades, precios y observaciones. 


4 
El usuario selecciona el Método de Pago (Efectivo, Tarjeta, Transferencia, o Mercado Pago)


5 
[Si es Mercado Pago] El sistema invoca la API de Mercado Pago, redirigiendo al Cliente para la autorización del pago. (Incluye: UC-09 Registrar pago).
[Para otros medios] El Vendedor registra el monto final recibido y confirma que la transacción externa (POS, efectivo) fue exitosa.


6
El sistema valida el pago y crea la Venta registrando el medio y el importe final.


7
El sistema emite el comprobante de venta e informa al usuario que el proceso se realizó con éxito.






Postcondición 
Se registra la instancia de Venta como "cerrada" y queda vinculada al Pedido.


Excepciones 
Paso 




2
Si el Pedido está en estado "Cancelado" o "Pendiente" (no listo para entrega), el sistema notifica y no permite registrar la venta.






5
Si la transacción con la API de Mercado Pago falla o es rechazada, el sistema notifica al usuario y mantiene el estado del Pedido y la Venta sin cambios.
Si el Vendedor intenta registrar un monto de cobro diferente al saldo pendiente sin una justificación, el sistema emite una advertencia
Rendimiento 
Paso 




6 
2 segundos
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 
Este CU gestiona el aspecto financiero y la finalización de la entrega.


UC–06
Modificar venta
Objetivos 
asociados 
OBJ–03 – Administrar pedidos y ventas.


Requisitos 
asociados 
IRQ–03 – Información de Pedido y Ventas.


Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite modificar una venta.
Precondición 
El usuario debe estar registrado en el sistema.
El producto solicitado debe estar registrado y activo en el catálogo.
La Venta debe estar registrada en el sistema.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Modificar venta.


2
El usuario busca y selecciona la Venta que desea modificar. (Incluye: UC-08 Consultar ventas).


3
El sistema muestra los detalles de la Venta (productos, total, medio de pago, fecha, etc.) y los campos editables.


4 
El usuario modifica los campos permitidos (ej., el medio de pago si fue mal registrado).


5 
El sistema solicita al usuario que ingrese una justificación para la modificación y confirma los cambios.


6
El sistema actualiza el registro de la Venta, aplica los ajustes financieros correspondientes (si los hay) y registra el cambio en el log de auditoría.
El sistema informa al usuario que el proceso se completó con éxito.






Postcondición 
Los datos financieros de la Venta se actualizan.
Se registra una entrada en el Log de Auditoría indicando el usuario, fecha y el detalle del cambio realizado.
Excepciones 
Paso 




2
Si la Venta no es encontrada, el sistema informa y finaliza el caso de uso.






5
Si el usuario no proporciona una justificación, el sistema no permite guardar los cambios.
Rendimiento 
Paso 




6 
2 segundos
Frecuencia 
Baja
Estabilidad 
Media
Comentarios 
Este CU está fuertemente vinculado al control interno. Las modificaciones deben ser mínimas y auditables para evitar fraudes o inconsistencias en los reportes financieros.


UC–07
Anular venta
Objetivos 
asociados 
OBJ–03 – Administrar pedidos y ventas.


Requisitos 
asociados 
IRQ–03 – Información de Pedido y Ventas.
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite Anular una venta.
Precondición 
El cliente debe estar registrado en el sistema.
El producto solicitado debe estar registrado y activo en el catálogo.
Debe existir disponibilidad de fecha y hora en la agenda de producción.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Anular una venta


2
El usuario busca y selecciona la Venta que desea cancelar. (Incluye: UC-08 Consultar ventas)


3 
El sistema muestra los detalles de la Venta y solicita una confirmación de seguridad


4 
El usuario confirma y proporciona una justificación detallada del motivo de la anulación (ej., error de cobro, devolución).


5
El sistema anula el registro de Venta (cambiando su estado a "anulado")


6
El sistema actualiza el estado del Pedido asociado a un estado previo al cierre (ej., "listo para entregar" o "cancelado" según el motivo de anulación).


7
El sistema registra la anulación en el Log de Auditoría, detallando usuario, Venta afectada y justificación. 
El sistema informa al usuario que el proceso de cancelación ha finalizado con éxito.
Postcondición 
La Venta y los Pagos asociados cambian su estado a "anulado".
El Estado del Pedido se revierte o cambia a "cancelado". 
Se genera un registro inmutable en el Log de Auditoría.
Excepciones 
Paso 




2
Si la Venta deseada ya se encuentra en estado "cancelada", el sistema informa y finaliza el caso de uso.






4
Si la justificación de la cancelación no es ingresada, el sistema no permite avanzar.
Rendimiento 
Paso 




4 


Frecuencia 
Baja
Estabilidad 
alta 
Comentarios 
-


UC–08
Consultar ventas
Objetivos 
asociados 
OBJ–03 – Administrar pedidos y ventas.
Requisitos 
asociados 
IRQ–03 – Información de Pedido y Ventas.
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite consultar ventas.
Precondición 
El usuario debe estar autenticado.
Deben existir registros de Ventas en el sistema.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Consultar ventas.


2
El sistema muestra un listado inicial de las ventas y presenta los criterios de búsqueda y filtros.


3
El usuario ingresa o selecciona los criterios de búsqueda (ej., Rango de fechas, Vendedor, Método de Pago, Pedido asociado, Monto total)


4
El sistema ejecuta la consulta en la base de datos y presenta la lista de resultados que cumplen con los criterios.


5 
El usuario selecciona una Venta específica del listado para ver su detalle


6 
El sistema muestra la información completa de la Venta (detalle de productos, monto total, desglose de pagos, método de pago final, y el estado del Pedido asociado).


7 
El usuario puede seleccionar una opción para Exportar la información a un formato externo
El usuario finaliza la consulta.






Postcondición 
 El usuario obtiene una vista detallada de las Ventas registradas
Excepciones 
Paso 




4
Si ninguna Venta cumple con los criterios de búsqueda, el sistema informa al usuario y solicita nuevos criterios


5
Si la exportación de datos falla, el sistema informa del error al usuario.




Rendimiento 
Paso 




4 
3 segundos
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 
Este CU es fundamental para la toma de decisiones y la auditoría. Es un caso de uso base que será incluido por otros CU como UC-06 Modificar Venta o UC-07 Cancelar Venta.


UC–09
Registrar pago
Objetivos 
asociados 
OBJ–03 – Administrar pedidos y ventas.


Requisitos 
asociados 
IRQ–03 – Información de Pedido y Ventas.
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite registrar un pago.
Precondición 
El usuario debe estar autenticado. 
El pedido asociado debe existir y tener un saldo pendiente mayor a cero.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Registrar pago.








2 
El usuario selecciona el pedido al que se aplicará el pago. (UC-04 Consultar pedidos)


3 
El sistema muestra el estado actual del pedido, el monto total y el saldo pendiente.


4 
El usuario indica el monto a pagar y selecciona el Método de Pago (Efectivo, Tarjeta, Transferencia, o Mercado Pago)


5
[Si es Mercado Pago] El sistema invoca la API de Mercado Pago, redirigiendo al Cliente para la autorización del pago.
6 | [Para otros medios] El Vendedor registra la confirmación del pago recibido.


6
El sistema valida la transacción y crea un registro de Pago, asociándolo al Pedido con el monto y medio de pago.


7
El sistema informa al usuario que el pago se realizó con éxito 
Postcondición 
Se crea un nuevo registro de Pago con el detalle de la transacción.
El Saldo Pendiente del Pedido disminuye
Excepciones 
Paso 




4
Si el monto a pagar es superior al saldo pendiente, el sistema emite una alerta y solicita confirmar si se trata de un error.






5
Si la transacción con la API de Mercado Pago es rechazada, el sistema informa del error y el pago no se registra.
Rendimiento 
Paso 




6 
2 segundos
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 
Este CU es la base de la gestión financiera. Se diferencia de UC-05 Registrar venta en que este solo registra el pago, mientras que el UC-05 es la acción final que usa este CU para cerrar el pedido a "entregado".


UC–10
Modificar pago  
Objetivos 
asociados 
OBJ–03 – Administrar pedidos y ventas.
Requisitos 
asociados 
IRQ–03 – Información de Pedido y Ventas.


Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite modificar un pago.
Precondición 
El usuario debe estar registrado en el sistema.
El pago debe existir y no estar en estado "anulado". 
El pedido asociado al pago debe existir.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Modificar pago.


2
El usuario busca y selecciona el Pedido asociado, y luego selecciona el Pago específico que desea modificar. (Incluye: UC-04 Consultar pedidos y/o UC-12 Consultar estado de pago)


3 
El sistema muestra los detalles del Pago (monto, fecha, método) y los campos editables.


3 




4 








Postcondición 
El pedido queda registrado en el sistema con estado inicial “pendiente” o “confirmado”.
El pedido queda vinculado a la agenda de producción y al historial del cliente.
Excepciones 
Paso 




2






Rendimiento 
Paso 




4 


Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 
La información impacta en producción, ventas y stock.


UC–11
Anular pago  
Objetivos 
asociados 
OBJ–03 – Administrar pedidos y ventas.
OBJ–04 – Gestionar agenda y planificación de producción.
OBJ–05 – Gestionar clientes y fidelización
Requisitos 
asociados 
IRQ–03 – Información de Pedido y Ventas.
IRQ–04 – Información de Agenda y Planificación de Producción
IRQ–06 – Información de Clientes.
IRQ–10 – Información de Productos.
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite registrar un pedido.
Precondición 
El usuario debe estar registrado en el sistema.
El Pago debe existir y estar en estado "registrado" o "confirmado".
El pedido asociado al pago debe existir.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Anular pago.


2
El usuario busca y selecciona el Pedido asociado, y luego selecciona el Pago específico que desea anular. (Incluye: UC-04 Consultar pedidos y/o UC-12 Consultar estado de pago).


3 
El sistema muestra los detalles del Pago y solicita una confirmación de seguridad.


4 
El usuario confirma la anulación e ingresa una justificación obligatoria (ej., error de transferencia, devolución al cliente).


5
El sistema anula el registro de Pago (cambiando su estado a "anulado").


6
El sistema registra la anulación en el Log de Auditoría y, si el pago era el saldo final, revierte el estado del pedido a "listo para entregar" (o similar).


7
El sistema informa al usuario que el proceso se completó con éxito.






Postcondición 
El registro de Pago cambia su estado a "anulado".
Se genera un registro inmutable en el Log de Auditoría
Excepciones 
Paso 




2
Si el Pago ya está en estado "anulado", el sistema informa al usuario y finaliza.






4
Si la justificación no es ingresada, el sistema impide continuar con la anulación.
Rendimiento 
Paso 




4 


Frecuencia 
Baja
Estabilidad 
alta 
Comentarios 
Este CU es altamente sensible. La reversión del saldo es crítica para la precisión financiera. Es fundamental que la anulación de un pago vinculado a una Venta ya cerrada exija la anulación previa de la Venta (UC-07).


UC–12
Consultar estado de pago  
Objetivos 
asociados 
OBJ–03 – Administrar pedidos y ventas.
Requisitos 
asociados 
IRQ–03 – Información de Pedido y Ventas.


Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite consultar el estado de pago.
Precondición 
El usuario debe estar registrado en el sistema.
Deben existir Pedidos registrados con transacciones de pago asociadas


Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Consultar estado de pago.


2
El usuario busca y selecciona el Pedido sobre el cual quiere consultar el estado de pagos. (Incluye: UC-04 Consultar pedidos).


3 
El sistema muestra la información financiera del Pedido: Monto total, Monto pagado hasta la fecha y Saldo Pendiente.


4 
El sistema presenta un historial detallado de todas las transacciones de Pago vinculadas a ese Pedido. Por cada Pago, el sistema muestra: Fecha, Monto, Método de Pago (Efectivo, Mercado Pago, etc.), ID de Transacción (si aplica) y el Estado del Pago ("registrado", "anulado", "pendiente de conciliación").


5
El usuario puede seleccionar la opción para imprimir o exportar el resumen del estado de pagos del Pedido.


6
El usuario finaliza la consulta.






Postcondición 


Excepciones 
Paso 




2
Si el Pedido no es encontrado, el sistema informa y solicita nuevos criterios de búsqueda.






4
Si el Pedido no tiene pagos registrados (saldo pendiente igual al total), el sistema informa que no hay historial de pagos y muestra solo el saldo.
Rendimiento 
Paso 




3 
2 segundos 
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 




UC–13
Registrar devolución/reintegro 
Objetivos 
asociados 
OBJ–03 – Administrar pedidos y ventas.


Requisitos 
asociados 
IRQ–03 – Información de Pedido y Ventas.


Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite registrar una devolución/reintegro.
Precondición 
El usuario debe estar registrado en el sistema.
El producto solicitado debe estar registrado y activo en el catálogo.
Debe existir disponibilidad de fecha y hora en la agenda de producción.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Registrar devolución/reintegro.


2
El usuario busca y selecciona la Venta que será objeto de la devolución


3 
El sistema muestra los detalles de la Venta y solicita la confirmación de devolución (total o parcial)


4 
El usuario indica el monto a reintegrar y el motivo de la devolución ( obligatorio, ej., producto dañado, error del pedido)


5 
El usuario indica si el producto físico será reingresado a stock (ej., si es reventa) o si se desecha. 


6
El sistema ejecuta la anulación: Anula la Venta (UC-07) o crea un Ajuste de Venta para la parte devuelta. Luego, Anula el Pago (UC-11) y registra el reverso financiero (la salida de dinero).


7
El sistema actualiza el estado del Pedido asociado a "Devuelto" (o similar) y registra la transacción completa en el Log de Auditoría.


8
El sistema informa al usuario que el proceso ha finalizado con éxito.






Postcondición 
La venta original se revierte. 
El pago asociado se anula y el reveso financiero se registra.
Se registra la justificación de la devolución.
Excepciones 
Paso 




2
Si la Venta ya está en estado "Devuelta" o "Anulada", el sistema informa y finaliza.






4
Si la justificación de la devolución no es ingresada, el sistema impide continuar.


Rendimiento 
Paso 




6 
3 segundos 
Frecuencia 
Baja
Estabilidad 
alta 
Comentarios 
Este CU es una combinación de UC-07 Cancelar venta y UC-11 Cancelar pago, pero añade la lógica de registrar la razón de la devolución y el potencial reingreso de stock (si el producto es apto).


UC–14
Registrar orden de compra
Objetivos 
asociados 
OBJ–01 – Gestionar insumos y materiales.
Requisitos 
asociados 
IRQ–01 – Información de Insumos y Materiales.
IRQ–06 – Información de Proveedores y Compras.
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite registrar una orden de compra.
Precondición 
El usuario debe estar registrado en el sistema.
El proveedor debe estar previamente registrado en el sistema.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Registrar orden de compra.


2
El sistema presenta el formulario y el usuario ingresa la fecha de orden y la fecha de recepción esperada.


3 
El usuario selecciona el proveedor al que se dirigirá la compra. 


4 
El usuario selecciona los insumos a comprar y la cantidad requerida para cada uno.


5
El sistema consulta automáticamente el precio unitario del insumo asociado al proveedor seleccionado (o el precio histórico más reciente)


6 
El usuario confirma la orden, el sistema crea la orden de compra con el estado inicial “pendiente de recepción”


7 
El sistema informa al usuario que el proceso ha finalizado con éxito.






Postcondición 
Se registra una nueva instancia de Orden de Compra con estado "Pendiente de Recepción". 
La compra queda asociada al Proveedor para la evaluación de desempeño
Excepciones 
Paso 




3
Si el Proveedor no existe, el sistema solicita registrarlo (invoca UC-16 Registrar proveedor en compra) o finalizar






5
Si el sistema no encuentra un precio histórico para el insumo y proveedor, solicita al usuario ingresar manualmente el precio unitario actual antes de continuar.
Rendimiento 
Paso 




4 


Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 




UC–15
Modificar orden de compra
Objetivos 
asociados 
OBJ–01 – Gestionar Insumos y Materiales.
Requisitos 
asociados 
IRQ–01 – Información de Insumos y Materiales.
IRQ–06 – Información de Proveedores y Compras.
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite modificar una orden de compra.
Precondición 
El usuario debe estar registrado en el sistema.
La orden de compra debe existir y estar en estado “Pendiente de recepción” 
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Modificar orden de compra.


2
El usuario busca y selecciona la Orden de Compra(OC) que desea modificar. (Incluye: UC- Consultar historial de compras).


3 
El sistema muestra los detalles de la OC y los campos editables.


4 
El usuario modifica los datos requeridos (ej., cantidades de insumos, fecha de recepción esperada, o añade/elimina ítems).


5 
El sistema recalcula el costo total de la Orden de Compra y lo muestra al usuario para su validación.


6
El usuario confirma los cambios e ingresa una justificación obligatoria (ej., cambio de planificación de stock).


7
El sistema actualiza el registro de la Orden de Compra y registra el cambio en el Log de Auditoría, incluyendo la justificación.
El sistema informa al usuario que el proceso se completó con éxito.






Postcondición 
La Orden de Compra se actualiza con los nuevos datos y el nuevo costo total.
Excepciones 
Paso 




2
Si la OC está en estado "Recibida" o "Cancelada", el sistema notifica que la modificación no está permitida y finaliza.






4
Si el usuario intenta modificar el Proveedor o la Fecha de la Orden (original), el sistema no lo permite, ya que esto requeriría crear una nueva OC.


6
Si la justificación no es ingresada, el sistema impide guardar los cambios.
Rendimiento 
Paso 




4 


Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 
La información impacta en producción, ventas y stock.


UC–16
Cancelar Orden de Compra
Objetivos 
asociados 
OBJ–01 – Gestionar Insumos y Materiales.
Requisitos 
asociados 
IRQ–01 – Información de Insumos y Materiales .
IRQ–06 – Información de Proveedores y Compras.
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite registrar un pedido.
Precondición 
El usuario debe estar autenticado.
La Orden de Compra debe existir y estar en estado "Pendiente de Recepción".
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Cancelar orden de compra.


2
El usuario busca y selecciona la Orden de Compra (OC) que desea anular. (Incluye: UC- Consultar historial de compras).


3 
El sistema muestra los detalles de la OC y solicita una confirmación de seguridad.


4 
El usuario confirma e ingresa una justificación obligatoria del motivo de la cancelación (ej., proveedor sin stock, cambio de planificación, precio alto).


5 
El sistema cambia el estado de la Orden de Compra a "Cancelada".
El sistema registra la cancelación en el Log de Auditoría, detallando usuario, fecha, OC afectada y justificación.


6
El sistema informa al usuario que el proceso de cancelación ha finalizado con éxito.












Postcondición 
La Orden de Compra cambia su estado a "Cancelada".
Excepciones 
Paso 




2
Si la OC está en estado "Recibida" o ya "Cancelada", el sistema notifica que la anulación no está permitida y finaliza.






4
Si la justificación no es ingresada, el sistema impide continuar con la cancelación.


5
Si la anulación de la OC falla (NFR–07), el sistema revierte las operaciones y notifica el error.
Rendimiento 
Paso 




4 


Frecuencia 
Baja
Estabilidad 
alta 
Comentarios 
La información impacta en producción, ventas y stock.


UC–17
Registrar Recepción de Compra
Objetivos 
asociados 
OBJ–01 – Gestionar Insumos y Materiales.
Requisitos 
asociados 
IRQ–01 – Información de Insumos y Materiales.
IRQ–06 – Información de Proveedores y Compras
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite registrar una recepción de compra.
Precondición 
El usuario debe estar registrado en el sistema.
Debe existir una Orden de Compra (OC) en estado ""
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Registrar recepción de compra.


2
El usuario busca y selecciona la Orden de Compra (OC) asociada a la recepción.


3 
El sistema muestra los ítems, las cantidades esperadas y el proveedor.


4 
El usuario valida los ítems y las cantidades recibidas contra el remito/factura. (Si hay diferencias, se ejecuta una excepción).


5 
El usuario ingresa datos adicionales obligatorios (ej., Número de Factura/Remito, Fecha de Vencimiento del lote de insumos, Ubicación de almacenamiento).


6
El sistema actualiza el stock de inventario para cada insumo, incrementando la cantidad disponible y registrando el lote/vencimiento. 


7
El sistema cambia el estado de la Orden de Compra a "Recibida".


8
El sistema registra la acción en el Log de Auditoría y notifica a sobre la recepción exitosa.
El sistema informa al usuario que el proceso ha finalizado con éxito.
Postcondición 
La Orden de Compra cambia su estado a "Recibida".
El Stock de insumos aumenta en la cantidad recibida.
Se genera un registro inmutable en el Log de Auditoría.
Excepciones 
Paso 




4
[Recepción Parcial o Faltante] Si la cantidad recibida es menor a la esperada, el sistema alerta, permite al usuario registrar solo lo recibido y mantiene la OC en estado "Recepción Parcial" con el saldo pendiente para una futura recepción.
















Rendimiento 
Paso 




4 


Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 
La información impacta en producción, ventas y stock.


UC–18
Consultar Historial de Compras
Objetivos 
asociados 
OBJ–01 – Gestionar Insumos y Materiales.
Requisitos 
asociados 
IRQ–01 – Información de Insumos y Materiales.
IRQ–06 – Información de Proveedores y Compras
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite consultar el historial de compras.
Precondición 
El usuario debe estar registrado en el sistema.
Deben existir Órdenes de Compra (OC) registradas en el sistema.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Consultar historial de compras.


2
El sistema muestra un listado inicial de las Órdenes de Compra (OC) y presenta los criterios de búsqueda y filtros.


3 
El usuario ingresa o selecciona los criterios de búsqueda (ej., Rango de fechas, Proveedor, Estado de la OC: "Pendiente", "Recibida", "Cancelada", Monto total).




4 
El sistema ejecuta la consulta y presenta la lista de resultados con la información clave de cada OC (Nro. de OC, Fecha de Orden, Proveedor, Total y Estado).


5 
El usuario selecciona una OC específica del listado para ver el detalle.


6
El sistema muestra la información completa de la OC: detalle de insumos, cantidades solicitadas y recibidas, costos unitarios y totales, fecha de recepción esperada y número de factura/remito asociado (si ya fue recibida).


7
El usuario puede seleccionar una opción para Exportar la información a un formato externo (PDF o Excel, según NFR-05).
El usuario finaliza la consulta.
Postcondición 
El pedido queda registrado en el sistema con estado inicial “pendiente” o “confirmado”.
El pedido queda vinculado a la agenda de producción y al historial del cliente.
Excepciones 
Paso 




2






Rendimiento 
Paso 




4 


Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 
La información impacta en producción, ventas y stock.


UC–19
Emitir Reporte de Compras por periodo
Objetivos 
asociados 
OBJ–01 – Administrar pedidos y ventas.
OBJ–06 – Gestionar proveedores y compras
Requisitos 
asociados 
IRQ–03 – Información de Insumos y Materiales
IRQ–06 – Información de Proveedores y Compras
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite emitir el reporte de compras por periodo.
Precondición 
El usuario debe estar autenticado en el sistema.
Deben existir Órdenes de Compra y Recepciones registradas en el período seleccionado.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Emitir reporte de compras por período


2
El sistema presenta la interfaz de generación de reportes y solicita los criterios de filtro.


3 
El usuario define el período de tiempo (fecha de inicio y fecha de fin) para la consulta. Selecciona criterios de filtrado y agrupación (ej., filtrar por Proveedor, agrupar por Insumo, incluir solo OC en estado "Recibida") 


4 
El sistema ejecuta la consulta en la base de datos, consolida los datos de costos, insumos y estado de las OC.


5 
El sistema muestra una vista previa del reporte, incluyendo métricas como Costo Total de Compras, Cantidad de Insumos Comprados y un resumen por el criterio de agrupación seleccionado.


6
El usuario selecciona el Formato de Exportación deseado y confirma la emisión.


7
El sistema genera el archivo de reporte y lo pone a disposición para su descarga. El sistema informa al usuario que el proceso ha finalizado.
Postcondición 
1. Se genera un archivo de reporte (PDF o Excel) con la información de compras consolidada. 
2. Los datos originales del sistema permanecen inalterados.
Excepciones 
Paso 




3
Si el Período de tiempo es excesivamente grande (ej., más de 1 año), el sistema emite una advertencia de posible lentitud de procesamiento, pero permite continuar.






4
 Si ningún dato cumple con los criterios y el período de búsqueda, el sistema informa y finaliza la emisión.


7
Si la generación o exportación del archivo falla, el sistema notifica el error técnico.
Rendimiento 
Paso 




6 
 5 segundos (para la generación y visualización de la vista previa de un reporte complejo).
Frecuencia 
Mensual (Usado para cierres y análisis periódicos).
Estabilidad 
alta 
Comentarios 
Este CU es vital para la evaluación de costos. Se recomienda que la versión exportada a Excel permita el fácil manejo de los datos para análisis posteriores.




UC–20
Consultar Desempeño de Proveedores
Objetivos 
asociados 
OBJ–01 – Administrar pedidos y ventas.
OBJ–06 – Gestionar proveedores y compras
Requisitos 
asociados 
IRQ–03 – Información de Insumos y Materiales
IRQ–06 – Información de Proveedores y Compras
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite consultar el desempeño de los proveedores.
Precondición 
El usuario debe estar registrado en el sistema.
Deben existir Órdenes de Compra (OC) y Recepciones registradas asociadas a proveedores en el período deseado.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Consultar desempeño de proveedores.


2
El sistema presenta la interfaz y solicita los criterios de filtro y análisis.


3 
El usuario define el Período de tiempo y selecciona los Proveedores a incluir en el análisis (o selecciona "todos").


4 
El sistema ejecuta la consulta y calcula las métricas de desempeño por proveedor para el período seleccionado.


5 
El sistema muestra un panel de resultados con las métricas clave para cada proveedor seleccionado, posiblemente con un ranking.


6
El usuario selecciona un proveedor del panel para ver el detalle de las Órdenes de Compra que sustentan esas métricas.
El usuario puede seleccionar la opción para Exportar el reporte de desempeño


7
El usuario finaliza la consulta.
Postcondición 
1. El usuario obtiene un informe consolidado del rendimiento de los proveedores. 
2. Los datos originales del sistema permanecen inalterados.
Excepciones 
Paso 




4
Si el sistema no encuentra datos de recepción para un proveedor en el período, ese proveedor se excluye del cálculo de cumplimiento y precisión, notificando al usuario.






5
Si la consulta no arroja resultados para el período y criterios seleccionados, el sistema informa y solicita nuevos criterios.
Rendimiento 
Paso 




5 
5 segundos (para calcular y cargar el panel de desempeño con múltiples proveedores y OC).
Frecuencia 
Baja
Estabilidad 
alta 
Comentarios 
Este CU es crucial para la toma de decisiones sobre qué proveedores priorizar, basado en métricas objetivas y no solo en el precio.


UC– 21
Buscar Producto
Objetivos 
asociados 
OBJ-03 Administrar pedidos y ventas
OBJ-02 Gestionar recetas y costos de producción
Requisitos 
asociados 
IRQ-10 Información de Productos
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite buscar un producto
Precondición 
El usuario debe estar registrado en el sistema.
Deben existir Productos registrados
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Buscar producto.


2
El sistema presenta una interfaz de búsqueda con los criterios de filtro disponibles.


3 
El usuario ingresa o selecciona los criterios de búsqueda (ej., Nombre del producto, Categoría, Estado [activo/anulado], Tipo de producto [Torta, Tarta, Postre]).


4 
El sistema ejecuta la consulta y presenta una lista de resultados que cumplen con los criterios.


5 
El usuario selecciona un Producto específico del listado para ver su detalle completo.


6
El sistema muestra la información detallada del Producto (Nombre, Descripción, Código, Precio de Venta, Estado, y la Receta asociada, si la tiene).


7
El usuario finaliza la consulta.
Postcondición 
1. El usuario obtiene una vista del listado o del detalle del Producto buscado. 
2. Los datos del producto permanecen inalterados.
Excepciones 
Paso 




4
Si ningún Producto cumple con los criterios de búsqueda, el sistema informa al usuario y sugiere refinar la búsqueda.






6
Si el Producto seleccionado no tiene una Receta asociada, el sistema omite el campo de receta y muestra un indicador de faltante.
Rendimiento 
Paso 




 4
1.5 segundos (para la ejecución de la búsqueda y la presentación de resultados).
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 
Este CU será incluido en la mayoría de los casos de uso de gestión de Pedidos (UC-01) y Recetas (UC-28, UC-29).


UC–22
Registrar Producto
Objetivos 
asociados 
OBJ-03 Administrar pedidos y ventas
OBJ-02 Gestionar recetas y costos de producción
Requisitos 
asociados 
IRQ-10 Información de Productos
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite registrar un producto
Precondición 
El usuario debe estar registrado en el sistema.
Las categorías de productos deben estar configuradas en el sistema.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Registrar producto.


2
El sistema presenta el formulario de registro y solicita los datos obligatorios.


3 
El usuario ingresa la información básica del producto (Nombre, Descripción, Categoría, Imagen, Etiqueta, Variantes).


4 
El usuario ingresa el Precio de Venta final.


5 
El usuario asocia una Receta existente o crea una nueva.


6
El sistema valida todos los campos obligatorios


7
El usuario confirma el registro; el sistema crea el Producto en la base de datos con el estado "Activo" y genera un código único.


8
El sistema informa al usuario que el producto fue registrado con éxito.
Postcondición 
1. Se registra una nueva instancia de Producto en el catálogo con estado "Activo"
2. El producto está disponible para ser seleccionado en el proceso de Registrar Pedido
Excepciones 
Paso 




6
Si faltan campos obligatorios o la validación falla, el sistema señala los errores y solicita corrección. 










Rendimiento 
Paso 




 7
1 segundo
Frecuencia 
Media
Estabilidad 
alta 
Comentarios 
Es vital que este CU asegure que todo producto vendible tenga un precio asociado y, si es producido, una receta vinculada para el control de costos


UC–23
Modificar Producto
Objetivos 
asociados 
OBJ-03 Administrar pedidos y ventas
OBJ-02 Gestionar recetas y costos de producción
Requisitos 
asociados 
IRQ-10 Información de productos
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite modificar un producto.
Precondición 
El usuario debe estar registrado en el sistema.
El Producto debe existir y estar registrado en el catálogo.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Modificar producto.


2
El usuario busca y selecciona el Producto que desea modificar.


3 
El sistema muestra los datos actuales del producto en el formulario de modificación.


4 
El usuario modifica los campos requeridos (ej., Precio de Venta, Descripción, Categoría, etc.).


5 
El sistema valida los campos obligatorios y verifica que el nuevo nombre no se duplique con otro producto activo.


6
El usuario confirma los cambios; el sistema actualiza el registro del Producto.


7
Si se modificó un campo sensible (ej., Precio de Venta o Receta asociada), el sistema registra el cambio en el Log de Auditoría.
El sistema informa al usuario que el proceso se completó con éxito.
Postcondición 
1. El registro de Producto se actualiza con los nuevos datos.
2. Se genera un registro inmutable en el Log de Auditoría (si aplica).
Excepciones 
Paso 




2
Si el Producto no es encontrado o está Anulado, el sistema informa y finaliza.






5
Si la validación falla o el nuevo Nombre ya está en uso, el sistema alerta y solicita corregir el error.






Rendimiento 
Paso 




 6
1.5 segundos
Frecuencia 
Media
Estabilidad 
alta 
Comentarios 
La capacidad de cambiar la Receta asociada es clave, ya que permite la evolución de un producto sin perder su historial de ventas previo.


UC–24
Anular/Activar Producto
Objetivos 
asociados 
OBJ-03 Administrar pedidos y ventas
OBJ-02 Gestionar recetas y costos de producción
Requisitos 
asociados 
IRQ-10 Información de Productos
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite anular o activar un producto
Precondición 
El usuario debe estar registrado en el sistema.
El Producto debe existir en el catálogo.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de anular/activar un producto.


2
El usuario busca el producto que desea anular/activar


3 
El sistema muestra el estado actual del Producto (Activo/Inactivo).


4 
El usuario selecciona el nuevo estado para el Producto (ej., cambia de "Activo" a "Anulado").


5 
El usuario ingresa una justificación obligatoria para el cambio de estado (ej., descontinuado, estacional, prueba de mercado).


6
El sistema valida que el producto no tenga pedidos pendientes o en producción asociados al momento de la anulación. 


7
El sistema actualiza el estado del Producto y registra el cambio en el Log de Auditoría , incluyendo la justificación.


8
El sistema informa al usuario que el proceso se completó con éxito.
Postcondición 
1. El estado del Producto se actualiza a "Activo" o "Anulado".
2. El Producto no aparece en el catálogo visible para clientes/vendedores si está "Anulado".
3. Se genera un registro inmutable en el Log de Auditoría.
Excepciones 
Paso 




2
Si el producto no es encontrado, el sistema informa y finaliza.






4
Si la justificación no es ingresada, el sistema impide guardar los cambios.


6
Si el Producto tiene Pedidos en estado "Pendiente" o "Confirmado" que aún no han sido entregados, el sistema bloquea la anulación y solicita finalizar esos pedidos primero.
Rendimiento 
Paso 




 


Frecuencia 
Baja
Estabilidad 
alta 
Comentarios 
Es vital que los productos Anulados sigan disponibles para la consulta histórica (UC-21) y en los registros de pedidos/ventas anteriores, pero no estén disponibles para nuevos pedidos.


UC–25
Registrar Receta
Objetivos 
asociados 
OBJ–01 Gestionar insumos y materiales
OBJ–02 Gestionar recetas y costos de producción
Requisitos 
asociados 
IRQ–01 Información de Insumos y Materiales
IRQ–02 Información de Recetas y Costos de Producción
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite registrar una receta.
Precondición 
El usuario debe estar registrado en el sistema.
Los Insumos requeridos deben estar previamente registrados en el sistema
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Registrar receta.


2
El sistema presenta el formulario y el usuario ingresa la información básica de la Receta (Nombre, Descripción, Categoría, Estado inicial: Activa).


3 
El usuario define el Rendimiento de la Receta (ej., 1 torta, 10 porciones, 1 kg de masa madre).


4 
El usuario comienza a agregar los Insumos necesarios: busca el insumo, ingresa la cantidad y la unidad de medida (ej., 300g de harina).


5 
El sistema calcula el Costo de Materia Prima (Costo Primo) de la receta, basado en el precio de costo actual de cada insumo (OBJ-02).


6
El usuario puede ingresar opcionalmente otros Costos Indirectos de Fabricación (CIF) o Tiempos de Producción.


7
El sistema valida que todos los insumos y cantidades estén definidos y que el rendimiento sea válido.


8
El usuario confirma; el sistema crea el registro de la Receta y la deja disponible para ser vinculada a un Producto.


9
El sistema informa al usuario que el proceso ha finalizado con éxito.
Postcondición 
1. Se registra una nueva instancia de Receta con estado "Activa".
2. El Costo Primo de la Receta queda registrado
3. La Receta está disponible para ser vinculada a un Producto o para la gestión de la Producción.
Excepciones 
Paso 




2
Si el Nombre de la Receta ya existe, el sistema informa del duplicado y solicita un nombre único.






4
Si el Insumo buscado no existe, el sistema solicita registrarlo (invoca el CU correspondiente del Subsistema de Stock) o buscar otro.


7
Si la Unidad de Medida ingresada para un insumo es incompatible con la unidad de stock (ej., usa 'litros' cuando el stock es en 'kg'), el sistema emite una alerta y solicita corrección o conversión.
Rendimiento 
Paso 




 8
2 segundos
Frecuencia 
Media
Estabilidad 
alta 
Comentarios 
La precisión en las cantidades de insumos es clave para el cálculo del costo real y la correcta descarga de stock


UC–26
Modificar Receta
Objetivos 
asociados 
OBJ–01 Gestionar insumos y materiales
OBJ–02 Gestionar recetas y costos de producción
Requisitos 
asociados 
IRQ–01 Información de Insumos y Materiales
IRQ–02 Información de Recetas y Costos de Producción
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite modificar una receta.
Precondición 
El usuario debe estar registrado en el sistema.
La Receta debe existir y estar en estado "Activa".
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Modificar receta.


2
El usuario busca y selecciona la Receta que desea modificar.


3 
El sistema muestra la información actual de la Receta (insumos, cantidades, rendimiento) y habilita la edición.


4 
El usuario modifica los datos requeridos (ej., cambia la cantidad de un insumo, añade un nuevo insumo, ajusta el rendimiento).


5 
El usuario ingresa una justificación obligatoria para el cambio (ej., mejora de calidad, ajuste de costos).


6
El sistema recalcula el Costo Primo de la receta basándose en los costos actuales de los insumos.


7
El sistema actualiza el registro de la Receta con los nuevos datos, registra el nuevo Costo Primo y registra el cambio en el Log de Auditoría.


8
El sistema notifica al usuario que el proceso ha finalizado con éxito.
Postcondición 
1. La Receta se actualiza con los nuevos insumos/cantidades.
2. El Costo Primo se recalcula.
3. Se genera un registro inmutable en el Log de Auditoría.
Excepciones 
Paso 




4
Si el usuario intenta utilizar un Insumo que no existe, el sistema emite una alerta y solicita registrarlo (o seleccionarlo correctamente).






5
Si la justificación de la modificación no es ingresada, el sistema impide guardar los cambios.






Rendimiento 
Paso 




 6
2 segundos
Frecuencia 
Media
Estabilidad 
alta 
Comentarios 
Es esencial que la modificación de una Receta dispare un recálculo automático en todos los Productos vinculados, ya que esto impacta el precio de venta y el margen.


UC–27
Desactivar Receta
Objetivos 
asociados 
OBJ–01 Gestionar insumos y materiales
OBJ–02 Gestionar recetas y costos de producción
Requisitos 
asociados 
IRQ–01 Información de Insumos y Materiales
IRQ–02 Información de Recetas y Costos de Producción
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite desactivar una receta.
Precondición 
El usuario debe estar registrado en el sistema.
La Receta debe existir en el sistema
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Desactivar receta.


2
El usuario busca y selecciona la Receta que desea modificar. (Incluye: UC-31 Consultar recetas).


3 
El sistema muestra el estado actual de la Receta (Activa/Inactiva) y solicita el nuevo estado.


4 
El usuario selecciona el nuevo estado ("Desactivada") e ingresa una justificación obligatoria (ej., obsoleta, descontinuada, falla de calidad).


5 
El sistema valida que la receta no esté vinculada a ningún Producto Activo (UC-24) ni a Órdenes de Producción pendientes.


6
El sistema actualiza el estado de la Receta a "Desactivada" y registra el cambio en el Log de Auditoría.


7
El sistema informa al usuario que el proceso ha finalizado con éxito.
Postcondición 
1. El estado de la Receta se actualiza a "Desactivada".
2. La Receta no está disponible para nuevas Órdenes de Producción.
3. Se genera un registro inmutable en el Log de Auditoría.
Excepciones 
Paso 




2
Si la Receta no es encontrada, el sistema informa y finaliza.






4
Si la justificación no es ingresada, el sistema impide guardar los cambios.


5
Si la Receta está vinculada a un Producto que se encuentra en estado "Activo" , el sistema bloquea la desactivación y solicita primero desvincularla o anular el Producto.
Rendimiento 
Paso 




 6
1.5 segundos
Frecuencia 
Baja
Estabilidad 
alta 
Comentarios 
La principal diferencia con la eliminación es que las recetas desactivadas se mantienen para la consulta histórica de costos y para el análisis de productos antiguos.


UC–28
Consultar Recetas
Objetivos 
asociados 
OBJ–01 Gestionar insumos y materiales
OBJ–02 Gestionar recetas y costos de producción
Requisitos 
asociados 
IRQ–01 Información de Insumos y Materiales
IRQ–02 Información de Recetas y Costos de Producción
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite consultar las recetas.
Precondición 
El usuario debe estar registrado en el sistema.
Deben existir Recetas registradas en el sistema
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Consultar recetas.


2
El sistema presenta la interfaz de consulta con los criterios de búsqueda y filtros.


3 
El usuario ingresa o selecciona los criterios de búsqueda (ej., Nombre de la receta, Categoría, Estado: Activa/Desactivada, Insumo utilizado).


4 
El sistema ejecuta la consulta y presenta la lista de resultados que cumplen con los criterios.


5 
El usuario selecciona una Receta específica del listado para ver su detalle.


6
El sistema muestra la información completa de la Receta: Nombre, Descripción, Rendimiento, Costo Primo Actual y una tabla con el detalle de insumos y cantidades (incluyendo unidad de medida).


7
El usuario puede seleccionar una opción para Exportar el listado o el detalle de la receta (ej., lista de insumos para cotización).


8
El usuario finaliza la consulta.
Postcondición 


Excepciones 
Paso 




4
Si ninguna Receta cumple con los criterios de búsqueda, el sistema informa al usuario y solicita nuevos criterios.






6
Si la Receta está en estado "Desactivada", el sistema lo resalta claramente pero permite visualizar la información histórica.
Rendimiento 
Paso 




 4
2 segundos
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 




UC–29
Ver Agenda de Producción
Objetivos 
asociados 
OBJ–04 Gestionar agenda y planificación de producción
Requisitos 
asociados 
IRQ–04 Información de Agenda y Planificación de Producción
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite ver la agenda de producción
Precondición 
El usuario debe estar registrado en el sistema.
Deben existir Pedidos registrados con fecha de entrega o Órdenes de Producción (OP) creadas y pendientes.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Ver agenda de producción.


2
El sistema presenta la Agenda/Calendario de Producción (IRQ-04), mostrando los datos por día, semana o mes, según la vista seleccionada por el usuario.


3 
El sistema muestra, para cada fecha, los Pedidos con fecha de entrega, agrupados por prioridad y hora.


4 
El sistema muestra, también para cada fecha, las Órdenes de Producción (OP) generadas y su estado (Pendiente, En Proceso, Terminada).


5 
El usuario puede aplicar filtros a la vista (ej., filtrar por tipo de producto, por colaborador asignado o por estado de la OP).


6
El usuario selecciona un Pedido o una Orden de Producción de la agenda para ver su detalle.


7
El usuario finaliza la consulta.
Postcondición 


Excepciones 
Paso 




3
Si el sistema detecta que la carga de trabajo para una fecha excede la capacidad máxima de producción (predefinida), el sistema emite una Alerta de Sobrecarga), resaltando la fecha en la agenda.






4
Si hay Órdenes de Producción atrasadas (fecha de inicio anterior a hoy), el sistema las resalta con un indicador de urgencia.
Rendimiento 
Paso 




 3
3 segundos
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 
Este CU es la interfaz principal para la planificación. Permite al Encargado transformar los Pedidos (demanda) en Órdenes de Producción (trabajo a realizar).


UC–30
Registrar Orden de Producción
Objetivos 
asociados 
OBJ–01 Gestionar insumos y materiales
OBJ–03 Administrar pedidos y ventas
OBJ–04 Gestionar agenda y planificación de producción
Requisitos 
asociados 
IRQ–01 Información de Insumos y Materiales
IRQ–03 Información de Pedido y Ventas
IRQ–04 Información de Agenda y Planificación de Producción
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite registrar una orden de producción
Precondición 
El usuario debe estar registrado en el sistema.
Deben existir Pedidos en estado "Confirmado" o "Pendiente" para producir. 
La Receta asociada al Producto debe estar registrada y activa
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Registrar orden de producción.


2
El usuario selecciona uno o varios Pedidos (o ítems de pedidos) para agrupar en una única OP.


3 
El sistema calcula las cantidades totales del Producto a elaborar y estima los insumos requeridos, basándose en la Receta vinculada.


4 
El sistema verifica la disponibilidad actual de stock de los insumos requeridos.


5 
El usuario define la Fecha de Inicio y la Fecha Límite de Terminación de la OP.


6
El usuario confirma; el sistema crea la Orden de Producción con estado "Pendiente" y la vincula a los pedidos.


7
El sistema evalúa si el stock es suficiente. Si no lo es, activa una Alerta de Insumos Críticos.


8
El sistema informa al usuario que el proceso ha finalizado con éxito.
Postcondición 
1. Se registra una nueva instancia de Orden de Producción (OP).
2. Los Pedidos seleccionados quedan marcados como "En Producción".
3. La OP se agrega a la Agenda de Producción.
Excepciones 
Paso 




4
Si la verificación de stock muestra insuficiencia, el sistema alerta al usuario y permite continuar, pero no reserva stock y mantiene la alerta (RF-09) para que el usuario gestione la compra.






3
Si el Producto seleccionado no tiene una Receta asociada, el sistema no permite generar la OP y solicita al usuario vincularla o registrarla.
Rendimiento 
Paso 




 6
3 segundos
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 
Es fundamental que la OP solo descuente el stock cuando el usuario la marque como "Terminada" (en otro CU, como UC-33 Finalizar orden de producción), asegurando que el consumo sea real.


UC–31
Consultar Orden de Producción
Objetivos 
asociados 
OBJ–01 Gestionar insumos y materiales
OBJ–03 Administrar pedidos y ventas
OBJ–04 Gestionar agenda y planificación de producción
Requisitos 
asociados 
IRQ–01 Información de Insumos y Materiales
IRQ–03 Información de Pedido y Ventas
IRQ–04 Información de Agenda y Planificación de Producción
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite consultar las órdenes de producción.
Precondición 
El usuario debe estar registrado en el sistema.
Deben existir Órdenes de Producción (OP) registradas en el sistema.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Consultar orden de producción.


2
El sistema presenta la interfaz de consulta con criterios de búsqueda y filtros (ej., ID de OP, Rango de fechas, Estado (Pendiente, En Proceso, Terminada), Producto, Pedido asociado).


3 
El usuario ingresa o selecciona los criterios y el sistema presenta el listado de Órdenes de Producción que cumplen con el filtro.


4 
El usuario selecciona una Orden de Producción específica del listado.


5 
El sistema muestra la información completa de la OP:
Detalle: Productos a elaborar, cantidades, fechas de inicio/límite.
Estado: Actual (ej., Pendiente, En Proceso, Terminada).
 Insumos: Lista detallada de insumos requeridos (según receta) y su disponibilidad actual en stock.
Vinculación: Referencia al Pedido(s) del cliente que originó la OP.


6
El usuario finaliza la consulta.
Postcondición 


Excepciones 
Paso 




3
Si ninguna OP cumple con los criterios de búsqueda, el sistema informa al usuario y sugiere nuevos criterios.






5
Si el sistema detecta que el stock actual de uno o más insumos en la lista es insuficiente para completar la OP, lo resalta con una Alerta de Insumos Críticos.
Rendimiento 
Paso 




 3
2 segundos
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 




UC–32
Modificar Orden de Producción
Objetivos 
asociados 
OBJ–01 Gestionar insumos y materiales
OBJ–03 Administrar pedidos y ventas
OBJ–04 Gestionar agenda y planificación de producción
Requisitos 
asociados 
IRQ–01 Información de Insumos y Materiales
IRQ–03 Información de Pedido y Ventas
IRQ–04 Información de Agenda y Planificación de Producción
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite modificar una orden de producción
Precondición 
El usuario debe estar registrado en el sistema.
La Orden de Producción debe existir y estar en estado "Pendiente" o "En Proceso".
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Modificar orden de producción.


2
El usuario busca y selecciona la Orden de Producción (OP) que desea ajustar.


3 
El sistema muestra los detalles de la OP y los campos editables.


4 
El usuario modifica los datos requeridos (ej., aumenta/disminuye la cantidad de productos, cambia la fecha límite).


5 
El sistema recalcula la nueva estimación de insumos requeridos, basándose en la Receta y las nuevas cantidades.


6
El sistema verifica la disponibilidad actual de stock con la nueva estimación.


7
El usuario confirma los cambios e ingresa una justificación obligatoria (ej., cliente solicitó 2 unidades más, ajuste de fecha por falta de personal).


8
El sistema actualiza el registro de la Orden de Producción y registra el cambio en el Log de Auditoría


9
El sistema informa al usuario que el proceso ha finalizado con éxito.
Postcondición 
1. La Orden de Producción se actualiza con los nuevos datos
2. La nueva estimación de insumos se actualiza
3. Se genera un registro inmutable en el Log de Auditoría.
Excepciones 
Paso 




2
Si la OP está en estado "Terminada" o "Cancelada", el sistema notifica que la modificación no está permitida y finaliza.






6
Si el cambio en la cantidad provoca un Stock Insuficiente de algún insumo, el sistema emite una Alerta de Insumos Críticos.


7
Si la justificación no es ingresada, el sistema impide guardar los cambios.
Rendimiento 
Paso 




 8
1.5 segundos
Frecuencia 
Media
Estabilidad 
alta 
Comentarios 
Este CU asegura que la planificación y el control de costos se mantengan actualizados incluso con cambios de último momento.


UC–33
Finalizar Orden de producción
Objetivos 
asociados 
OBJ–01 Gestionar insumos y materiales
OBJ–03 Administrar pedidos y ventas
OBJ–04 Gestionar agenda y planificación de producción
Requisitos 
asociados 
IRQ–01 Información de Insumos y Materiales
IRQ–03 Información de Pedido y Ventas
IRQ–04 Información de Agenda y Planificación de Producción
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite finalizar la orden de producción
Precondición 
El usuario debe estar registrado en el sistema.
La Orden de Producción debe existir y estar en estado "En Proceso".
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Finalizar orden de producción.


2
El usuario busca y selecciona la Orden de Producción (OP) que ha sido completada.


3 
El sistema muestra la OP, la receta, los insumos estimados y solicita ingresar la Cantidad Final Producida (ej., 4 tortas en lugar de 5).


4 
El usuario confirma la finalización y, opcionalmente, ingresa la Cantidad Real de Insumos Consumidos (si hubo desvíos de la receta).


5 
El sistema ejecuta la transacción de stock: calcula el consumo real (basado en la receta y la cantidad producida) y descuenta esos Insumos del Stock.


6
El sistema cambia el estado de la OP a "Terminada".


7
El sistema cambia el estado del Pedido(s) asociado(s) a "Listo para Entrega".


8
El sistema registra el consumo real y el evento de finalización en el Log de Auditoría, incluyendo la justificación por cualquier desviación de rendimiento.


9
El sistema informa al usuario que el proceso ha finalizado con éxito.
Postcondición 
1. El Stock de insumos se reduce por el consumo real (o estimado).
2. El estado de la OP cambia a "Terminada"
3. El Pedido(s) asociado(s) cambia(n) a "Listo para Entrega"
4. El costo real de producción del lote queda registrado para análisis.
Excepciones 
Paso 




3
Si la Cantidad Final Producida es significativamente menor a la planificada (ej., más de 20%), el sistema solicita una justificación obligatoria (ej., merma, error de producción).






5
Si el stock actual es menor a la cantidad a descargar, el sistema alerta, permite la descarga (generando stock negativo), y registra la diferencia para un ajuste posterior.
Rendimiento 
Paso 




 5
3 segundos
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 




UC–34
Consultar Stock Disponible 
Objetivos 
asociados 
OBJ–01 Gestionar insumos y materiales
Requisitos 
asociados 
IRQ–01 Información de Insumos y Materiales
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite consultar el stock disponible
Precondición 
El usuario debe estar registrado en el sistema.
Deben existir Insumos registrados y con movimientos de stock.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Consultar stock disponible.


2
El sistema presenta la interfaz de consulta con los criterios de búsqueda y filtros.


3 
El usuario ingresa o selecciona los criterios de búsqueda (ej., Nombre de insumo, Categoría, Ubicación (si aplica), Rango de fechas de vencimiento).


4 
El sistema ejecuta la consulta y presenta la lista de insumos que cumplen con el filtro.


5 
Por cada insumo, el sistema muestra: Nombre, Unidad de Medida, Cantidad Disponible Actual, Stock Mínimo definido y el Estado (Normal, Bajo, Crítico).


6
El usuario puede seleccionar un insumo específico para ver el detalle de los lotes y fechas de vencimiento asociados (IRQ-05).


7
El usuario puede seleccionar una opción para Exportar el listado de stock (NFR-05), especialmente el stock crítico.


8
El usuario finaliza la consulta.
Postcondición 


Excepciones 
Paso 




4
Si ningún Insumo cumple con los criterios de búsqueda, el sistema informa al usuario y solicita nuevos criterios.






5
Si la Cantidad Disponible es menor al Stock Mínimo, el sistema resalta el insumo y activa la Alerta de Stock Crítico.


6
Si el insumo está cerca de su fecha de vencimiento, el sistema lo resalta con una alerta de caducidad.
Rendimiento 
Paso 




4 
2 segundos
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 




UC–35
Registrar movimiento de stock (entrada, salida, ajuste)
Objetivos 
asociados 
OBJ–01 Gestionar insumos y materiales
OBJ–06 Gestionar proveedores y compras
Requisitos 
asociados 
IRQ–01 Información de Insumos y Materiales
IRQ–06 Información de Proveedores y Compras
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite registrar un movimiento de stock
Precondición 
El usuario debe estar registrado en el sistema.
El Insumo debe estar registrado en el sistema.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Registrar movimiento de stock.


2
El usuario busca y selecciona el Insumo afectado.


3 
El usuario selecciona el Tipo de Movimiento ("Entrada", "Salida" o "Ajuste por Inventario").


4 
El usuario ingresa la Cantidad del movimiento y la Unidad de Medida.


5 
El usuario ingresa una Justificación obligatoria y detallada del movimiento (ej., "Merma por humedad", "Salida por prueba de sabor", "Ajuste de inventario semanal").


6
El sistema valida el movimiento (ej., que el formato sea correcto).


7
El sistema actualiza el stock disponible del Insumo, aumentando o disminuyendo la cantidad según el tipo de movimiento.


8
El sistema crea el registro del movimiento en el historial  y lo registra en el Log de Auditoría.


9
El sistema informa al usuario que el proceso ha finalizado con éxito.
Postcondición 
1. La Cantidad Disponible del Insumo se actualiza en el inventario
2. Se genera un registro inmutable en el Historial de Movimientos y el Log de Auditoría.
Excepciones 
Paso 




3
Si el usuario intenta un movimiento de Salida o Ajuste negativo y el insumo tiene stock insuficiente, el sistema alerta, pero permite la acción (generando stock negativo), documentando la inconsistencia.






5
Si la Justificación no es ingresada, el sistema bloquea el registro.
Rendimiento 
Paso 




 7
1.5 segundos
Frecuencia 
Media
Estabilidad 
alta 
Comentarios 
Este CU es vital para la trazabilidad de las pérdidas y la corrección de errores de inventario. La justificación es la clave para la auditoría.


UC–36
Emitir Reporte de Stock Crítico
Objetivos 
asociados 
OBJ–01 Gestionar insumos y materiales
OBJ–06 Gestionar proveedores y compras
OBJ–08 Generar reportes e indicadores estratégicos
Requisitos 
asociados 
IRQ–01 Información de Insumos y Materiales
IRQ–06 Información de Proveedores y Compras
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite emitir un reporte de stock crítico 
Precondición 
El usuario debe estar registrado en el sistema.
Los Insumos deben tener definido un valor de Stock Mínimo. Deben existir insumos con un stock disponible menor o igual a su stock mínimo.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Emitir reporte de stock crítico.


2
El sistema presenta la interfaz de filtros (ej., filtrar por Categoría de Insumo o Ubicación).


3 
El sistema ejecuta la consulta sobre el inventario, identificando todos los insumos donde Cantidad Disponible <= Stock Mínimo.


4 
El sistema genera el reporte, presentando los datos clave para la compra: Nombre del Insumo, Unidad de Medida, Cantidad Disponible, Stock Mínimo, Diferencia/Cantidad a Reponer y Último Proveedor registrado.


5 
El sistema resalta aquellos insumos que están en Stock Negativo como prioridad máxima.


6
El usuario selecciona la opción para Exportar el reporte a un formato externo (ej., Excel, para usar como borrador de Orden de Compra, según NFR-05).


7
El sistema informa al usuario que el proceso ha finalizado con éxito.
Postcondición 
1. Se genera un reporte con los insumos que requieren ser comprados
Excepciones 
Paso 




4
Si ningún Insumo se encuentra en estado crítico, el reporte se genera con una leyenda indicando que el stock está saludable.




Rendimiento 
Paso 




 4
3 segundos
Frecuencia 
Media
Estabilidad 
alta 
Comentarios 
Este reporte es la fuente principal para el Subsistema de Compras (OBJ-03), informando qué se debe comprar y en qué cantidad. La automatización de este reporte es la implementación de la alerta (RF-09).


UC–37
Consultar Historial de Movimientos
Objetivos 
asociados 
OBJ–01 Gestionar insumos y materiales
OBJ–06 Gestionar proveedores y compras
Requisitos 
asociados 
IRQ–01 Información de Insumos y Materiales
IRQ–06 Información de Proveedores y Compras
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite consultar el historial de movimientos de stock
Precondición 
El usuario debe estar registrado en el sistema.
Deben existir Movimientos de Stock registrados
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Consultar historial de movimientos.


2
El sistema presenta la interfaz de consulta con criterios de búsqueda y filtros.


3 
El usuario ingresa o selecciona los criterios (ej., Rango de fechas, Insumo específico, Tipo de Movimiento (Entrada/Salida/Ajuste), Usuario responsable, ID de OP/OC asociada).


4 
El sistema ejecuta la consulta sobre la base de datos de movimientos y presenta el listado cronológico de movimientos que cumplen con el filtro.


5 
Por cada movimiento, el sistema muestra: Fecha y Hora, Tipo de Movimiento, Cantidad (+/-), Insumo afectado, Justificación/Referencia (ej., "Consumo OP-123" o "Ajuste de inventario semanal") y el Usuario que lo registró.


6
El usuario puede seleccionar un movimiento para ver el registro de auditoría detallado (NFR-08).


7
El usuario puede seleccionar una opción para Exportar el historial de movimientos a un formato externo.


8
El usuario finaliza la consulta.
Postcondición 


Excepciones 
Paso 




4
Si ningún movimiento cumple con los criterios de búsqueda, el sistema informa al usuario y solicita nuevos criterios.




Rendimiento 
Paso 




 4
3 segundos
Frecuencia 
Media
Estabilidad 
alta 
Comentarios 
Este CU es la herramienta clave para la auditoría interna y la trazabilidad de insumos, permitiendo investigar la causa de las diferencias entre el stock físico y el lógico.


UC–38
Buscar cliente
Objetivos 
asociados 
OBJ–05 Gestionar clientes y fidelización
Requisitos 
asociados 
IRQ-05 Información de clientes y fidelización 
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite buscar un cliente
Precondición 
El usuario debe estar registrado en el sistema.
Deben existir Clientes registrados en el sistema
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Buscar cliente.


2
El sistema presenta la interfaz de búsqueda con los campos de criterio y filtros


3 
El usuario ingresa los criterios de búsqueda (ej., Nombre, Apellido, Teléfono, Email).


4 
El sistema ejecuta la consulta y presenta la lista de resultados que cumplen con los criterios.


5 
El usuario selecciona un Cliente específico del listado.


6
El sistema muestra la información completa del Cliente: Datos de contacto, Dirección, Estado (Activo/Inactivo), Historial de Pedidos y Detalle de Compras/Pagos.


7
El usuario puede seleccionar una opción para iniciar un nuevo Pedido o modificar los datos del cliente


8
El usuario finaliza la consulta.
Postcondición 


Excepciones 
Paso 




4
Si ningún Cliente cumple con los criterios de búsqueda, el sistema informa al usuario y sugiere registrar un nuevo cliente






6
Si el Cliente está Inactivo, el sistema lo indica claramente, pero permite visualizar su historial.
Rendimiento 
Paso 




 4
1.5 segundos
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 




UC–39
Registrar Cliente
Objetivos 
asociados 
OBJ-05 Gestionar clientes y fidelización
Requisitos 
asociados 
IRQ-05 Información de clientes y fidelización
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite registrar un cliente
Precondición 
El usuario debe estar registrado en el sistema.
El cliente a registrar no debe existir previamente en la base de datos
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Registrar cliente.


2
El sistema presenta el formulario y el usuario ingresa la información obligatoria del Cliente (ej., Nombre, Apellido, Email, Teléfono).


3 
El usuario ingresa datos opcionales (ej., Dirección de entrega, Comentarios relevantes).


4 
El sistema valida que todos los campos obligatorios estén completos y que los formatos sean correctos.


5 
El usuario confirma; el sistema crea el registro del Cliente con estado "Activo".


6
El sistema informa al usuario que el proceso ha finalizado con éxito.
Postcondición 
1. Se registra una nueva instancia de Cliente en el sistema.
2. El Cliente está disponible para ser asociado a nuevos Pedidos
3. El Cliente se incorpora al grupo de fidelización
Excepciones 
Paso 




3
Si el Email ya existe, el sistema emite una alerta, sugiere Buscar cliente o Modificar cliente, y bloquea el registro.






4
Si faltan campos obligatorios, el sistema resalta el error y no permite guardar
Rendimiento 
Paso 




 5
1.5 segundos
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 




UC–40
Modificar Cliente
Objetivos 
asociados 
OBJ-05 Gestionar clientes y fidelización
Requisitos 
asociados 
IRQ-05 Información de clientes y fidelización
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite modificar un cliente
Precondición 
El usuario debe estar registrado en el sistema.
El Cliente debe existir y no estar en estado "Anulado"
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Modificar cliente.


2
El usuario busca y selecciona el Cliente que desea actualizar.


3 
El sistema muestra la información actual del Cliente y habilita los campos editables (Email, Teléfono, Dirección, etc.).


4 
El usuario modifica los datos requeridos.


5 
El sistema valida que los campos obligatorios sigan completos y que los formatos sean correctos


6
El usuario confirma los cambios e ingresa una justificación obligatoria (ej., corrección de dirección, actualización de teléfono).


7
El sistema actualiza el registro del Cliente y registra el cambio en el Log de Auditoría


8
El sistema informa al usuario que el proceso ha finalizado con éxito.
Postcondición 
1. El registro del Cliente se actualiza con la nueva información
2. Se genera un registro inmutable en el Log de Auditoría.
3. La nueva información estará disponible para futuros Pedidos y Ventas.
Excepciones 
Paso 




2
Si el Cliente no es encontrado, el sistema informa y finaliza.






4
Si el usuario modifica el email o DNI a un valor que ya pertenece a otro cliente activo, el sistema notifica el duplicado y bloquea el guardado.


6
Si la justificación de la modificación no es ingresada, el sistema impide guardar los cambios.
Rendimiento 
Paso 




7
1.5 segundos
Frecuencia 
Media
Estabilidad 
alta 
Comentarios 
Este CU es vital para la coherencia de las direcciones de entrega y el contacto con el cliente. La justificación y auditoría son clave en la gestión de datos.


UC–41
Anular/Activar cliente 
Objetivos 
asociados 
OBJ-05 Gestionar clientes y fidelización
Requisitos 
asociados 
IRQ-05 Información de clientes y fidelización
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite anular o activar un cliente
Precondición 
El usuario debe estar registrado en el sistema.
El Cliente debe existir en la base de datos
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Anular/activar cliente.


2
El usuario busca y selecciona el Cliente cuyo estado desea modificar.


3 
El sistema muestra el estado actual del Cliente (Activo/Inactivo).


4 
El usuario selecciona el nuevo estado para el Cliente (ej., cambia de "Activo" a "Inactivo").


5 
El usuario ingresa una justificación obligatoria para el cambio de estado (ej., datos desactualizados, cliente bloqueado por falta de pago, solicitud de baja).


6
El sistema valida que el cliente no tenga Pedidos pendientes o Ventas sin cerrar.


7
El sistema actualiza el estado del Cliente y registra el cambio en el Log de Auditoría


8
El sistema informa al usuario que el proceso se completó con éxito.
Postcondición 
1. El estado del Cliente se actualiza a "Activo" o "Inactivo".
2. Un Cliente Inactivo no puede generar nuevos Pedidos
3. Se genera un registro inmutable en el Log de Auditoría
Excepciones 
Paso 




2
Si el Cliente no es encontrado, el sistema informa y finaliza.






5
Si la justificación de la modificación no es ingresada, el sistema impide guardar los cambios.


6
Si el Cliente tiene Pedidos en estado "Pendiente" o "Confirmado" que aún no han sido entregados, el sistema bloquea la anulación y solicita finalizar esos pedidos primero.
Rendimiento 
Paso 




7
1.5 segundos
Frecuencia 


Estabilidad 
alta 
Comentarios 




UC–42
Consultar Historial de Pedidos del Cliente
Objetivos 
asociados 
OBJ-05 Gestionar clientes y fidelización
Requisitos 
asociados 
IRQ-05 Información de clientes y fidelización
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite consultar el historial de pedidos del cliente
Precondición 
El usuario debe estar registrado en el sistema.
El Cliente debe existir en el sistema
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Consultar historial de pedidos del cliente.


2
El usuario busca y selecciona el Cliente cuya información desea consultar.


3 
El sistema presenta la lista cronológica de Pedidos/Ventas asociados a ese Cliente.


4 
El usuario puede aplicar filtros sobre el historial (ej., por rango de fechas, por estado: Entregado/Cancelado, por producto).


5 
Por cada ítem del historial, el sistema muestra: Fecha del Pedido/Venta, Monto Total, Estado actual y Fecha de Entrega/Retiro.


6
El usuario selecciona un Pedido específico para ver el detalle de los productos comprados, la receta utilizada y la forma de pago.


7
El sistema puede mostrar un resumen de Estadísticas de Fidelización (ej., total gastado, cantidad de pedidos realizados, producto más comprado).


8
El usuario finaliza la consulta
Postcondición 


Excepciones 
Paso 




3
Si el Cliente no tiene historial de pedidos/ventas, el sistema informa al usuario que es un cliente nuevo o que no hay registros.






6
Si el Pedido seleccionado está Cancelado o Anulado, el sistema lo indica claramente, pero permite ver la información histórica.
Rendimiento 
Paso 




 3
2 segundos
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 




UC–43
Ver catálogo de productos
Objetivos 
asociados 
OBJ–03 Administrar pedidos y ventas
Requisitos 
asociados 
IRQ–03 Información de Pedido y Ventas
IRQ–10 Información de Productos
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite ver el catálogo de productos
Precondición 
Deben existir Productos registrados y en estado "Activo"
Los precios de venta deben estar definidos
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema Ver catálogo de productos.


2
El sistema presenta el catálogo de productos activos, organizados por Categoría (ej., tortas, tartas, postres).


3 
El usuario aplica filtros o utiliza la barra de búsqueda (ej., buscar por nombre, rango de precio).


4 
El sistema muestra, por cada producto, la imagen, el nombre, la descripción corta y el precio de venta.


5 
El usuario selecciona un Producto para ver su ficha detallada (ej., ingredientes principales, descripción larga, opciones de personalización, alérgenos).


6
El Cliente puede agregar el Producto al carrito para iniciar un nuevo pedido


7
El usuario finaliza la consulta.
Postcondición 
1. 
2. 
Excepciones 
Paso 




3
Si ningún Producto cumple con el filtro, el sistema informa y sugiere eliminar los filtros.






4
Si un producto está en estado "Inactivo", el sistema no lo muestra al Cliente, pero sí podría mostrarlo al Vendedor con una alerta.


6
Si el cliente no está logueado, se le pedirá que se registre o logee y de ahí se lo llevará de nuevo a su carrito 
Rendimiento 
Paso 




 2
1.5 segundos
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 




UC–44
Buscar Proveedor
Objetivos 
asociados 
OBJ–06 Gestionar proveedores y compras
Requisitos 
asociados 
IRQ–06 Información de Proveedores y Compras
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite buscar un proveedor
Precondición 
El usuario debe estar registrado en el sistema.
Deben existir Proveedores registrados en el sistema
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Buscar proveedor.


2
El sistema presenta la interfaz de búsqueda con los campos de criterio y filtros.


3 
El usuario ingresa los criterios de búsqueda (ej., Nombre del proveedor, Razón social/CUIT, Insumo que provee, Estado).


4 
El sistema ejecuta la consulta y presenta la lista de resultados que cumplen con los criterios.


5 
El usuario selecciona un Proveedor específico del listado.


6
El sistema muestra la información completa del Proveedor: Datos de contacto, Insumos que provee, Historial de Órdenes de Compra y Métricas de Desempeño.


7
El usuario puede seleccionar una opción para iniciar una nueva Orden de Compra o modificar los datos del proveedor.


8
El usuario finaliza la consulta.
Postcondición 


Excepciones 
Paso 




4
Si ningún Proveedor cumple con los criterios de búsqueda, el sistema informa al usuario y sugiere registrar uno nuevo






6
Si el Proveedor está Inactivo, el sistema lo indica claramente, pero permite visualizar su historial.
Rendimiento 
Paso 




 4
1.5 segundos
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 




UC–45
Registrar Proveedor
Objetivos 
asociados 
OBJ–06 Gestionar proveedores y compras
Requisitos 
asociados 
IRQ-06 Información de proveedores y compras
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite registrar un proveedor
Precondición 
El usuario debe estar registrado en el sistema.
El Proveedor a registrar no debe existir previamente en la base de datos.
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Registrar proveedor.


2
El sistema presenta el formulario y el usuario ingresa la información fiscal y de contacto del Proveedor (Razón Social, CUIT/ID Fiscal, Dirección, Teléfono, Email).


3 
El usuario ingresa las Condiciones Comerciales iniciales (ej., plazo de pago, monto mínimo de pedido, días de entrega habituales).


4 
El usuario comienza a asociar los Insumos que este Proveedor suministra, seleccionándolos del catálogo de insumos.


5 
Para cada Insumo asociado, el usuario ingresa el Precio Unitario de Costo actual y la unidad de medida que utiliza el proveedor.


6
El sistema valida que los campos obligatorios estén completos y que el CUIT no esté duplicado


7
El usuario confirma; el sistema crea el registro del Proveedor con estado "Activo".


8
El sistema informa al usuario que el proceso ha finalizado con éxito.
Postcondición 
1. Se registra una nueva instancia de Proveedor en el sistema.
2. El Proveedor y sus costos iniciales están disponibles para generar Órdenes de Compra
Excepciones 
Paso 




2
Si el CUIT/Razón Social del Proveedor ya existe, el sistema emite una alerta, sugiere Buscar proveedor o Modificar proveedor, y bloquea el registro.






5
Si el usuario no ingresa el Precio Unitario de Costo para un insumo asociado, el sistema lo solicita obligatoriamente o impide la asociación


6
Si faltan campos obligatorios, el sistema resalta el error y no permite guardar
Rendimiento 
Paso 




7 
1.5 segundos
Frecuencia 
Baja
Estabilidad 
alta 
Comentarios 




UC–46
Modificar Proveedor
Objetivos 
asociados 
OBJ-06 Gestionar proveedores y compras
Requisitos 
asociados 
IRQ-06 Información de proveedores y compras
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite modificar un proveedor
Precondición 
El usuario debe estar registrado en el sistema.
El Proveedor debe existir y estar registrado en el sistema
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Modificar proveedor.


2
El usuario busca y selecciona el Proveedor que desea actualizar


3 
El sistema muestra la información actual del Proveedor y habilita los campos editables.


4 
El usuario modifica los datos requeridos (ej., teléfono, dirección, plazos de pago).


5 
El usuario puede modificar la lista de Insumos asociados o actualizar el Precio Unitario de Costo de uno o más insumos.


6
El sistema valida que todos los campos obligatorios sigan completos


7
El usuario confirma los cambios e ingresa una justificación obligatoria (ej., proveedor actualizó lista de precios, cambio de domicilio fiscal).


8
El sistema actualiza el registro del Proveedor y los costos de los insumos asociados, y registra el cambio en el Log de Auditoría


9
El sistema informa al usuario que el proceso ha finalizado con éxito.
Postcondición 
1. El registro del Proveedor se actualiza con la nueva información y condiciones comerciales
2. Los costos unitarios de los insumos provistos se actualizan.
3. Se genera un registro inmutable en el Log de Auditoría
Excepciones 
Paso 




2
Si el Proveedor no es encontrado, el sistema informa y finaliza.






5
Si la modificación de un costo es significativa (ej., >10% de aumento), el sistema emite una Alerta pero permite continuar si el usuario lo confirma.


7
Si la justificación de la modificación no es ingresada, el sistema impide guardar los cambios.
Rendimiento 
Paso 




 8
2 segundos
Frecuencia 
Media
Estabilidad 
alta 
Comentarios 




UC–47
Anular/Activar Proveedor
Objetivos 
asociados 
OBJ-06 Gestionar proveedores y compras
Requisitos 
asociados 
IRQ-06 Información de proveedores y compras
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite anular o activar un proveedor 
Precondición 
El usuario debe estar registrado en el sistema.
El Proveedor debe existir en la base de datos
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Anular/activar proveedor.


2
La Administradora busca y selecciona el Proveedor cuyo estado desea modificar.


3 
El sistema muestra el estado actual del Proveedor (Activo/Inactivo).


4 
El usuario selecciona el nuevo estado para el Proveedor (ej., cambia de "Activo" a "Anulado").


5 
El usuario ingresa una justificación obligatoria para el cambio de estado (ej., proveedor descontinuado, problemas de calidad, cese de actividad).


6
El sistema valida que el Proveedor no tenga Órdenes de Compra (OC) pendientes de recepción asociadas.


7
El sistema actualiza el estado del Proveedor y registra el cambio en el Log de Auditoría


8
El sistema informa al usuario que el proceso se completó con éxito.
Postcondición 
1. El estado del Proveedor se actualiza a "Activo" o "Inactivo"
2. Un Proveedor Inactivo no puede ser seleccionado en nuevas Órdenes de Compra
3. Se genera un registro inmutable en el Log de Auditoría
Excepciones 
Paso 




2
Si el proveedor no es encontrado, el sistema informa y finaliza






5
Si la justificación de la modificación no es ingresada, el sistema impide guardar los cambios.


6
Si el Proveedor tiene Órdenes de Compra en estado "Pendiente de Recepción", el sistema bloquea la anulación y solicita primero cancelar esas OC o recibirlas.
Rendimiento 
Paso 




 7
1.5 segundos
Frecuencia 
Baja
Estabilidad 
alta 
Comentarios 




UC–48
Consultar Historial de Compras de Proveedor
Objetivos 
asociados 
OBJ-06 Gestionar proveedores y compras
Requisitos 
asociados 
IRQ-06 Información de proveedores y compras
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite consultar el historial de compras de un proveedor
Precondición 
El usuario debe estar registrado en el sistema.
Deben existir Órdenes de Compra asociadas al proveedor
Secuencia 
Paso 


normal 
1 
El usuario solicita al sistema iniciar el proceso de Consultar historial de compras de proveedor.


2
El usuario busca y selecciona el Proveedor cuyo historial desea revisar.


3 
El sistema presenta la lista cronológica de Órdenes de Compra emitidas a ese Proveedor.


4 
El usuario puede aplicar filtros sobre el historial (ej., por rango de fechas, por estado de la OC, por insumo).


5 
Por cada OC, el sistema muestra: Fecha de Emisión, Costo Total, Estado de la OC (Pendiente/Recibida/Cancelada) y Fecha de Recepción real.


6
El usuario selecciona una OC específica para ver el detalle de los insumos comprados, las cantidades y los precios unitarios de ese momento.


7
El sistema muestra un resumen de Estadísticas clave (ej., total gastado al proveedor en el período, cantidad de OC emitidas).


8
El usuario finaliza la consulta.
Postcondición 


Excepciones 
Paso 




3
Si el Proveedor no tiene historial de compras, el sistema informa al usuario que no hay registros.




Rendimiento 
Paso 




 3
2 segundos
Frecuencia 
Media
Estabilidad 
alta 
Comentarios 




UC–49
Emitir Reporte de Desempeño de Proveedor
Objetivos 
asociados 
OBJ-06 Gestionar proveedores y compras
Requisitos 
asociados 
IRQ-06 Información de proveedores y compras
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite emitir un reporte de desempeño de un proveedor.
Precondición 
El usuario debe estar registrado en el sistema.
Deben existir Órdenes de Compra Recibidas que contengan las fechas de recepción esperadas y reales.
Secuencia 
Paso 


normal 
1 
La Administradora solicita al sistema iniciar el proceso de Emitir reporte de desempeño de proveedor.


2
La Administradora define los parámetros del reporte (ej., Rango de fechas de evaluación, Proveedor(es) a incluir, Métrica a priorizar).


3 
El sistema ejecuta los cálculos sobre el historial para generar las métricas clave:
Cumplimiento de Plazo: Porcentaje de OC entregadas antes o en la fecha esperada.
Precio Competitivo: Comparación del precio promedio de insumos clave del proveedor vs. el promedio del mercado (u otros proveedores).


4 
El sistema genera el reporte en formato tabular, mostrando la calificación de cada proveedor para las métricas seleccionadas.


5 
El sistema incluye una sección de Alertas sobre proveedores con baja puntuación en una o más métricas.


6
El sistema informa a la Administradora que el proceso ha finalizado con éxito.
Postcondición 
1. Se genera un reporte con el análisis de desempeño de los proveedores.
Excepciones 
Paso 




3
Si la información de fechas de recepción es incompleta, el sistema muestra la métrica de Cumplimiento de Plazo como "N/A" y notifica la inconsistencia.






4
Si el rango de fechas es muy extenso y el cálculo es lento, el sistema informa y sugiere reducir el rango.
Rendimiento 
Paso 




 4
6 segundos
Frecuencia 
Baja
Estabilidad 
alta 
Comentarios 






Requisitos No funcionales

NFR–01 
Usabilidad de la interfaz
Objetivos asociados 
OBJ-03, OBJ-04, OBJ-05, OBJ-06
Requisitos asociados 
IRQ-03, IRQ-04, IRQ-06
Descripción 
El sistema deberá contar con una interfaz intuitiva, clara y coherente, que facilite la interacción de los usuarios (administradores, encargados, vendedores y clientes) con las distintas funcionalidades. El diseño debe priorizar la facilidad de aprendizaje, la reducción de errores y la eficiencia en la ejecución de tareas.


Comentarios 
La interfaz deberá ser comprensible para usuarios con distintos niveles de experiencia tecnológica.
El sistema deberá adaptarse a diferentes dispositivos (computadoras de escritorio, tablets y smartphones).




NFR–02
Rendimientos y tiempos de respuesta
Objetivos asociados 
OBJ-01, OBJ-02, OBJ-03, OBJ-04, OBJ-08
Requisitos asociados 
IRQ-01, IRQ-02, IRQ-03, IRQ-04, IRQ-08
Descripción 
El sistema deberá ofrecer un tiempo de respuesta adecuado en la ejecución de las operaciones críticas, garantizando fluidez en la gestión diaria de la pastelería.


Comentarios 
Las operaciones de consulta deberán ejecutarse en un tiempo máximo de 2 segundos para volúmenes de datos medios.
La generación de reportes deberá completarse en un tiempo máximo de 5 segundos para consultas estándar.


NFR–03
Fiabilidad y disponibilidad del sistema
Objetivos asociados 
OBJ-01, OBJ-03, OBJ-04, OBJ-09
Requisitos asociados 
IRQ-01, IRQ-03, IRQ-04, IRQ-09
Descripción 
El sistema deberá garantizar un funcionamiento confiable y continuo, minimizando la ocurrencia de fallas que afecten la gestión de pedidos, producción o entregas. Asimismo, deberá asegurar la disponibilidad de la información registrada para que las operaciones de la pastelería no se vean interrumpidas.


Comentarios 
-



NFR–04
Seguridad en el acceso y manejos de datos
Objetivos asociados 
OBJ-05, OBJ-06, OBJ-09
Requisitos asociados 
IRQ-05, IRQ-06, IRQ-09
Descripción 
El sistema deberá garantizar la seguridad y confidencialidad de la información, tanto en el acceso de los usuarios como en el almacenamiento y transmisión de datos. Se deberán implementar controles de autenticación, autorización y registro de actividades que reduzcan el riesgo de accesos no autorizados o manipulaciones indebidas.


Comentarios 
-


NFR–01 
Mantenibilidad y escalabilidad del sistema
Objetivos asociados 
OBJ-01 al OBJ-09
Requisitos asociados 
IRQ-01 al IRQ-10
Descripción 
El sistema deberá ser fácil de mantener y evolucionar, permitiendo la incorporación de nuevas funcionalidades o la modificación de las existentes sin afectar la estabilidad general. Asimismo, deberá ser escalable, de modo que pueda soportar un aumento en la cantidad de usuarios concurrentes, pedidos, insumos y sucursales sin comprometer el rendimiento ni la confiabilidad.


Comentarios 
La mantenibilidad estará apoyada por la documentación técnica generada durante el ciclo de vida del sistema (UP). La escalabilidad deberá permitir tanto el uso en pequeñas pastelerías familiares como en franquicias con múltiples sucursales.







Matriz de Rastreabilidad Objetivo/Requisitos


OBJ-01


OBJ-N
RI-01






RF-01






RNF






…









Glosario de Términos
Término
Categoría
Comentarios








