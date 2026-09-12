<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

final class AiBuilderGenerator extends AbstractGenerator
{
    public function name(): string
    {
        return 'AiBuilderGenerator';
    }

    public function generate(array $definition): void
    {
        $moduleName = $this->required($definition, 'module');
        $moduleLower = strtolower($moduleName);

        // Estructura base para el componente de IA dentro del módulo generado
        $content = "<?php\n\n" .
            "declare(strict_types=1);\n\n" .
            "namespace App\\Modules\\{$moduleName}\\Services;\n\n" .
            "final class {$moduleName}AiService\n" .
            "{\n" .
            "    public function processPrompt(string \$prompt): array\n" .
            "    {\n" .
            "        // Lógica de integración con IA para el módulo {$moduleName}\n" .
            "        return [\n" .
            "            'success' => true,\n" .
            "            'module' => '{$moduleName}',\n" .
            "            'result' => 'Procesado mediante AI Builder'\n" .
            "        ];\n" .
            "    }\n" .
            "}\n";

        $destination = $this->file($definition, 'Services/' . $moduleName . 'AiService.php');

        $this->write(
            $destination,
            $content,
            $this->config()['overwrite']
        );
    }
}