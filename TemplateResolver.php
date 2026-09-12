<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

use RuntimeException;

final class TemplateResolver
{
    private string $templatePath;

    public function __construct(string $path)
    {
        $real = realpath($path);
        if ($real === false || !is_dir($real)) {
            throw new RuntimeException('Directorio de templates inexistente: ' . $path);
        }
        $this->templatePath = rtrim(str_replace('\\', '/', $real), '/');
    }

    public function getPath(): string
    {
        return $this->templatePath;
    }

    public function resolve(string $templateName): string
    {
        $templateName = trim(str_replace('\\', '/', $templateName));
        if ($templateName === '' || str_contains($templateName, "\0")) {
            throw new RuntimeException('Nombre de plantilla inválido.');
        }

        if (str_contains($templateName, '/') && preg_match('#(^|/)\.\.?(/|$)#', $templateName)) {
            throw new RuntimeException('Ruta de plantilla no permitida: ' . $templateName);
        }

        $baseName = preg_replace('/\.(tpl\.php|tpl)$/', '', basename($templateName));
        $relativeDir = dirname($templateName);
        $relativeDir = $relativeDir === '.' ? '' : trim($relativeDir, '/');

        $directories = [
            $this->templatePath,
            dirname($this->templatePath) . '/api',
        ];

        if ($relativeDir !== '') {
            $directories[] = $this->templatePath . '/' . $relativeDir;
        }

        foreach (array_unique($directories) as $directory) {
            foreach ([$baseName . '.tpl.php', $baseName . '.tpl'] as $extension) {
                $file = $directory . '/' . $extension;
                if (is_file($file)) {
                    return str_replace('\\', '/', $file);
                }
            }
        }

        throw new RuntimeException("No se encuentra la plantilla '{$templateName}'.");
    }
}
