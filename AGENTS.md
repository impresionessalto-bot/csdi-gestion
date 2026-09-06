# AGENTS.md — CSDI ERP

## 1. Propósito

Este repositorio contiene el ERP de CSDI y su ecosistema de módulos. El agente que trabaje sobre este proyecto debe actuar como **arquitecto, desarrollador, integrador y auditor**, no como un generador aislado de código.

La prioridad es mantener un sistema estable, coherente, seguro, multiempresa y evolutivo. Antes de modificar código existente, el agente debe comprender cómo encaja el cambio en la arquitectura y en los procesos de negocio.

---

## 2. Stack y restricciones

- PHP 8.3+
- MySQL/MariaDB
- PDO
- MVC propio; **NO Laravel** salvo decisión explícita del proyecto
- Bootstrap 5
- JavaScript moderno, Fetch/AJAX cuando corresponda
- Composer/autoload PSR-4 cuando esté disponible
- Hosting objetivo: Ferozo/shared hosting
- No asumir acceso SSH, cron, comandos del sistema o extensiones no confirmadas
- `exec()`, `shell_exec()` y `escapeshellarg()` pueden estar deshabilitados en producción
- No depender de Tesseract/OCR del sistema operativo salvo que el entorno lo confirme explícitamente

---

## 3. Arquitectura obligatoria

Respetar la separación:

```text
Route
  ↓
Middleware / Auth
  ↓
Controller
  ↓
Service
  ↓
Repository
  ↓
PDO / MySQL
```

Y para presentación:

```text
Controller → View
                 ↓
             Bootstrap 5
                 ↓
          JS / Fetch / AJAX
```

### Reglas

- Controllers delgados: reciben la petición, validan entrada básica, delegan al Service y preparan la respuesta/vista.
- Services contienen reglas de negocio y coordinación de operaciones.
- Repositories contienen acceso a datos y SQL.
- Views no deben contener reglas de negocio ni consultas SQL.
- Evitar duplicar lógica entre módulos.
- Reutilizar Core, Components y servicios transversales existentes antes de crear equivalentes nuevos.
- Usar `Container`/inyección de dependencias del proyecto cuando corresponda.
- Usar `BaseController`, `BaseRepository` y `BaseService` si existen y son compatibles con el cambio.
- No introducir un framework nuevo para resolver un problema que el núcleo existente ya puede resolver.

---

## 4. Regla de oro antes de programar

Nunca modificar un archivo solamente porque su nombre parece correcto.

Antes de implementar una funcionalidad:

1. Inspeccionar la estructura del módulo.
2. Revisar rutas relacionadas.
3. Revisar Controller, Service y Repository.
4. Revisar vistas y JavaScript.
5. Revisar tablas y columnas realmente existentes.
6. Buscar funcionalidades equivalentes ya implementadas.
7. Revisar relaciones con otros módulos.
8. Identificar riesgos de regresión.
9. Proponer el plan de cambio si el alcance es significativo.
10. Implementar y auditar el flujo completo.

Si el código y la base de datos no coinciden, **no inventar nombres de columnas**: documentar la discrepancia y adaptar el código a la estructura real o proponer la migración necesaria.

---

## 5. Multiempresa — obligatorio

El ERP es multiempresa.

Toda funcionalidad nueva debe considerar aislamiento por empresa cuando el dato pertenezca a una empresa.

- No mezclar clientes, proveedores, productos, stock, ventas, compras, pedidos o configuraciones entre empresas.
- No asumir que un usuario pertenece a una única empresa si el modelo permite múltiples empresas.
- Las consultas deben filtrar por el contexto de empresa correspondiente.
- Las operaciones de escritura deben validar que el registro pertenece a la empresa autorizada.
- No confiar únicamente en IDs enviados por el navegador.
- Los roles y permisos deben poder configurarse por empresa.
- El rol `vendedor` **no es global por definición**; debe poder habilitarse/configurarse por empresa.

---

## 6. Seguridad

Toda implementación debe considerar como mínimo:

- PDO con consultas preparadas.
- CSRF en operaciones mutables.
- Autenticación y autorización en servidor.
- Validación estricta de IDs y parámetros.
- Escape/sanitización de salida HTML según contexto.
- Prevención de SQL Injection.
- Prevención de XSS.
- No exponer excepciones, SQL ni información sensible al usuario final.
- No aceptar como confiable ningún dato de empresa, usuario, precio, stock o permiso enviado por JavaScript.
- Verificar permisos antes de leer información sensible y antes de modificarla.
- Operaciones críticas dentro de transacciones cuando impliquen varias escrituras relacionadas.

---

## 7. Base de datos

Antes de crear o modificar tablas:

