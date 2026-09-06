# CSDI ERP — Modelo de Datos y Persistencia

> Documento vivo. Los nombres y relaciones definitivos deben validarse contra el esquema SQL real antes de generar código. Las entidades mencionadas aquí provienen del modelo funcional conocido del proyecto y no autorizan al agente a inventar columnas.

## 1. Principio fundamental

La base de datos es la fuente de verdad del estado persistente del ERP.

Antes de modificar SQL, el agente debe inspeccionar:

- tabla real;
- columnas;
- tipos;
- nullabilidad;
- claves;
- índices;
- relaciones;
- valores ENUM existentes;
- datos dependientes;
- código consumidor.

Nunca asumir que un nombre usado en una conversación, documentación o clase coincide con la columna real.

---

## 2. Convenciones

Preferir:

- claves primarias numéricas existentes;
- claves foráneas reales;
- índices para campos de búsqueda frecuente;
- `DECIMAL` para importes monetarios;
- tipos enteros adecuados para cantidades/IDs;
- fechas almacenadas en tipos nativos;
- restricciones de unicidad cuando una regla de negocio realmente lo requiera.

No cambiar tipos por estética.

---

## 3. Mapa funcional conocido

```text
EMPRESA
 │
 ├── USUARIOS / ROLES / PERMISOS
 │
 ├── CLIENTES
 │    ├── EQUIPOS
 │    │    └── REGISTROS DE CONTADORES
 │    ├── VENTAS
 │    │    └── VENTAS_DETALLE
 │    └── CONTRATOS / SERVICIOS
 │
 ├── PROVEEDORES
 │    ├── LISTAS DE PRECIOS
 │    └── COMPRAS
 │         └── COMPRAS_ITEMS
 │
 ├── INSUMOS / PRODUCTOS
 │    └── STOCK / MOVIMIENTOS
 │
 ├── FACTURACIÓN
 │
 └── DOCUMENTOS
```

La relación exacta y la existencia de `empresa_id` deben comprobarse en el esquema.

---

## 4. Clientes

Entidad comercial central.

Conceptualmente relaciona:

- empresa;
- localidad;
- equipos;
- ventas;
- contratos;
- facturación;
- servicios.

Regla: ningún módulo debe duplicar la entidad cliente para resolver una necesidad local.

---

## 5. Localidades

Catálogo territorial reutilizado por clientes, equipos y procesos que requieran ubicación.

Debe evitarse almacenar nombres de localidad repetidos en múltiples tablas cuando exista una relación normalizada.

---

## 6. Equipos

Entidad utilizada para equipos de impresión y otros equipos gestionados por el ERP.

Campos conocidos/esperados del modelo actual incluyen conceptos como:

- `codigo_interno`;
- `cliente_id`;
- `modelo_id`;
- `serie`;
- `identificador_externo`;
- `activo`;
- `propiedad`;
- `estado`.

No asumir que todos estos nombres son exactos sin verificar el esquema.

### Propiedad

Se han utilizado valores conceptuales:

```text
CSDI
Cliente
TNG
```

Pero TNG no debe mezclarse con el circuito de Contadores.

---

## 7. Modelos de equipos

La entidad de modelo debe separar las características del equipo de la instancia física.

Conceptos conocidos:

- marca;
- nombre/modelo;
- tipo Mono/Color;
- rendimiento `yield_k` cuando corresponda.

Un equipo físico pertenece a un modelo; el modelo no representa la lectura histórica de un equipo.

---

## 8. Registros de contadores

La tabla `registros` representa lecturas periódicas.

Conceptos conocidos:

- `cliente_id`;
- `equipo_id`;
- `periodo_mes`;
- `periodo_anio`;
- `contador_actual`;
- `contador_actual_color`;
- `contador_anterior`;
- `contador_anterior_color`;
- `copias`;
- `copias_color`.

### Regla de período

