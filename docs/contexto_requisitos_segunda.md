Sistema de Gestión para Pastelerías
Documento de Requisitos del Sistema
Versión  01.00
Fecha: 01/09/2025
Realizado por: Castillo Mazo Andrés Luciano
Realizado para: Cliente

Objetivos de la Iteración
Se debe hacer una lista con los objetivos que se esperan alcanzar con el software a desarrollar.


OBJ–01 
Gestionar insumos y materiales
Descripción 
El sistema deberá permitir registrar y controlar los insumos y materiales utilizados en la producción de pastelería (harinas, huevos, frutas, decoraciones, packaging), manteniendo la trazabilidad de sus movimientos, cantidades disponibles y fechas de vencimiento.
Estabilidad 
Alta 
Comentarios 
Base fundamental para garantizar continuidad en la producción y reducir pérdidas por desperdicio o faltantes.


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


OBJ–10
Gestionar usuarios y roles
Descripción 
El sistema deberá permitir el registro de usuarios que interactuarán con él. Dichos usuarios contarán con sus respectivos roles y permisos asignados.
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



IRQ–07
Información de Usuarios y Roles
Objetivos asociados 
OBJ–10 – Gestionar Usuarios y roles
Requisitos asociados 
-
Descripción 
El sistema deberá almacenar y mantener actualizada la información de los usuarios que acceden al sistema y los roles que definen sus permisos. Esto asegura que cada usuario tenga el nivel de acceso apropiado a las funcionalidades del sistema.
Datos específicos 
Identificación del usuario (nombre completo, nombre de usuario).
Datos de contacto (correo electrónico).
Contraseña (almacenada de forma segura).
Rol o roles asignados al usuario (ej. Administrador, Comprador, Producción).
Permisos asociados a cada rol (ej. crear orden de compra, ver inventario, modificar precios).
Estado del usuario (activo, inactivo).
Fecha de creación y última modificación del usuario.


Estabilidad 
alta 


Comentarios 
Este requisito es fundamental para la seguridad y el cumplimiento de normativas internas. Permite auditar las acciones realizadas por cada usuario y garantizar la integridad de los datos.





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

UC–50
 Registrar Usuario
Objetivos 
asociados 
OBJ-10 Gestionar usuarios y roles
Requisitos 
asociados 
IRQ-07 Información de Usuarios y Roles
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite registrar un usuario.
Precondición 
El administrador debe estar registrado en el sistema.
El Rol a asignar debe estar previamente definido en el sistema


Secuencia 
Paso 
Acción 
normal 
1 
El Administrador solicita al sistema iniciar el proceso de Registrar usuario.


2
El sistema presenta el formulario y la Administradora ingresa los datos personales (Nombre, Apellido, DNI, Email).


3 
La Administradora ingresa los datos de acceso: Nombre de Usuario (único) y una contraseña inicial.


4 
El Administrador asigna el Rol que tendrá el usuario (ej., Vendedor).


5 
El sistema valida que el Email o DNI no esté duplicado y que los campos obligatorios estén completos


6
El Administrador confirma; el sistema crea el registro del Usuario con estado "Activo" y registra la acción en el log de auditoría.


7
El sistema informa al Administrador que el proceso ha finalizado con éxito.
Postcondición 
Se registra una nueva instancia de Usuario en el sistema.
El Usuario puede iniciar sesión y acceder a las funcionalidades definidas por su Rol
Excepciones 
Paso 
Acción 


5
Si el DNI ya existe, el sistema informa del duplicado y bloquea el registro.
Si la Contraseña inicial no cumple con la política de seguridad mínima (ej., longitud), el sistema notifica y solicita una contraseña más robusta.




Rendimiento 
Paso 
Cota de tiempo 


6
1.5 segundos
Frecuencia 
Baja
Estabilidad 
alta 
Comentarios 





UC–51
 Modificar Usuario
