<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

final class BuilderGenerator extends AbstractGenerator
{
    public function name(): string
    {
        return 'BuilderGenerator';
    }

    public function generate(array $definition): void
    {
        $module = $this->validateModuleName((string) $this->required($definition, 'module'));
        $builders = $definition['builders'] ?? ['GeneralBuilder'];
        if (!is_array($builders) || $builders === []) {
            $builders = ['GeneralBuilder'];
        }

        foreach ($builders as $builder) {
            $class = $this->className((string) $builder);
            $content = "<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Modules\\{$module}\\Builders;\n\nfinal class {$class}\n{\n    public function build(array \\$context = []): array\n    {\n        return [\n            'module' => '{$module}',\n            'builder' => '{$class}',\n            'context' => \\$context,\n        ];\n    }\n}\n";
            $this->write($this->file($definition, 'Builders/' . $class . '.php'), $content, (bool) $this->config()['overwrite']);
        }
    }
}
