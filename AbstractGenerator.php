<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

use RuntimeException;

abstract class AbstractGenerator
{
    protected TemplateResolver $resolver;
    protected TemplateEngine $engine;

    public function __construct(?TemplateResolver $resolver = null, ?TemplateEngine $engine = null)
    {
        $this->resolver = $resolver ?? new TemplateResolver($this->templatePath());
        $this->engine = $engine ?? new TemplateEngine();
    }

    public function templatePath(): string
    {
        $path = dirname(__DIR__) . '/Templates/default';
        $real = realpath($path);

        if ($real === false || !is_dir($real)) {
            throw new RuntimeException('No se encontró el directorio de templates de Creator: ' . $path);
        }

        return $this->normalizePath($real);
    }

    public function resolveTemplate(string $templateName): string
    {
        $templateName = trim($templateName);
        if ($templateName === '') {
            throw new RuntimeException('El nombre de la plantilla no puede estar vacío.');
        }

        if ($this->isAbsolutePath($templateName) && is_file($templateName)) {
            return $this->normalizePath($templateName);
        }

        return $this->resolver->resolve($templateName);
    }

    protected function render(string $templateName, array $data = []): string
    {
        return $this->engine->render($this->resolveTemplate($templateName), $data);
    }

    protected function required(array $definition, string $key): mixed
    {
        if (!array_key_exists($key, $definition)) {
            throw new RuntimeException("Falta el parámetro requerido en la definición del módulo: '{$key}'");
        }

        $value = $definition[$key];
        if ($value === null || (is_string($value) && trim($value) === '')) {
            throw new RuntimeException("El parámetro requerido '{$key}' no puede estar vacío.");
        }

        return $value;
    }

    protected function optional(array $definition, string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $definition) ? $definition[$key] : $default;
    }

    protected function namespace(array $definition): string
    {
        $module = $this->required($definition, 'module');
        return 'App\\Modules\\' . $this->className((string) $module);
    }

    protected function modulePath(array $definition): string
    {
        $module = $this->validateModuleName((string) $this->required($definition, 'module'));
        return $this->appModulesPath() . '/' . $module;
    }

    protected function file(array $definition, string $relativePath): string
    {
        $moduleRoot = $this->modulePath($definition);
        $relativePath = str_replace(['{module}', '{Module}'], basename($moduleRoot), trim($relativePath));

        if ($relativePath === '' || str_contains($relativePath, "\0")) {
            throw new RuntimeException('Ruta de archivo inválida.');
        }

        $relativePath = str_replace('\\', '/', $relativePath);
        if ($relativePath[0] === '/' || preg_match('#(^|/)\.\.?(/|$)#', $relativePath)) {
            throw new RuntimeException('Ruta de destino no permitida: ' . $relativePath);
        }

        $destination = $this->normalizePath($moduleRoot . '/' . $relativePath);
        $prefix = rtrim($this->normalizePath($moduleRoot), '/') . '/';

        if (!str_starts_with($destination, $prefix)) {
            throw new RuntimeException('La ruta de destino intenta salir del módulo.');
        }

        return $destination;
    }

    protected function write(string $destination, string $content, bool $overwrite = true): void
    {
        $destination = $this->normalizePath($destination);
        $this->createDirectory(dirname($destination));

        if (is_file($destination) && !$overwrite) {
            return;
        }

        $bytes = file_put_contents($destination, $content, LOCK_EX);
        if ($bytes === false) {
            throw new RuntimeException('No se pudo escribir el archivo: ' . $destination);
        }

        if (!is_file($destination) || !is_readable($destination)) {
            throw new RuntimeException('El archivo fue escrito pero no quedó disponible: ' . $destination);
        }
    }

    protected function createDirectory(string $directory): void
    {
        if (is_dir($directory)) {
            return;
        }

        if (!mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException('No se pudo crear el directorio: ' . $directory);
        }
    }

    protected function copyFile(string $source, string $destination, bool $overwrite = true): void
    {
        if (!is_file($source)) {
            throw new RuntimeException('Archivo origen inexistente: ' . $source);
        }

        if (is_file($destination) && !$overwrite) {
            return;
        }

        $this->createDirectory(dirname($destination));
        if (!copy($source, $destination)) {
            throw new RuntimeException('No se pudo copiar el archivo a: ' . $destination);
        }
    }

    protected function config(): array
    {
        return [
            'overwrite' => true,
        ];
    }

    protected function sanitizeIdentifier(string $value, string $label = 'identificador'): string
    {
        $value = trim($value);
        if ($value === '' || !preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $value)) {
            throw new RuntimeException("{$label} inválido: {$value}");
        }
        return $value;
    }

    protected function validateModuleName(string $module): string
    {
        return $this->sanitizeIdentifier($module, 'Nombre de módulo');
    }

    protected function className(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            throw new RuntimeException('El nombre de clase no puede estar vacío.');
        }

        $value = preg_replace('/[^A-Za-z0-9_]+/', ' ', $value) ?? $value;
        $parts = preg_split('/\s+/', trim($value)) ?: [];
        $result = '';
        foreach ($parts as $part) {
            $result .= ucfirst($part);
        }

        if ($result === '' || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $result)) {
            throw new RuntimeException('Nombre de clase inválido.');
        }

        return $result;
    }

    protected function appModulesPath(): string
    {
        $base = defined('BASE_PATH') ? (string) BASE_PATH : dirname(__DIR__, 3);
        $base = $this->normalizePath($base);

        if (basename($base) === 'App') {
            return $base . '/Modules';
        }

        return $base . '/App/Modules';
    }

    protected function normalizePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $prefix = '';

        if (preg_match('#^[A-Za-z]:/#', $path)) {
            $prefix = substr($path, 0, 3);
            $path = substr($path, 3);
        } elseif (str_starts_with($path, '/')) {
            $prefix = '/';
            $path = ltrim($path, '/');
        }

        $parts = [];
        foreach (explode('/', $path) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }
            if ($part === '..') {
                if ($parts !== []) {
                    array_pop($parts);
                }
                continue;
            }
            $parts[] = $part;
        }

        return $prefix . implode('/', $parts);
    }

    private function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, '/') || (bool) preg_match('/^[A-Za-z]:[\\\\\/]/', $path);
    }

    abstract public function name(): string;
    abstract public function generate(array $definition): void;
}