Objetivos 
asociados 
OBJ-10 Gestionar Usuarios y Roles
Requisitos 
asociados 
IRQ-07 Información de Usuarios y Roles
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite modificar un usuario 
Precondición 
El administrador debe estar registrado en el sistema.
El Usuario a modificar debe estar registrado en el sistema
Secuencia 
Paso 
Acción 
normal 
1 
El Administrador solicita al sistema iniciar el proceso de Modificar usuario.


2
El Administrador busca y selecciona el Usuario que desea actualizar (ej., por Nombre, Apellido o DNI).


3 
El sistema muestra la información actual del Usuario y habilita los campos editables.


4 
El Administrador modifica los datos requeridos: datos personales (Nombre, Apellido, DNI, Email) o datos de acceso (Contraseña, Rol).


5 
[Si modifica el Rol] El Administrador selecciona el nuevo Rol (ej., de "Vendedor" a "Encargado de Producción").


6
El Administrador confirma los cambios e ingresa una justificación obligatoria (ej., cambio de puesto, corrección de DNI, solicitud de blanqueo de clave).


7
El sistema valida los cambios, actualiza el registro del Usuario y registra la acción en el Log de Auditoría


8
El sistema informa al Administrador que el proceso ha finalizado con éxito.
Postcondición 
El registro del Usuario se actualiza con la nueva información.
Si se cambió la contraseña, el Usuario debe usar la nueva clave o la clave temporal para iniciar sesión
Se genera un registro inmutable en el Log de Auditoría.
Excepciones 
Paso 
Acción 


2
Si el Usuario no es encontrado o ya está "Anulado", el sistema informa y finaliza.






4
Si la Administradora intenta modificar el DNI o Nombre de Usuario a un valor que ya pertenece a otro usuario, el sistema notifica el duplicado y bloquea la acción.


6
Si la justificación de la modificación no es ingresada, el sistema impide guardar los cambios.
Rendimiento 
Paso 
Cota de tiempo 


7
1.5 segundos
Frecuencia 
Media
Estabilidad 
alta 
Comentarios 





UC–52
 Anular/Activar Usuario
Objetivos 
asociados 
OBJ-10 Gestionar Usuarios y Roles
Requisitos 
asociados 
IRQ-07 Información de Usuarios y Roles
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite anular o activar un usuario 
Precondición 
El administrador debe estar registrado en el sistema. 
El Usuario a modificar debe estar registrado en el sistema 


Secuencia 
Paso 
Acción 
normal 
1 
El administrador solicita al sistema iniciar el proceso de Anular/Activar usuario


2
El Administrador busca y selecciona el Usuario cuyo acceso desea revocar o restaurar (ej., por DNI o Nombre de Usuario).


3 
El sistema muestra el estado actual del Usuario (Activo/Inactivo).


4 
El Administrador selecciona el nuevo estado para el Usuario (ej., cambia de "Activo" a "Anulado").


5 
El Administrador ingresa una justificación obligatoria para el cambio de estado (ej., desvinculación laboral, licencia, sanción).


6
El sistema valida el cambio y actualiza el estado del Usuario.


7
El sistema registra la acción en el Log de Auditoría (NFR-08), incluyendo la justificación y el usuario que realizó el cambio.


8
El sistema informa a la Administradora que el proceso se completó con éxito.
Postcondición 
El estado del Usuario se actualiza a "Activo" o "Anulado".
Un Usuario Anulado no podrá iniciar sesión
Se genera un registro inmutable en el Log de Auditoría.
Excepciones 
Paso 
Acción 


2
Si el Usuario no es encontrado o es el mismo usuario que ejecuta la acción, el sistema notifica y finaliza.






5
Si la justificación de la anulación no es ingresada, el sistema impide guardar los cambios.
Rendimiento 
Paso 
Cota de tiempo 


7
1.5 segundos
Frecuencia 
Baja
Estabilidad 
alta 
Comentarios 
Este CU es vital para la seguridad y para la gestión de accesos. Al ser una baja lógica, el historial de las acciones del usuario se mantiene en el sistema.



UC–53
 Consultar Bitácora de Auditoría 
