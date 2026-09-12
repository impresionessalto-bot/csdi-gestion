<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

use RuntimeException;

final class TemplateEngine
{
    /**
     * Renderiza una plantilla utilizando un array asociativo de variables de forma segura (Nivel Enterprise).
     */
    public function render(string $templateFile, array $data = []): string
    {
        $normalizedPath = str_replace(['\\', '//'], ['/', '/'], $templateFile);

        if (!is_file($normalizedPath)) {
            throw new RuntimeException("El archivo de plantilla no existe o la ruta es inválida: " . $normalizedPath);
        }

        // Extraemos las variables de forma controlada para que estén disponibles en el scope del include
        extract($data, EXTR_SKIP);

        // Capturamos la salida del buffer de forma segura
        ob_start();
        
        try {
            include $normalizedPath;
            $output = ob_get_clean();
        } catch (\Throwable $e) {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            throw new RuntimeException("Error crítico al renderizar la plantilla Enterprise [{$normalizedPath}]: " . $e->getMessage(), 0, $e);
        }

        return $output !== false ? $output : '';
    }
}