- inspeccionar el esquema actual;
- respetar nombres y tipos existentes;
- identificar claves primarias/foráneas;
- revisar índices y unicidad;
- revisar datos existentes y compatibilidad hacia atrás.

Las migraciones deben ser explícitas y reproducibles.

No borrar ni renombrar columnas/tablas existentes sin analizar dependencias y migración.

Evitar almacenar datos derivados si pueden calcularse de forma segura, salvo que exista una razón de rendimiento/auditoría.

---

## 8. Inventario

El stock es un proceso contable/operativo y no debe modificarse de forma arbitraria.

Regla conceptual:

```text
Compra / Recepción → aumenta stock
Venta / Salida      → disminuye stock
Ajuste              → movimiento explícito
```

Siempre que sea posible, registrar movimientos en `stock_movimientos` o en el mecanismo transversal vigente.

Cada movimiento debe conservar trazabilidad de:

- producto/insumo;
- cantidad;
- signo o tipo de movimiento;
- origen;
- documento relacionado;
- fecha;
- empresa;
- usuario cuando corresponda.

No implementar `stock = stock + X` como única fuente de verdad cuando el sistema dispone de movimientos e historial.

---

## 9. Compras y proveedores

Las compras deben integrarse con:

- proveedores;
- insumos/productos;
- listas de precios;
- compras;
- compras_items;
- stock;
- documentos y comprobantes cuando corresponda.

El sistema debe poder evolucionar hacia múltiples listas de proveedores y comparación de precios sin duplicar el catálogo de productos.

La identidad del producto debe separarse de la identidad del proveedor: un mismo producto puede tener múltiples proveedores, códigos externos y precios.

---

## 10. PUNTO — Red de Compras

PUNTO es la futura red de compras conjunta integrada al ERP.

Concepto de marca:

> PUNTO — Red de Compras
> Compramos juntos. Conseguimos más.

PUNTO no debe crear un inventario paralelo. Debe integrarse con el catálogo, proveedores, listas de precios, compras y stock existentes.

Conceptualmente:

```text
Inventario del comercio
        ↓
Motor de reposición
        ↓
Necesidad detectada
        ↓
Catálogo PUNTO
        ↓
Compra conjunta
        ↓
Pedido individual
        ↓
Recepción
        ↓
Movimiento de stock
```

La arquitectura debe permitir posteriormente:

- catálogo PUNTO;
- compras conjuntas;
- demanda agregada de múltiples empresas;
- escalas de precio por volumen;
- pedidos individuales derivados de una compra conjunta;
- recepción de mercadería;
- diferencias/incidencias;
- trazabilidad logística;
- comparación contra listas de proveedores normales.

No implementar todavía funcionalidades de PUNTO como sistema aislado si pueden reutilizar entidades existentes.

---

## 11. Motor de reposición (Reorder Engine)

El motor debe comenzar simple y evolucionar progresivamente.

Modelo inicial:

```text
Necesidad sugerida = Stock objetivo - Stock actual - compras pendientes
```

Debe considerar, cuando estén disponibles:

- stock actual;
- punto de reposición;
- stock objetivo;
- compras pendientes;
- consumo histórico;
- ventas proyectadas;
- unidad de medida;
- múltiplos de compra;
- proveedor/precio;
- oportunidad PUNTO.

Las sugerencias son recomendaciones: no generar compras reales sin una acción/autorización definida por el negocio.

---

## 12. Recepción de mercadería

La recepción debe ser transaccional y trazable.

Flujo esperado:

```text
Pedido
 ↓
Recepción
 ↓
Validación de cantidades
 ↓
Incidencias si existen diferencias
 ↓
Movimiento de stock
 ↓
Actualización de estado
```

Debe soportar diferencias entre solicitado y recibido.

Ejemplo:

```text
Solicitado: 20
Recibido:   19
Faltante:    1
```

No confirmar automáticamente cantidades no verificadas.

Cuando exista QR/código de recepción, debe identificar el pedido/documento; no debe convertirse en una autorización para modificar stock sin validaciones de servidor.

---

## 13. Ventas

Las ventas deben respetar:

- empresa;
- cliente;
- artículos;
- cantidades;
- precios;
- impuestos;
- listas de precios;
- stock;
- permisos.

Una venta confirmada debe integrarse con el movimiento de stock correspondiente según las reglas del negocio.

No asumir que todos los usuarios vendedores tienen permisos globales.

---

## 14. Contadores

El módulo de Contadores trabaja con continuidad histórica de lecturas.

Reglas conocidas:

- Equipos mono: un contador relevante.
- Equipos color: contador monocromático + contador color.
- Validar asociación equipo/cliente.
- Validar período mes/año.
- Evitar duplicar lecturas del mismo período.
- No aceptar contadores negativos.
- Mantener historial.

### Swap transversal