Objetivos 
asociados 
OBJ–09 Auditar acciones del sistema
Requisitos 
asociados 
-
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite consultar la bitácora de auditoria 
Precondición 
El usuario debe estar registrado en el sistema.
Deben existir acciones sensibles registradas en el Log de Auditoría
Secuencia 
Paso 
Acción 
normal 
1 
El Administrador solicita al sistema iniciar el proceso de Consultar bitácora de auditoría


2
El sistema presenta la interfaz con los criterios de búsqueda y filtros (ej., Rango de fechas y hora, Usuario, Tipo de Acción (ej., Modificación, Anulación, Blanqueo de Clave), Entidad Afectada).


3 
El Administrador ingresa los criterios y el sistema presenta el listado cronológico de eventos que cumplen con el filtro.


4 
Por cada evento, el sistema muestra: Fecha y Hora del Evento, Usuario que lo ejecutó, Acción Detallada (ej., "Modificó precio del Producto X"), Entidad Afectada y la Justificación asociada.


5 
El Administrador finaliza la consulta.
Postcondición 


Excepciones 
Paso 
Acción 


3
Si ningún evento cumple con los criterios de búsqueda, el sistema informa y solicita nuevos criterios.






4
El sistema no debe permitir al Administrador modificar o eliminar registros del Log de Auditoría.
Rendimiento 
Paso 
Cota de tiempo 


3
4 segundos
Frecuencia 
Baja
Estabilidad 
alta 
Comentarios 





UC–54
 Configurar Parámetros de Alertas
Objetivos 
asociados 
OBJ–07 Automatizar procesos críticos
OBJ–08 Generar reportes e indicadores estratégicos
Requisitos 
asociados 


Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite configurar parámetros de alertas
Precondición 
El usuario debe estar registrado en el sistema.
Los tipos de alertas (stock, producción, finanzas) deben estar definidos en el sistema.
Secuencia 
Paso 
Acción 
normal 
1 
El Administrador solicita al sistema iniciar el proceso de Configurar parámetros de alertas.


2
El sistema presenta las opciones de configuración organizadas por categoría (ej., Alertas de Stock, Alertas de Producción/Venta, Alertas de Costos).


3 
La Administradora selecciona y modifica los valores de los parámetros:
Stock: Valor predeterminado para Stock Mínimo (si no está definido por insumo), Días para Vencimiento Crítico (ej., avisar 7 días antes de caducidad).
|Producción/Venta: Días de Alerta de Pedido Próximo (ej., avisar 2 días antes de la fecha de entrega), Umbral de Sobrecarga de Agenda (ej., > 80% de capacidad).
Costos: Porcentaje de Variación Crítica de Costo (ej., avisar si el costo de un insumo aumenta más del 10% en UC-46).


4 
La Administradora confirma los cambios e ingresa una justificación obligatoria (ej., ajuste de stock mínimo por nueva política de producción).


5 
El sistema valida que los valores ingresados sean lógicos (ej., porcentajes entre 0 y 100, días positivos).


6
El sistema guarda la nueva configuración y registra la acción en el Log de Auditoría


7
El sistema informa al Administrador que el proceso ha finalizado con éxito.
Postcondición 
La configuración del sistema se actualiza con los nuevos parámetros de alerta.
El motor de alertas comenzará a utilizar los nuevos valores de umbral para disparar notificaciones.
Excepciones 
Paso 
Acción 


5
Si la validación detecta un valor ilógico (ej., ingresar -5 días para alerta de vencimiento), el sistema bloquea el guardado y resalta el error.






4
Si la justificación no es ingresada, el sistema impide guardar los cambios.
Rendimiento 
Paso 
Cota de tiempo 


6
1 segundo
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 





UC–55
 Consultar Centro de Notificaciones
Objetivos 
asociados 
OBJ-07 Automatizar procesos críticos
Requisitos 
asociados 


Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite consultar el centro de notificaciones
Precondición 
El usuario debe estar registrado en el sistema.
Deben existir alertas generadas por el sistema que aún no hayan sido marcadas como resueltas.


