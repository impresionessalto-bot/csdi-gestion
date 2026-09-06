# CSDI ERP — Reglas de Negocio

> Documento funcional central. Estas reglas representan el comportamiento que el software debe preservar. Cuando exista una regla más reciente aprobada por el negocio, esta documentación debe actualizarse antes de implementar código contradictorio.

## 1. Principio general

CSDI ERP no es solamente un CRUD. Cada módulo representa procesos de negocio que deben conservar:

- trazabilidad;
- continuidad histórica;
- aislamiento entre empresas;
- integridad de stock;
- consistencia documental;
- permisos;
- auditabilidad.

Ante una contradicción entre una solución rápida y una regla de negocio, preservar la regla y escalar la decisión.

---

## 2. Multiempresa

Una instalación puede administrar múltiples empresas.

Reglas:

1. Los datos empresariales deben quedar aislados.
2. Un usuario puede tener contexto/pertenencia según el modelo real.
3. Roles y permisos deben poder configurarse por empresa.
4. `vendedor` no debe ser un rol global rígido.
5. Nunca confiar en `empresa_id` enviado por el navegador.
6. Toda escritura debe validar autorización en servidor.

---

## 3. Clientes

El cliente es una entidad comercial reutilizable.

Debe evitarse crear clientes duplicados para cada módulo.

Las ventas, servicios, equipos y facturación deben relacionarse con el cliente existente.

---

## 4. Equipos

Un equipo físico es distinto de su modelo.

```text
Modelo de equipo
      ↓
Equipo físico
      ↓
Cliente
      ↓
Historial / lecturas / servicios
```

Conceptos de propiedad conocidos:

- CSDI;
- Cliente;
- TNG.

TNG no debe mezclarse con el circuito normal de Contadores.

---

## 5. Contadores

### 5.1 Equipos Mono

Un equipo monocromático utiliza un contador principal.

### 5.2 Equipos Color

Un equipo color conserva:

- contador monocromático;
- contador color.

### 5.3 Lectura

Una lectura debe:

- identificar cliente;
- identificar equipo;
- identificar mes/año;
- validar que el equipo pertenece al cliente;
- impedir valores negativos;
- evitar duplicación de período;
- mantener relación con lectura anterior;
- preservar historial.

### 5.4 Consumo

Conceptualmente:

```text
copias = contador_actual - contador_anterior
```

Para color:

```text
copias_color = contador_actual_color - contador_anterior_color
```

Las fórmulas exactas deben respetar el código real si existe una regla adicional.

---

## 6. Swap de equipos

El reemplazo/intercambio de equipos es transversal.

Debe utilizar `SwapService` cuando esté disponible.

Objetivos:

- conservar continuidad;
- identificar equipo saliente;
- identificar equipo entrante;
- registrar motivo;
- registrar usuario;
- conservar historial;
- evitar perder lecturas anteriores.

No duplicar esta lógica dentro de Contadores.

---

## 7. TNG

TNG posee reglas distintas.

Regla conocida:

> TNG cobra por visita, no por copias.

Una visita puede originarse por:

- lectura de contador/visita correspondiente;
- servicio técnico;
- otras categorías definidas por el negocio.

No convertir automáticamente un proceso TNG en un proceso de copias.

Los equipos TNG utilizados para informes TNG no deben alterar la lógica de Contadores.

---

## 8. Inventario

El inventario debe ser trazable.

Regla fundamental:

```text
Entrada → aumenta stock
Salida  → disminuye stock
```

Las operaciones que producen stock deben tener un origen identificable.

---

## 9. Compras

Una compra representa adquisición a un proveedor.

Proceso conceptual:

```text
Proveedor
 ↓
Compra
 ↓
Items
 ↓
Recepción
 ↓
Stock
```

No asumir que registrar la orden de compra equivale a recibir mercadería.

Si el negocio diferencia pedido y recepción, el stock debe modificarse al momento definido como recepción.

---

## 10. Recepción de compras

Debe comparar solicitado vs recibido.

Ejemplo:

```text
Solicitado 10
Recibido    8
Faltante    2
```

La diferencia debe quedar trazable.

Estados conceptuales:

```text
PENDIENTE
RECIBIDA
PARCIAL
CON_DIFERENCIAS
```

Usar los estados reales del sistema si ya están definidos.

---

## 11. Proveedores y múltiples listas

Un producto puede tener varios proveedores.

```text
TONER XYZ
 ├── Proveedor A → $100
 ├── Proveedor B → $95
 └── Proveedor C → $110
```

El sistema debe poder comparar:

- precio;
- proveedor;
- vigencia;
- unidad/múltiplo;
- condiciones;
- costo efectivo cuando corresponda.

El proveedor no define la identidad del producto.

---

## 12. Comparación de precios

Cuando el usuario carga un pedido y selecciona un producto/proveedor, el ERP puede mostrar alternativas.

La comparación debe considerar:

1. mismo producto;
2. misma unidad de comparación;
3. impuestos;
4. descuentos;
5. mínimo de compra;
6. costo logístico cuando esté disponible;
7. fecha de actualización;
8. proveedor;
9. lista vigente.

No mostrar un precio como directamente comparable si las unidades son diferentes sin normalización.

---

## 13. Ventas