Un equipo no debe tener dos lecturas válidas para el mismo período si el modelo de negocio define unicidad mensual.

La validación debe existir tanto en Service como, cuando sea viable, mediante restricción de base de datos.

### Equipo Mono

No necesita un contador color funcional.

### Equipo Color

Debe conservar contador mono y contador color.

---

## 9.1 Continuidad por Swap

El intercambio de equipos utiliza el servicio transversal `SwapService`.

Tablas/entidades conocidas relacionadas:

- `equipos`;
- `registros`;
- `historial_cambios`.

`historial_cambios` tiene conceptos conocidos:

- `equipo_antiguo_id`;
- `equipo_nuevo_id`;
- `cliente_id`;
- `localidad_id`;
- `fecha_cambio`;
- `motivo`;
- `usuario_id`.

El nombre y tipo exactos deben verificarse.

No crear una tabla de Swap específica de Contadores si ya existe el servicio transversal.

---

## 10. Insumos / productos

`insumos` representa el catálogo de artículos utilizados por Inventario, Compras y Ventas.

Debe separar:

```text
Producto/insumo
        ≠
Proveedor
        ≠
Código del proveedor
        ≠
Precio del proveedor
        ≠
Stock
```

Esto es fundamental para soportar múltiples proveedores por artículo.

---

## 11. Proveedores

`proveedores` representa empresas/personas que suministran productos o servicios.

Un producto puede relacionarse con varios proveedores.

El diseño futuro de listas debe permitir:

```text
Producto
 ├── Proveedor A → código + precio + vigencia
 ├── Proveedor B → código + precio + vigencia
 └── Proveedor C → código + precio + vigencia
```

No duplicar el producto por proveedor.

---

## 12. Listas de precios de proveedores

La arquitectura debe poder evolucionar hacia listas con:

- proveedor;
- producto;
- código externo;
- descripción del proveedor;
- precio;
- moneda;
- impuestos cuando corresponda;
- unidad de compra;
- múltiplo mínimo;
- vigencia;
- fecha de actualización;
- empresa/contexto cuando sea necesario.

La comparación debe hacerse sobre una identidad de producto común, no sobre texto únicamente.

---

## 13. Compras

Entidades conocidas:

- `compras`;
- `compras_items`;
- `proveedores`;
- `insumos`;
- stock/movimientos.

Conceptualmente:

```text
Compra
 └── Items
      ├── producto
      ├── cantidad
      ├── precio
      ├── impuestos
      └── subtotal/total
```

Una compra puede tener estados propios. No reutilizar estados de ventas sin una razón explícita.

---

## 14. Stock

Entidades conocidas:

- `stock_movimientos`;
- `stock_insumos`.

El diseño debe preservar trazabilidad.

```text
Saldo de stock
     ↑
Movimientos
     ↑
Documentos de origen
```

### Movimientos típicos

```text
COMPRA / RECEPCIÓN   → ENTRADA
VENTA                → SALIDA
AJUSTE POSITIVO      → ENTRADA
AJUSTE NEGATIVO      → SALIDA
DEVOLUCIÓN           → según origen
TRANSFERENCIA        → salida + entrada
```

La nomenclatura exacta debe respetar los valores reales del sistema.

---

## 15. Ventas

Entidades conocidas:

- `ventas`;
- `ventas_detalle`;
- clientes;
- insumos/productos;
- stock;
- facturación cuando corresponda.

Una venta confirmada debe producir el efecto de stock definido por el negocio.

La venta no debe confiar en precios enviados por el navegador si el servidor puede recalcularlos.

---

## 16. Facturación

Entidades conocidas:

- `facturacion_control`;
- `facturacion_origenes`;
- `facturas`;
- `facturacion_items`.

También se han utilizado:

- `historial_informes`;
- `servicios`;
- `ventas`;
- contratos;
- tarifas.

La estructura definitiva debe verificarse antes de modificar el módulo.

