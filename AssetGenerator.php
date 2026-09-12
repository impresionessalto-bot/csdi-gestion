<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

final class AssetGenerator extends AbstractGenerator
{
    public function name(): string
    {
        return 'AssetGenerator';
    }

    public function generate(array $definition): void
    {
        $moduleName = $this->required($definition, 'module');
        $moduleLower = strtolower($moduleName);

        // Definir rutas para los assets específicos del módulo (JS y CSS)
        $assets = [
            'Assets/js/' . $moduleLower . '.js' => "document.addEventListener('DOMContentLoaded', () => {\n    console.log('Módulo " . $moduleName . " cargado correctamente.');\n});\n",
            'Assets/css/' . $moduleLower . '.css' => "/* Estilos personalizados para el módulo " . $moduleName . " */\n"
        ];

        foreach ($assets as $path => $defaultContent) {
            $destination = $this->file($definition, $path);

            $this->write(
                $destination,
                $defaultContent,
                $this->config()['overwrite']
            );
        }
    }
}