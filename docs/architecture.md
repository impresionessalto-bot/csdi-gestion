# CSDI ERP — Arquitectura Técnica

> Documento vivo. Describe la arquitectura objetivo y las convenciones que deben respetarse. Cuando el código real del ERP sea incorporado al repositorio, este documento debe contrastarse con la implementación y actualizarse para reflejar la realidad.

## 1. Objetivo

CSDI ERP es una aplicación empresarial modular, multiempresa y extensible, desarrollada en PHP 8.3+, MySQL/MariaDB, PDO, MVC propio, Bootstrap 5 y JavaScript/Fetch.

La arquitectura debe permitir crecer desde la operación de CSDI hacia otros comercios y empresas sin convertir el sistema en un conjunto de módulos aislados.

Principios:

1. separación de responsabilidades;
2. reutilización antes que duplicación;
3. seguridad en servidor;
4. trazabilidad de operaciones;
5. aislamiento multiempresa;
6. compatibilidad con hosting compartido;
7. evolución incremental;
8. mínima dependencia de servicios externos no garantizados.

---

## 2. Capas

```text
HTTP
 ↓
public/index.php / bootstrap
 ↓
Router
 ↓
Middleware / autenticación / autorización
 ↓
Controller
 ↓
Service
 ↓
Repository
 ↓
PDO
 ↓
MySQL/MariaDB
```

Presentación:

```text
Controller
 ↓
View
 ↓
Bootstrap 5 + componentes
 ↓
JavaScript / Fetch / AJAX
```

Integraciones externas deben estar detrás de Services/Adapters y nunca dispersas por Controllers o Views.

---

## 3. Core

El núcleo previsto/ya utilizado incluye conceptos como:

- `App/Core/Container`
- `App/Core/Router`
- `BaseController`
- `BaseRepository`
- `BaseService`
- middleware de autenticación/autorización
- carga de vistas
- conexión PDO

El agente debe inspeccionar los nombres y firmas reales antes de crear nuevas abstracciones.

### Container

Debe centralizar la construcción/inyección de dependencias cuando corresponda.

No crear instancias manualmente de servicios que el Container ya pueda resolver.

### Router

Debe ser la única fuente de verdad para las rutas HTTP. Antes de añadir una ruta, buscar rutas existentes equivalentes.

### Controller

Responsabilidades:

- recibir request;
- validar formato básico;
- verificar autenticación mediante middleware/Core;
- delegar reglas de negocio;
- seleccionar vista/respuesta;
- devolver respuestas HTTP apropiadas.

No debe contener SQL ni reglas de negocio complejas.

### Service

Responsabilidades:

- reglas de negocio;
- validaciones de dominio;
- coordinación de varios repositories/services;
- transacciones cuando corresponda;
- orquestación de procesos.

### Repository

Responsabilidades:

- SQL;
- lectura/escritura de persistencia;
- mapeo de resultados;
- consultas preparadas.

No debe decidir reglas de negocio.

---

## 4. Módulos

La estructura conceptual es:

```text
App/
├── Core/
├── Components/
└── Modules/
    ├── Auth/
    ├── Clientes/
    ├── Equipos/
    ├── Counter/
    ├── Inventario/
    ├── Compras/
    ├── Ventas/
    ├── Facturacion/
    ├── Proveedores/
    ├── DocumentosIA/
    ├── Servicios/
    ├── Taller/
    ├── Logistics/
    ├── Punto/
    └── ...
```

Los nombres anteriores representan el mapa conceptual y deben contrastarse con el repositorio real.

Cada módulo debería tender a una estructura similar:

```text
Module/
├── Controllers/
├── Services/
├── Repositories/
├── Views/
└── Assets/          # solo si el proyecto realmente usa assets por módulo
```

No todos los módulos necesitan exactamente las mismas carpetas. No crear directorios vacíos solo para cumplir una plantilla.

---

## 5. Components

Los componentes transversales deben reutilizarse.

Ejemplos previstos:

```text
Components/
├── Alerts/
├── Badge/
├── Buttons/
├── Cards/
├── Charts/
├── Dropdown/
├── Filters/
├── Forms/
├── Modals/
├── Pagination/
├── Table/
├── Tabs/
└── ...
```

DocumentosIA tiene además un concepto de Dropzone reutilizable para documentos. Si el componente existe en código, debe utilizarse desde Compras, Ventas, informes TNG y otros módulos en lugar de crear nuevos uploaders.

---

## 6. Frontend

Bootstrap 5 es la base visual.

Reglas:

- responsive;
- accesible en lo razonable;
- feedback de éxito/error;
- confirmación de acciones críticas;
- formularios con validación cliente y servidor;
- Fetch/AJAX para operaciones que mejoren la UX;
- nunca confiar en validaciones JavaScript como mecanismo de seguridad.