El equipo no debe tratarse como una entidad aislada cuando existe reemplazo/intercambio.

El `SwapService` transversal debe conservar continuidad e historial de lecturas y registrar el cambio de equipo.

No duplicar una lógica alternativa de Swap dentro de Contadores.

---

## 15. DocumentosIA

DocumentosIA debe poder alimentar otros módulos sin duplicar el procesamiento.

Tipos de documentos previstos incluyen, entre otros:

- FACTURA
- TICKET
- REMITO
- INFORME_TECNICO
- PRESUPUESTO
- ORDEN_COMPRA
- OTRO

Los documentos pueden tener módulos destino como:

- COMPRAS
- SERVICIOS
- INVENTARIO
- FACTURACION
- CLIENTES

El procesamiento debe contemplar las limitaciones del hosting. No asumir disponibilidad de ejecutables del sistema.

---

## 16. UI/UX

La interfaz debe mantener una experiencia consistente:

- Bootstrap 5.
- Componentes reutilizables.
- Formularios claros.
- Feedback visible después de acciones.
- Confirmaciones para operaciones destructivas o críticas.
- Búsqueda rápida cuando existan catálogos grandes.
- Diseño responsive.
- No recargar páginas innecesariamente cuando Fetch/AJAX mejore la experiencia.

No introducir estilos completamente independientes si existe un sistema visual del proyecto.

---

## 17. Rutas y vistas

Antes de crear una ruta:

- comprobar si ya existe;
- verificar namespace del Controller;
- verificar middleware;
- verificar nombre/ruta real de la vista;
- comprobar parámetros y métodos HTTP.

No mezclar convenciones como `counter::index`, `counter/index` o equivalentes arbitrariamente. Usar la convención que realmente soporte el cargador de vistas del proyecto.

---

## 18. Manejo de errores

El sistema debe distinguir:

- error de validación;
- error de autorización;
- registro inexistente;
- conflicto de negocio;
- error de base de datos;
- error inesperado.

El usuario recibe un mensaje útil y seguro.

Los detalles técnicos deben quedar para logs cuando exista infraestructura de logging.

No utilizar `die()`, `var_dump()` o `print_r()` como solución permanente.

---

## 19. Cambios y regresiones

Antes de modificar código compartido:

1. identificar consumidores;
2. analizar compatibilidad;
3. modificar de forma incremental;
4. revisar rutas relacionadas;
5. revisar SQL relacionado;
6. revisar vistas/JS;
7. verificar casos de error.

Una solución que arregla una pantalla pero rompe otro módulo **no se considera terminada**.

---

## 20. Protocolo de trabajo del agente

Para cada tarea significativa seguir este ciclo:

### A. ANALIZAR

- Entender el pedido.
- Identificar módulos afectados.
- Inspeccionar código y DB.
- Detectar dependencias.

### B. PLANIFICAR

Definir:

```text
Archivos a crear
Archivos a modificar
Tablas/migraciones
Rutas
Dependencias
Riesgos
```

### C. IMPLEMENTAR

- Mantener la arquitectura.
- Reutilizar servicios/componentes.
- Validar entradas.
- Usar transacciones cuando corresponda.
- Mantener multiempresa.

### D. AUDITAR

Revisar:

```text
DB
Repository
Service
Controller
Router
Middleware
View
JS
Seguridad
Multiempresa
Stock
Regresiones
```

### E. ENTREGAR

La entrega debe indicar:

- qué se modificó;
- qué se creó;
- SQL/migraciones necesarias;
- cómo probarlo;
- riesgos o pendientes conocidos.

Cuando se soliciten archivos completos, entregar archivos completos y coherentes, no fragmentos que obliguen a adivinar dónde insertarlos.

---

## 21. Regla contra soluciones improvisadas

No hacer:

- parches duplicando lógica;
- SQL concatenado con variables;
- consultas directamente desde Views;
- acceso directo a DB desde JavaScript;
- modificación de stock sin movimiento cuando exista el sistema de movimientos;
- bypass de permisos;
- asumir columnas inexistentes;
- crear tablas paralelas para entidades que ya existen;
- cambiar arquitectura para solucionar un bug puntual;
- ocultar errores con `@`;
- eliminar validaciones para que una pantalla "funcione".

Si una implementación requiere una decisión arquitectónica, detenerse, explicitarla y elegir la alternativa que mantenga la coherencia del ERP.

---

## 22. Principio final

**El objetivo no es solamente que el código funcione. El objetivo es que CSDI ERP pueda seguir creciendo sin convertirse en un conjunto de parches.**

Cada nueva funcionalidad debe integrarse con lo existente, respetar el modelo multiempresa, conservar trazabilidad y dejar la arquitectura preparada para la siguiente etapa del proyecto.
