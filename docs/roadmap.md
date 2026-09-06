# CSDI ERP — Roadmap Maestro

> Hoja de ruta funcional y técnica. No representa que todas las etapas estén implementadas. El agente debe distinguir siempre entre EXISTENTE, EN DESARROLLO, PLANIFICADO y PROPUESTO.

## 1. Visión

Construir un ERP multiempresa capaz de administrar la operación completa de CSDI y posteriormente otros comercios/empresas, manteniendo una arquitectura modular.

Objetivos:

- centralizar datos;
- reducir carga administrativa;
- automatizar procesos repetitivos;
- controlar stock;
- comparar proveedores;
- mejorar compras;
- integrar ventas/facturación;
- gestionar equipos y contadores;
- incorporar IA documental;
- crear PUNTO como red de compras;
- habilitar portal/app e integraciones.

---

## 2. Estados del roadmap

Usar estas etiquetas:

```text
EXISTENTE       → existe código funcional
EN_DESARROLLO   → código parcial o en refactor
PLANIFICADO     → aprobado como próxima etapa
PROPUESTO       → idea pendiente de decisión
BLOQUEADO       → depende de infraestructura/dato/decisión
```

Nunca presentar una idea como funcionalidad existente.

---

## 3. Fase 0 — Fundaciones

Estado conceptual: **EN DESARROLLO / CONSOLIDACIÓN**.

Incluye:

- PHP 8.3;
- Composer/autoload;
- Core;
- Container;
- Router;
- BaseController;
- BaseService;
- BaseRepository;
- PDO;
- autenticación;
- sesión `CSDIERP`;
- middleware;
- sistema de vistas;
- componentes Bootstrap;
- estructura de módulos.

Objetivo: estabilizar el núcleo antes de expandir funcionalidades.

---

## 4. Fase 1 — Identidad y multiempresa

Estado: **PLANIFICADO / CONSOLIDACIÓN**.

Objetivos:

- empresas;
- usuarios;
- pertenencia/contexto empresarial;
- roles por empresa;
- permisos;
- auditoría;
- aislamiento de datos.

Regla crítica:

> `vendedor` debe ser configurable por empresa y no un rol global rígido.

---

## 5. Fase 2 — Clientes

Estado: **EN DESARROLLO / EXISTENTE PARCIAL**.

Objetivos:

- CRUD clientes;
- localidades;
- contactos;
- datos fiscales;
- relación con equipos;
- relación con ventas;
- relación con servicios;
- relación con facturación.

---

## 6. Fase 3 — Equipos

Estado: **EN DESARROLLO / EXISTENTE PARCIAL**.

Objetivos:

- modelos;
- equipos físicos;
- número de serie;
- código interno;
- propiedad;
- estado;
- cliente;
- historial;
- Swap transversal.

No mezclar equipos TNG con el circuito de Contadores.

---

## 7. Fase 4 — Contadores

Estado: **EN DESARROLLO / EXISTENTE PARCIAL**.

Objetivos:

- carga rápida;
- selección cliente/equipo;
- lecturas Mono;
- lecturas Color;
- validación de período;
- consumo;
- historial;
- ficha 360;
- recordatorios;
- exportación PDF/Excel;
- integración con SwapService;
- preparación para facturación.

Regla:

```text
Mono  → contador principal
Color → contador mono + color
```

---

## 8. Fase 5 — Inventario

Estado: **EN DESARROLLO / PLANIFICADO**.

Objetivos:

- catálogo de insumos/productos;
- categorías;
- unidades de medida;
- depósitos;
- stock;
- movimientos;
- transferencias;
- ajustes;
- mínimos/máximos;
- punto de reposición;
- historial.

Regla:

```text
Compra/Recepción → stock +
Venta/Salida      → stock -
```

---

## 9. Fase 6 — Proveedores y listas

Estado: **PLANIFICADO / EN DISEÑO**.

Objetivos:

- proveedores;
- múltiples proveedores por producto;
- códigos externos;
- listas de precios;
- vigencias;
- unidades de compra;
- mínimos;
- múltiplos;
- descuentos;
- comparación de precios.

Visión:

```text
Producto
 ├── Proveedor A
 ├── Proveedor B
 └── Proveedor C
```

Cuando el usuario cargue un pedido, el ERP podrá comparar alternativas.

---

## 10. Fase 7 — Compras

Estado: **EN DESARROLLO / PLANIFICADO**.

Objetivos:

- solicitud;
- orden de compra;
- proveedor;
- items;
- precios;
- impuestos;
- recepción;
- recepción parcial;
- diferencias;
- stock;
- documentos.

---

## 11. Fase 8 — Reorder Engine

Estado: **PLANIFICADO**.

Objetivo: transformar stock en recomendaciones inteligentes.

Primera versión:

```text
Necesidad = Stock objetivo - Stock actual - compras pendientes
```

Evolución:

- consumo histórico;
- lead time;
- estacionalidad;
- ventas;
- múltiplos;
- proveedor preferido;
- costo;
- oportunidad PUNTO.

---

## 12. Fase 9 — Ventas

Estado: **EN DESARROLLO / EXISTENTE PARCIAL**.

Objetivos:

- clientes;
- búsqueda de productos;
- selección de lista;
- edición de precios;
- cantidades;
- impuestos por línea;
- descuentos;
- stock;
- confirmación;
- comprobantes;
- facturación.

UX objetivo:

```text
Buscar artículo
 ↓
Seleccionar
 ↓
Cambiar lista
 ↓
Precio
 ↓
Cantidad
 ↓
Impuestos
 ↓
Confirmar
```

---

## 13. Fase 10 — Facturación

Estado: **EN DESARROLLO / EXISTENTE PARCIAL**.

Objetivos:

- control de facturación;
- orígenes;
- items;
- facturas;
- integración con ventas;
- servicios;
- contadores;
- contratos;
- Dux/API cuando corresponda.

---

## 14. Fase 11 — DocumentosIA

Estado: **EN DESARROLLO**.

Objetivos:

- carga drag & drop;
- clasificación;
- extracción disponible;
- revisión;
- integración con Compras;
- Ventas;
- Inventario;
- Facturación;
- Clientes;
- Informes TNG.

Restricción:

> No asumir Tesseract/OCR del sistema en Ferozo.

---

## 15. Fase 12 — TNG

Estado: **EN DESARROLLO / EXISTENTE PARCIAL**.

Objetivos:

- contratos TNG;
- visitas;
- informes;
- tarifas;
- reglas de cobro;
- facturación TNG.

Regla central:

> TNG cobra por visita, no por copias.

No mezclar esta lógica con Contadores CSDI.

---

## 16. Fase 13 — PUNTO Red de Compras

Estado: **PROPUESTO / DISEÑO**.

Marca:

> PUNTO — Red de Compras
> Compramos juntos. Conseguimos más.

Concepto:

> PUNTO conecta comercios independientes para que puedan comprar como grandes.

Flujo:

```text
Reorder Engine
      ↓
Comprar en PUNTO
      ↓
Demanda de múltiples comercios
      ↓
Compra conjunta
      ↓
Negociación con fabricante/importador
      ↓
Precio por volumen
      ↓
Pedidos individuales
      ↓
Distribución
      ↓
Recepción
      ↓
Stock
```

---

## 17. PUNTO — funcionalidades previstas

### Catálogo

- productos;
- equivalencias;
- proveedor/fabricante;
- precios;
- escalas.

### Compra conjunta

- campaña;
- fecha de cierre;
- meta de volumen;
- participantes;
- cantidades;
- precio negociado.

### Pedido individual

Cada empresa conserva su pedido y trazabilidad.

### Distribución

- preparación;
- despacho;
- QR;
- seguimiento.

### Recepción

- confirmar;
- diferencias;
- incidencias;
- stock automático.

---

## 18. Fase 14 — Portal Cliente

Estado: **PLANIFICADO**.