No crear una tabla paralela para resolver una ausencia que corresponda a `facturacion_items` o a otra entidad existente.

---

## 17. Servicios y contratos

El ERP contempla servicios técnicos, contratos y tarifas.

Para CSDI existen reglas de tarifas relacionadas con:

- servicio técnico eventual;
- contador general;
- contador ARCA.

TNG posee un circuito distinto y no debe mezclarse con Contadores.

TNG cobra conceptualmente por visitas, no por copias.

---

## 18. TNG

Entidades conocidas:

- `visitas_tng`;
- `contratos_tng`;
- `tarifas_tng`;
- `reglas_cobro_tng`;
- equipos/relaciones específicas TNG según esquema real.

Regla fundamental:

> Los equipos TNG utilizados para informes TNG no deben mezclarse con el circuito de Contadores de CSDI.

---

## 19. DocumentosIA

La persistencia documental debe poder conservar:

- archivo;
- tipo;
- estado;
- módulo destino;
- metadatos extraídos;
- errores;
- timestamps;
- relación con documento de negocio cuando exista.

Los estados conceptuales conocidos son:

```text
PENDIENTE
PROCESANDO
PROCESADO
ERROR
```

---

## 20. PUNTO — Red de Compras

PUNTO debe integrarse con el catálogo y stock existentes.

Tablas propuestas conceptualmente:

```text
punto_catalogo
punto_catalogo_precios
punto_compras_conjuntas
punto_compras_items
punto_participantes
punto_pedidos
punto_pedidos_items
punto_recepciones
punto_recepciones_items
punto_incidencias
punto_proveedores
```

Estas tablas son **propuestas**, no deben crearse automáticamente sin una decisión de diseño y sin revisar el modelo real.

Flujo:

```text
Necesidad
 ↓
Oportunidad PUNTO
 ↓
Demanda agregada
 ↓
Precio por volumen
 ↓
Pedido individual
 ↓
Recepción
 ↓
Stock
```

---

## 21. Integridad referencial

Cuando existan claves foráneas, respetarlas.

Antes de eliminar:

- verificar referencias;
- determinar si corresponde `RESTRICT`, `SET NULL` o eliminación en cascada;
- considerar auditoría/historial.

No usar `ON DELETE CASCADE` por comodidad en entidades históricas/contables sin justificación.

---

## 22. Índices

Indexar consultas críticas, especialmente combinaciones utilizadas en:

- empresa + entidad;
- cliente + equipo;
- equipo + período;
- producto + proveedor;
- documentos por estado;
- stock por producto;
- búsquedas frecuentes.

No crear índices indiscriminadamente.

---

## 23. Migraciones

Toda modificación estructural debe incluir SQL/migración reproducible.

Una migración debe:

1. comprobar precondiciones cuando sea necesario;
2. modificar estructura;
3. crear índices/FK;
4. ser segura respecto del estado conocido;
5. documentar reversión si el sistema lo requiere.

Nunca modificar producción manualmente y dejar el repositorio desactualizado.

---

## 24. Datos derivados

Distinguir:

```text
Dato fuente
Dato calculado
Snapshot histórico
```

Ejemplo:

```text
contador_actual + contador_anterior
              ↓
          copias calculadas
```

Si `copias` es un dato histórico persistido por razones de auditoría, no sobrescribirlo sin criterio.

---

## 25. Regla de no invención

Si el agente necesita una columna y no sabe si existe:

```text
NO inventar
↓
Inspeccionar esquema
↓
Confirmar
↓
Implementar
```

Si no puede inspeccionar la base:

- marcar la dependencia;
- no afirmar que la migración es compatible;
- preparar una propuesta SQL separada.

---

## 26. Definición de consistencia

Una operación persistente está correctamente implementada cuando:

- las relaciones son válidas;
- los saldos son trazables;
- los documentos de origen son identificables;
- la empresa está correctamente aislada;
- el historial crítico no se pierde;
- una excepción no deja una operación a medias.