Secuencia 
Paso 
Acción 
normal 
1 
El usuario solicita al sistema Consultar centro de notificaciones.


2
El sistema presenta el listado de notificaciones/alertas pendientes y las filtra según el Rol del Usuario (ej., el Encargado solo ve alertas de Stock y Producción).


3 
Por cada notificación, el sistema muestra: Tipo de Alerta (ej., Stock Crítico), Fecha/Hora de Creación, Entidad Afectada (ej., Harina 0000, Pedido #123) y Prioridad (Alta, Media, Baja).


4 
El usuario puede aplicar filtros (ej., solo mostrar alertas de Stock Crítico o por antigüedad).


5 
El usuario selecciona una Alerta y realiza una de las siguientes acciones:
Ver el detalle de la entidad afectada (ej., consulta el insumo, incluye UC-34).
Marcar la alerta como "Resuelta/Leída".


6
Si la alerta es marcada como resuelta, el sistema la mueve al historial y la oculta de la vista principal.


7
El usuario finaliza la consulta.
Postcondición 
El estado de la alerta se actualiza si fue marcada como resuelta.
Excepciones 
Paso 
Acción 


3
Si el centro de notificaciones está vacío (todas las alertas resueltas), el sistema informa al usuario y sugiere consultar el historial.






5
Si la alerta es de Stock Crítico y el stock se resuelve automáticamente (ej., por una nueva recepción UC-20), el sistema la marca como "Resuelta por Sistema".
Rendimiento 
Paso 
Cota de tiempo 


3
2 segundos
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 





UC–56
 Gestionar Envío de Notificaciones
Objetivos 
asociados 
OBJ-04 Automatizar procesos críticos
Requisitos 
asociados 


Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite gestionar el envío de notificaciones 
Precondición 
El usuario debe estar registrado en el sistema.
Los tipos de alertas (stock, producción, etc.) deben estar definidos en el sistema
Los canales de envío (ej., servidor de correo) deben estar configurados.
Secuencia 
Paso 
Acción 
normal 
1 
La Administradora solicita al sistema iniciar el proceso de Gestionar envío de notificaciones.


2
El sistema presenta la interfaz con una lista de Tipos de Alerta definidos (ej., Stock Crítico, Pedido Próximo, Vencimiento de Insumo).


3 
La Administradora selecciona un Tipo de Alerta y define los siguientes parámetros:
Rol Destinatario: A qué Rol debe enviarse (ej., Administradora, Encargado de Producción).
Canal de Comunicación: Por dónde se envía (ej., Notificación Interna, Email).
Nivel de Urgencia: (Ej., las alertas críticas van por Email, las de baja prioridad solo por Notificación Interna).


4 
La Administradora confirma los cambios e ingresa una justificación obligatoria (ej., cambio en la política de comunicación de stock).


5 
El sistema guarda la nueva configuración de envío.


6
El sistema registra la acción en el Log de Auditoría.


7
El sistema informa a la Administradora que el proceso ha finalizado con éxito.
Postcondición 
La configuración de envío se actualiza
Las próximas alertas generadas por el sistema se distribuirán según los nuevos parámetros.
Excepciones 
Paso 
Acción 


3
Si la Administradora intenta asignar un Canal que no está configurado (ej., un servidor de correo no válido), el sistema emite una advertencia.






4
Si la justificación de la modificación no es ingresada, el sistema impide guardar los cambios.
Rendimiento 
Paso 
Cota de tiempo 


6
1 segundo
Frecuencia 
Baja
Estabilidad 
alta 
Comentarios 
La información impacta en producción, ventas y stock.



UC–57
 Emitir Reporte de Ventas Consolidadas
Objetivos 
asociados 
OBJ–03 Administrar pedidos y ventas
OBJ–07 Automatizar procesos críticos
Requisitos 
asociados 
IRQ–03 Información de Pedido y Ventas
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite 
Precondición 
El usuario debe estar registrado en el sistema.
Deben existir Ventas y Pagos registrados en el sistema
Secuencia 
Paso 
Acción 
normal 
1 
La Administradora solicita al sistema iniciar el proceso de Emitir reporte de ventas consolidadas.


2
El sistema presenta la interfaz de filtros, y la Administradora define los parámetros del reporte (ej., Rango de fechas, Vendedor, Estado de Venta (Entregada, Anulada), Método de Pago).


3 
El sistema ejecuta la consulta sobre las ventas y pagos (UC-08, UC-12) y consolida los datos.


4 
El sistema calcula las métricas clave del período, incluyendo: Ingreso Neto Total, Total de Ventas Anuladas/Reintegradas, Distribución de Ingresos por Medio de Pago (ej., % Efectivo, % Mercado Pago) y Venta Promedio por Pedido.


5 
El sistema genera el reporte en formato tabular y gráfico (si aplica), mostrando los datos consolidados.


6
La Administradora selecciona la opción para Exportar el reporte


7
El sistema informa a la Administradora que el proceso ha finalizado con éxito.
Postcondición 
Se genera un documento o archivo con el resumen consolidado de las ventas del período
La información está disponible para el análisis financiero y la evaluación de desempeño.
Excepciones 
Paso 
Acción 


2
Si el rango de fechas es inválido o no contiene datos de ventas, el reporte se genera con una leyenda indicando la falta de información.






4
Si el cálculo de una métrica falla por inconsistencia de datos, el sistema omite esa métrica y notifica la inconsistencia.
Rendimiento 
Paso 
Cota de tiempo 


5
5 segundos
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 
Este reporte es una herramienta clave de inteligencia de negocio que permite evaluar la rentabilidad del negocio y el rendimiento comercial.



UC–58
 Emitir Ranking de Productos
Objetivos 
asociados 
OBJ–02 Gestionar recetas y costos de producción
OBJ–03 Administrar pedidos y ventas
OBJ–07 Automatizar procesos críticos
Requisitos 
asociados 
IRQ–03 Información de Pedido y Ventas
IRQ–10 Información de Productos
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite emitir el ranking de productos
Precondición 
El usuario debe estar registrado en el sistema.
Deben existir Ventas y Costos de Producción registrados para los productos.
Secuencia 
Paso 
Acción 
normal 
1 
La Administradora solicita al sistema iniciar el proceso de Emitir ranking de productos.


2
El sistema presenta la interfaz de filtros, y la Administradora define los parámetros del reporte (ej., Rango de fechas, Categoría de producto).


3 
La Administradora selecciona el Criterio de Ranking (ej., "Por Cantidad Vendida", "Por Ingreso Total Generado", "Por Margen de Ganancia Unitario").


4 
El sistema ejecuta la consulta sobre los registros de venta y costo, y ordena los productos según el criterio seleccionado.


5 
El sistema genera el reporte, mostrando el ranking y las métricas clave para cada producto: Posición, Cantidad Vendida, Ingreso Bruto, Costo Total, y Margen de Ganancia (Absoluto y Porcentual).


6
La Administradora selecciona la opción para Exportar el reporte


7
El sistema informa a la Administradora que el proceso ha finalizado con éxito.
Postcondición 


Excepciones 
Paso 
Acción 


2
Si el rango de fechas es inválido o no contiene datos, el reporte se genera con una leyenda indicando la falta de información.






5
Si un producto no tiene Costo de Producción asociado (Receta faltante), el sistema lo muestra, pero con un Margen de Ganancia de "N/A" y resalta la inconsistencia.
Rendimiento 
Paso 
Cota de tiempo 


5
4 segundos
Frecuencia 
Media
Estabilidad 
alta 
Comentarios 
Este reporte es crucial para el OBJ-02, permitiendo a la Administradora tomar decisiones basadas en la rentabilidad real y no solo en la popularidad.



UC–59
 Ver tablero de Control (Dashboard)
Objetivos 
asociados 
OBJ–02 Gestionar recetas y costos de producción
OBJ–03 Administrar pedidos y ventas
OBJ–07 Automatizar procesos críticos
Requisitos 
asociados 
IRQ–01 Información de Insumos y Materiales
IRQ–03 Información de Pedido y Ventas
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicita ver el tablero de control (dashboard)
Precondición 
El usuario debe estar registrado en el sistema.
Deben existir datos operacionales (Ventas, Stock, Producción) para poblar el tablero
Secuencia 
Paso 
Acción 
normal 
1 
El usuario accede al sistema y solicita Ver tablero de control (generalmente es la pantalla de inicio).


2
El sistema ejecuta múltiples consultas y carga los componentes del Dashboard, incluyendo:
Resumen Financiero: Ventas netas del día/semana (UC-57), margen de ganancia promedio.
Alertas Críticas: Listado de insumos en Stock Crítico  y Pedidos próximos a vencer.
Producción: Resumen de la carga de la Agenda de Producción (UC-29) y el estado de las OP.
Productos: Los 3 productos más vendidos/rentables del último período (UC-58).


3 
El usuario puede interactuar con un componente (ej., hacer clic en la alerta de Stock Crítico) para navegar al módulo detallado de consulta.


4 
El usuario puede actualizar los datos del Dashboard para obtener una vista en tiempo real.


5 
El usuario finaliza la consulta
Postcondición 


Excepciones 
Paso 
Acción 


2
Si la carga de datos de un componente es lenta o falla (ej., el cálculo de ventas), el sistema muestra un indicador de "Datos no disponibles" o "Cargando" sin afectar el resto del Dashboard.






3
Si no hay alertas críticas, el componente correspondiente muestra el mensaje "Estado de stock/producción saludable".
Rendimiento 
Paso 
Cota de tiempo 


2
3 segundos
Frecuencia 
Alta
Estabilidad 
alta 
Comentarios 
Este CU es la cara de la Inteligencia de Negocio y debe ser eficiente y amigable. Es la consolidación de varios reportes funcionales.



UC–60
 Registrar Rol/Permiso
Objetivos 
asociados 
OBJ-09 Auditar acciones del sistema
Requisitos 
asociados 
IRQ-07 Información de Usuario y Roles
Actores asociados


Descripción 
El sistema deberá comportarse tal como se describe en el siguiente caso de uso cuando un usuario solicite registrar un rol o permise 
Precondición 
El usuario debe estar registrado en el sistema.
Los Casos de Uso y funcionalidades del sistema deben estar definidos
Secuencia 
Paso 
Acción 
normal 
1 
La Administradora solicita al sistema iniciar el proceso de Registrar rol/permiso.


2
El sistema presenta la interfaz con el listado de roles existentes (ej., Vendedor, Encargado, Administradora) y la opción de Crear nuevo rol o Modificar uno existente.


3 
[Si crea un nuevo Rol] La Administradora ingresa el Nombre del nuevo Rol y una Descripción.


4 
La Administradora define los Permisos de Acceso al Rol, seleccionando los Casos de Uso específicos que el rol podrá ejecutar (ej., "Vendedor" tiene permiso para UC-01, UC-05, UC-38).


5 
La Administradora confirma los cambios e ingresa una justificación obligatoria (ej., creación de nuevo rol 'Cajero' para fines de prueba).


6
El sistema valida la información, guarda la definición del Rol con sus permisos asociados, y registra la acción en el Log de Auditoría


7
El sistema informa a la Administradora que el proceso ha finalizado con éxito.
Postcondición 
Un nuevo Rol se registra y está disponible para ser asignado a usuarios
La política de seguridad se actualiza con la nueva definición de permisos. Se genera un registro inmutable en el Log de Auditoría.
Excepciones 
Paso 
Acción 


3
Si el Nombre del Rol ya existe, el sistema informa del duplicado.




4
El sistema debe proteger el Rol Administradora de ser modificado o eliminado, asegurando que siempre exista un usuario con acceso total.


5
Si la justificación no es ingresada, el sistema impide guardar los cambios.
Rendimiento 
Paso 
Cota de tiempo 


6
1.5 segundos
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