Los endpoints AJAX deben pasar por la misma autenticación, autorización, CSRF y validación que una petición normal.

---

## 7. Respuestas HTTP

El agente debe mantener la convención existente del proyecto para:

- redirects;
- HTML;
- JSON;
- errores de validación;
- respuestas AJAX.

No introducir formatos incompatibles sin necesidad.

Cuando se creen endpoints JSON, definir respuestas consistentes, por ejemplo:

```json
{
  "ok": true,
  "data": {},
  "message": ""
}
```

Para errores:

```json
{
  "ok": false,
  "message": "No fue posible completar la operación.",
  "errors": {}
}
```

Adaptar siempre al contrato real del proyecto si ya existe uno.

---

## 8. Autenticación y autorización

La autenticación debe estar centralizada.

El sistema usa conceptualmente una sesión propia, actualmente conocida como `CSDIERP`.

No duplicar sistemas de sesión por módulo.

Autorización:

```text
Usuario
 ↓
Empresa/contexto
 ↓
Rol
 ↓
Permiso
 ↓
Acción
```

El permiso debe comprobarse en servidor.

El rol `vendedor` debe poder configurarse por empresa y no debe implementarse como una condición global rígida.

---

## 9. Contexto de empresa

Toda operación debe conocer el contexto de empresa cuando corresponda.

El agente debe evitar:

```php
SELECT * FROM clientes WHERE id = ?
```

si el modelo exige aislamiento por empresa y existe una columna/contexto empresarial.

Preferir el patrón real del proyecto, por ejemplo:

```text
empresa_id + registro_id
```

La columna exacta debe verificarse en el esquema real.

---

## 10. Transacciones

Usar transacciones para operaciones que modifican varias entidades y deben ser atómicas.

Ejemplos:

- recepción de compra + stock;
- confirmación de venta + stock;
- Swap de equipos + historial + lecturas;
- creación de documentos relacionados;
- cierre de operaciones con múltiples escrituras.

Patrón conceptual:

```text
BEGIN
  validar
  modificar A
  modificar B
  modificar C
COMMIT
```

Ante cualquier excepción:

```text
ROLLBACK
```

No hacer commit parcial de una operación que conceptualmente debe ser atómica.

---

## 11. Integraciones

Toda API externa debe estar encapsulada.

Ejemplos:

- facturación/API Dux;
- proveedores externos;
- servicios de IA;
- WhatsApp;
- servicios de logística.

Nunca distribuir llamadas HTTP externas directamente entre múltiples Controllers.

Definir timeout, manejo de errores y comportamiento ante indisponibilidad cuando sea posible.

---

## 12. Hosting

La aplicación debe ser compatible con hosting compartido.

No asumir:

- Docker;
- workers persistentes;
- Redis;
- RabbitMQ;
- acceso root;
- comandos shell;
- ejecutables externos;
- procesos en segundo plano permanentes.

`exec()`, `shell_exec()` y `escapeshellarg()` pueden estar deshabilitados.

Cualquier nueva dependencia de infraestructura debe justificarse.

---

## 13. DocumentosIA

El procesamiento documental debe ser desacoplado del módulo destino.

```text
Documento
 ↓
Almacenamiento
 ↓
Clasificación
 ↓
Extracción disponible
 ↓
Revisión
 ↓
Módulo destino
```

No asumir OCR local. Si el hosting no dispone de Tesseract, utilizar mecanismos PHP/librerías disponibles o servicios externos autorizados.

---

## 14. Logging y auditoría

Las operaciones críticas deben poder rastrearse.

Cuando exista infraestructura de auditoría, conservar:

- usuario;
- empresa;
- fecha/hora;
- entidad;
- acción;
- valores relevantes;
- documento origen.

Nunca registrar contraseñas, tokens, secretos o datos sensibles innecesarios.

---

## 15. Compatibilidad hacia atrás

Al modificar una clase compartida:

1. buscar todos sus consumidores;
2. revisar firmas;
3. revisar rutas;
4. revisar inyección de dependencias;
5. revisar vistas;
6. revisar JS;
7. verificar casos existentes.

No cambiar una API interna pública sin analizar el impacto.

---

## 16. Definición de terminado

Una tarea se considera terminada solamente cuando:

- código implementado;
- arquitectura respetada;
- seguridad revisada;
- multiempresa revisada;
- SQL revisado;
- rutas revisadas;
- vistas revisadas;
- JS revisado;
- transacciones revisadas;
- regresiones razonablemente verificadas;
- migraciones documentadas;
- pendientes explícitos.