Proceso conceptual:

```text
Cliente
 ↓
Venta
 ↓
Items
 ↓
Confirmación
 ↓
Salida de stock
 ↓
Facturación si corresponde
```

Antes de confirmar:

- validar artículos;
- cantidades;
- precios;
- impuestos;
- stock según regla del negocio;
- permisos;
- empresa.

---

## 14. Listas de precios de venta

El precio mostrado al usuario puede depender de:

- empresa;
- cliente;
- lista;
- artículo;
- vigencia;
- impuestos;
- promociones/descuentos futuros.

Nunca asumir que existe una única lista global si el modelo de negocio requiere varias.

---

## 15. Impuestos

Los impuestos deben ser explícitos cuando forman parte del documento.

El usuario puede necesitar editar impuestos por línea en procesos como Ventas, pero el servidor debe validar que los valores son permitidos.

No confiar en el cálculo del navegador.

---

## 16. Facturación

Facturación debe recibir datos desde los módulos de origen sin duplicar reglas.

Orígenes posibles:

- ventas;
- servicios;
- contratos;
- contadores;
- TNG;
- otros procesos autorizados.

El módulo debe poder identificar el origen de cada documento.

---

## 17. DocumentosIA

DocumentosIA puede clasificar y preparar información para otros módulos.

No debe convertir automáticamente un documento en una operación contable/stock sin validaciones y reglas de negocio.

Flujo seguro:

```text
Documento
 ↓
Procesamiento
 ↓
Extracción
 ↓
Validación
 ↓
Confirmación
 ↓
Operación de negocio
```

---

## 18. Reorder Engine

El motor de reposición recomienda compras.

Regla inicial conceptual:

```text
Necesidad = Stock objetivo - Stock actual - compras pendientes
```

Puede evolucionar para incorporar consumo histórico, estacionalidad, lead time y múltiplos de compra.

Una recomendación no es una compra confirmada.

---

## 19. PUNTO — Red de Compras

PUNTO conecta comercios independientes para comprar con escala.

Marca:

> PUNTO — Red de Compras
> Compramos juntos. Conseguimos más.

Manifiesto funcional:

> PUNTO no es un mayorista tradicional. Es una red de compras que agrega demanda para obtener escala, mejores precios, condiciones y logística.

Flujo:

```text
Necesidad del comercio
 ↓
Oportunidad PUNTO
 ↓
Participación
 ↓
Demanda agregada
 ↓
Negociación
 ↓
Precio por volumen
 ↓
Pedido individual
 ↓
Distribución
 ↓
Recepción
 ↓
Stock
```

---

## 20. Compra conjunta PUNTO

Estados conceptuales:

```text
BORRADOR
ABIERTA
META_ALCANZADA
CERRADA
CONFIRMADA_PROVEEDOR
EN_TRANSITO
RECIBIDA_CSDI
EN_DISTRIBUCION
FINALIZADA
CANCELADA
```

No implementar estos estados en DB sin verificar el diseño definitivo.

---

## 21. Pedido individual PUNTO

Cada participante debe tener un pedido individual derivado de la compra conjunta.

Estados conceptuales:

```text
PENDIENTE
CONFIRMADO
PAGADO
PREPARANDO
DESPACHADO
RECIBIDO
CON_DIFERENCIA
CANCELADO
```

La compra conjunta no debe convertir a los participantes en una sola empresa contable.

---

## 22. Recepción PUNTO

La recepción individual debe poder:

- confirmar cantidades;
- registrar faltantes;
- registrar sobrantes;
- registrar daños/incidencias;
- generar movimiento de stock;
- actualizar estado del pedido.

QR/código puede acelerar la identificación del pedido, pero nunca sustituir las validaciones de servidor.

---

## 23. Devoluciones

Las devoluciones deben conservar referencia al movimiento/documento original cuando sea posible.

No borrar el movimiento anterior para simular una devolución.

Crear el movimiento compensatorio correspondiente.

---

## 24. Ajustes de stock

Un ajuste debe ser explícito y auditable.

Debe registrar motivo y usuario cuando corresponda.

No utilizar ajustes silenciosos para corregir errores de programación.

---

## 25. Precios

Diferenciar:

```text
Precio de costo
Precio de lista
Precio negociado
Precio de venta
Precio con impuestos
Precio efectivo
```

No sobrescribir un precio histórico simplemente porque cambió la lista actual.

Los documentos históricos deben conservar sus valores aplicados.

---

## 26. Fechas y períodos

Los procesos periódicos deben distinguir:

- fecha de operación;
- fecha de documento;
- período contable;
- mes/año de contador;
- vigencia de precio.

No usar la fecha actual para reemplazar un período de negocio explícito.

---

## 27. Auditoría

Operaciones críticas deben ser reconstruibles.

Ejemplos:

- quién modificó un precio;
- quién recibió mercadería;
- quién realizó un ajuste;
- quién cambió un equipo;
- quién confirmó una venta;
- quién modificó permisos.

---

## 28. Regla de prioridad

Cuando existan varias fuentes de información:

```text
Regla aprobada del negocio
        ↓
Esquema real de DB
        ↓
Código existente
        ↓
Documentación
        ↓
Suposición
```

La suposición nunca debe prevalecer sobre una evidencia real.
