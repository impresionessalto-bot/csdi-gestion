<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

final class WidgetGenerator extends AbstractGenerator
{
    public function name(): string
    {
        return 'WidgetGenerator';
    }

    public function generate(array $definition): void
    {
        $module = $this->validateModuleName((string) $this->required($definition, 'module'));
        $widgets = $definition['widgets'] ?? ['SummaryWidget'];
        if (!is_array($widgets) || $widgets === []) {
            $widgets = ['SummaryWidget'];
        }

        foreach ($widgets as $widget) {
            $class = $this->className((string) $widget);
            $content = "<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Modules\\{$module}\\Widgets;\n\nfinal class {$class}\n{\n    public function render(array \\$data = []): array\n    {\n        return [\n            'type' => 'widget',\n            'module' => '{$module}',\n            'name' => '{$class}',\n            'data' => \\$data,\n        ];\n    }\n}\n";
            $this->write($this->file($definition, 'Widgets/' . $class . '.php'), $content, (bool) $this->config()['overwrite']);
        }
    }
}
