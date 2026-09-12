# CSDI ERP Studio / Creator

Módulo de generación de módulos del ERP.

## Generadores incluidos

- ModuleGenerator
- ControllerGenerator
- ServiceGenerator
- RepositoryGenerator
- RouteGenerator
- ViewGenerator
- CrudBuilder
- BuilderGenerator
- DashboardGenerator
- WidgetGenerator
- SqlGenerator
- MigrationGenerator
- SchemaGenerator
- ApiGenerator
- AiBuilderGenerator
- AssetGenerator
- PermissionGenerator
- MenuGenerator

## Seguridad

`AbstractGenerator` valida nombres de módulos e identificadores, normaliza rutas y evita que una ruta de destino salga del directorio del módulo.

La firma pública de `generate(array $definition): void` se mantiene para no romper los generadores existentes.
