<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

final class DashboardGenerator extends AbstractGenerator
{
    public function name(): string
    {
        return 'DashboardGenerator';
    }

    public function generate(array $definition): void
    {
        $module = $this->validateModuleName((string) $this->required($definition, 'module'));
        $class = $module . 'Dashboard';
        $namespace = 'App\\Modules\\' . $module . '\\Dashboard';

        $content = "<?php\n\ndeclare(strict_types=1);\n\nnamespace {$namespace};\n\nfinal class {$class}\n{\n    public function obtener(int \\$id): array\n    {\n        return [\n            'module' => '{$module}',\n            'id' => \\$id,\n            'widgets' => [],\n            'builders' => [],\n            'alerts' => [],\n            'statistics' => [],\n        ];\n    }\n}\n";

        $this->write($this->file($definition, 'Dashboard/' . $class . '.php'), $content, (bool) $this->config()['overwrite']);
    }
}
