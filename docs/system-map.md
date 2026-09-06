# CSDI ERP — Mapa del Sistema

> Baseline arquitectónico para el agente. Este archivo será actualizado después de incorporar el código real al repositorio. Todo elemento marcado como `POR VERIFICAR` debe contrastarse con el código/DB antes de implementarse.

## 1. Propósito

Este documento sirve como índice de navegación para un agente de programación.

El agente debe utilizarlo para ubicar rápidamente:

- módulo afectado;
- responsabilidades;
- entidades;
- dependencias;
- puntos de integración;
- riesgos.

No sustituye la inspección del código real.

---

## 2. Núcleo

| Componente | Responsabilidad | Estado |
|---|---|---|
| Container | Inyección de dependencias | CONOCIDO |
| Router | Enrutamiento HTTP | CONOCIDO |
| BaseController | Funciones comunes de Controllers | CONOCIDO |
| BaseService | Base de servicios | CONOCIDO |
| BaseRepository | Base de persistencia | CONOCIDO |
| PDO/Database | Acceso MySQL | CONOCIDO |
| Middleware Auth | Protección de rutas | CONOCIDO |
| View Loader | Carga de vistas | POR VERIFICAR |

---

## 3. Flujo de una petición

```text
Browser
  ↓
/public/index.php
  ↓
Bootstrap
  ↓
Container
  ↓
Routes
  ↓
Router
  ↓
Middleware
  ↓
Controller
  ↓
Service
  ↓
Repository
  ↓
PDO
  ↓
MySQL
```

Respuesta:

```text
MySQL
 ↓
Repository
 ↓
Service
 ↓
Controller
 ↓
View / JSON / Redirect
 ↓
Browser
```

---

## 4. Auth

Responsabilidades:

- login;
- sesión;
- logout;
- protección de rutas;
- contexto de usuario;
- roles/permisos.

Sesión conocida:

```text
CSDIERP
```

La implementación exacta debe verificarse.

---

## 5. Clientes

Dependencias conceptuales:

```text
Clientes
 ├── Localidades
 ├── Equipos
 ├── Contadores
 ├── Ventas
 ├── Servicios
 ├── Contratos
 └── Facturación
```

---

## 6. Equipos

Dependencias:

```text
Modelos de equipos
       ↓
Equipos
       ↓
Cliente
       ├── Registros
       ├── Servicios
       └── Swap
```

Swap es transversal.

---

## 7. Contadores

Componentes conceptuales:

```text
Counter Controller
       ↓
Counter Service
       ↓
Counter Repository
       ↓
registros
```

Dependencias:

- ClienteService;
- EquipoService;
- LocalidadService;
- SwapService;
- ContadoresAbonosService;
- PdfService;
- ExcelService.

La lista anterior proviene de la arquitectura conocida y debe contrastarse con el código real.

---

## 8. Counter — operaciones conocidas

```text
listarRegistros()
getIndexData()
getCreateData()
obtenerRegistro()
obtenerFicha360()
historialEquipo()
clientesPorLocalidad()
equiposPorCliente()
obtenerEquipo()
obtenerUltimoContador()
obtenerRegistroPorPeriodo()
obtenerRegistroAnterior()
prepararNuevaLectura()
validarLectura()
```

Estos nombres son referencias de trabajo, no una autorización para asumir que las firmas actuales son idénticas.

---

## 9. Swap

```text
SwapService
 ↓
BEGIN
 ↓
Equipo saliente
 ↓
Equipo entrante
 ↓
Lecturas / continuidad
 ↓
historial_cambios
 ↓
COMMIT
```

Debe permanecer transversal.

---

## 10. Inventario

```text
Insumos / Productos
      ↓
Stock
      ↓
Stock Movimientos
      ↑
      │
Compras ───── Ventas
```

Entidades conocidas:

- `insumos`;
- `stock_insumos`;
- `stock_movimientos`.

---

## 11. Compras

```text
Proveedor
 ↓
Compra
 ↓
Compra Items
 ↓
Recepción
 ↓
Stock Movement
```

Entidades conocidas:

- `proveedores`;
- `compras`;
- `compras_items`.

---

## 12. Proveedores

```text
Proveedor
 ├── Producto A
 │    ├── código externo
 │    ├── precio
 │    └── vigencia
 ├── Producto B
 └── Producto C
```