Objetivos:

- consulta de equipos;
- contadores;
- servicios;
- documentos;
- pedidos;
- facturas;
- solicitudes;
- comunicación.

Debe respetar aislamiento estricto por cliente/empresa.

---

## 19. Fase 15 — App móvil técnicos

Estado: **PLANIFICADO**.

Tecnología prevista:

- Android Studio;
- Retrofit;
- QR;
- fotos;
- API autenticada.

Funciones:

- órdenes de trabajo;
- visitas;
- lecturas;
- equipos;
- fotos;
- firma/confirmación cuando corresponda;
- sincronización.

---

## 20. Fase 16 — Taller

Estado: **PLANIFICADO**.

Objetivos:

- ingreso de equipos;
- diagnóstico;
- reparación;
- repuestos;
- técnicos;
- estados;
- presupuesto;
- entrega.

---

## 21. Fase 17 — Logística

Estado: **PLANIFICADO**.

Objetivos:

- entregas;
- retiros;
- rutas;
- estados;
- evidencia;
- QR;
- integración PUNTO.

---

## 22. Fase 18 — Marketing / CRM

Estado: **PROPUESTO**.

Posibles capacidades:

- campañas;
- segmentación;
- seguimiento comercial;
- WhatsApp;
- recordatorios;
- oportunidades.

---

## 23. Fase 19 — POS

Estado: **PROPUESTO**.

Objetivo: extender Ventas hacia operación de mostrador.

Debe reutilizar:

- clientes;
- productos;
- listas;
- impuestos;
- stock;
- caja;
- facturación.

No crear un catálogo paralelo.

---

## 24. IA transversal

Estado: **PLANIFICADO**.

La IA debe asistir, no reemplazar controles críticos.

Usos:

- documentos;
- clasificación;
- sugerencias de compra;
- comparación de proveedores;
- detección de anomalías;
- asistencia al usuario;
- análisis comercial.

Las operaciones sensibles requieren validación determinística del sistema.

---

## 25. Orden de prioridad

Priorizar:

```text
1. Integridad del Core
2. Multiempresa
3. Seguridad
4. Clientes / Equipos
5. Contadores
6. Inventario
7. Proveedores / Listas
8. Compras
9. Ventas
10. Facturación
11. DocumentosIA
12. Reorder Engine
13. PUNTO
14. Portal / App / Logística
15. IA avanzada
```

El orden puede cambiar por necesidades comerciales, pero una funcionalidad nueva no debe degradar las capas anteriores.

---

## 26. Deuda técnica

El agente debe mantener una lista de deuda técnica cuando encuentre:

- duplicación;
- SQL inseguro;
- rutas inconsistentes;
- nombres incompatibles;
- vistas mal ubicadas;
- servicios demasiado grandes;
- dependencia circular;
- falta de índices;
- falta de autorización;
- falta de pruebas.

No ocultar deuda técnica bajo una implementación rápida.

---

## 27. Criterio de avance

Una fase no debe considerarse terminada porque la pantalla funciona.

Debe cumplir:

```text
Código
+ DB
+ Seguridad
+ Multiempresa
+ UX
+ Integración
+ Trazabilidad
+ Manejo de errores
+ Compatibilidad
```

---

## 28. Visión final

El ERP debe evolucionar hacia:

```text
                    CSDI ERP
                        │
        ┌───────────────┼────────────────┐
        │               │                │
     OPERACIÓN       COMERCIO         INTELIGENCIA
        │               │                │
 Clientes          Ventas           DocumentosIA
 Equipos           Compras          Reorder Engine
 Contadores        Inventario       IA
 Servicios         Facturación      Analítica
        │               │                │
        └───────────────┼────────────────┘
                        │
                 PUNTO RED DE COMPRAS
                        │
              comercios independientes
                        │
                 demanda agregada
                        │
                fabricantes/importadores
```

Principio final:

> CSDI ERP debe convertirse en una plataforma empresarial, no en una colección de CRUDs independientes.