La arquitectura debe soportar múltiples proveedores por producto.

---

## 13. Ventas

```text
Cliente
 ↓
Venta
 ↓
Venta Detalle
 ↓
Confirmación
 ↓
Stock Movement
 ↓
Facturación
```

Entidades conocidas:

- `ventas`;
- `ventas_detalle`.

---

## 14. Facturación

```text
Origen
 ↓
Facturacion Origenes
 ↓
Control
 ↓
Factura
 ↓
Items
```

Entidades conocidas:

- `facturacion_control`;
- `facturacion_origenes`;
- `facturas`;
- `facturacion_items`.

---

## 15. DocumentosIA

```text
Upload
 ↓
Documento
 ↓
Clasificación
 ↓
Extracción
 ↓
Revisión
 ↓
Módulo destino
```

Destinos conceptuales:

- Compras;
- Servicios;
- Inventario;
- Facturación;
- Clientes;
- Informes TNG.

---

## 16. TNG

```text
Contratos TNG
      ↓
Visitas
      ↓
Informes
      ↓
Tarifas / reglas
      ↓
Facturación
```

No conectar este circuito con el cálculo de copias de Contadores salvo que una regla explícita lo requiera.

---

## 17. Reorder Engine

```text
Stock actual
     ↓
Punto de reposición
     ↓
Compras pendientes
     ↓
Necesidad
     ↓
Proveedor / precio
     ↓
PUNTO
```

---

## 18. PUNTO

```text
                   PUNTO
                     │
        ┌────────────┼────────────┐
        │            │            │
     Catálogo     Campañas     Proveedores
        │            │            │
        └────────────┼────────────┘
                     │
              Demanda agregada
                     │
              Compra conjunta
                     │
               Pedidos individuales
                     │
                 Recepción
                     │
                  Stock
```

PUNTO debe reutilizar productos, empresas, proveedores y stock existentes cuando corresponda.

---

## 19. Servicios

Circuito conceptual:

```text
Cliente
 ↓
Equipo
 ↓
Servicio técnico
 ↓
Visita / trabajo
 ↓
Repuestos
 ↓
Facturación
```

El modelo exacto debe verificarse.

---

## 20. Taller

```text
Ingreso
 ↓
Diagnóstico
 ↓
Presupuesto
 ↓
Reparación
 ↓
Repuestos
 ↓
Control
 ↓
Entrega
```

---

## 21. Portal Cliente

Debe consumir APIs/servicios autorizados y respetar:

- cliente;
- empresa;
- permisos;
- documentos propios;
- equipos propios.

No consultar directamente tablas sin pasar por las reglas del dominio.

---

## 22. App móvil técnicos

```text
Android
 ↓
API
 ↓
Auth
 ↓
Services
 ↓
DB
```

Funciones previstas:

- órdenes;
- visitas;
- lecturas;
- QR;
- fotos;
- sincronización.

---

## 23. Dependencias transversales

### Auth

Afecta todos los módulos protegidos.

### Multiempresa

Afecta todas las entidades empresariales.

### Stock

Afecta Compras, Ventas, PUNTO, Taller y ajustes.

### Clientes

Afecta Equipos, Contadores, Ventas, Servicios y Facturación.

### Equipos

Afecta Contadores, Servicios, Taller y Swap.

### DocumentosIA

Puede alimentar Compras, Ventas, Inventario, Facturación y otros módulos.

---

## 24. Puntos de alto riesgo

El agente debe revisar cuidadosamente:

1. cambios al Core;
2. Router;
3. Container;
4. autenticación;
5. permisos;
6. consultas multiempresa;
7. stock;
8. facturación;
9. Swap;
10. migraciones;
11. contratos internos de Services/Repositories.

---

## 25. Mapa después de incorporar el código

Cuando el código real sea subido al repositorio, actualizar este documento con:

```text
Archivo exacto
Clase
Namespace
Métodos públicos
Ruta
Middleware
Tabla
FK
Dependencias
```

También generar un inventario de:

- Controllers;
- Services;
- Repositories;
- Views;
- Routes;
- tablas;
- migraciones;
- assets;
- endpoints AJAX/API.

---

## 26. Regla de mantenimiento

Cada módulo nuevo debe agregarse a este mapa.

Cada cambio arquitectónico relevante debe actualizarlo.

El mapa debe reflejar el código real, no una aspiración que el código todavía no implementa.
