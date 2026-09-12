<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

final class ModuleGenerator extends AbstractGenerator
{
    public function name(): string
    {
        return 'ModuleGenerator';
    }

    public function generate(array $definition): void
    {
        $templateFile = 'module.tpl';

        if (!is_file($templateFile)) {
            return;
        }

        $moduleName = $this->required($definition, 'module');
        $className = 'Module';

        $content = $this->render(
            $templateFile,
            [
                'MODULE'    => $moduleName,
                'CLASS'     => $className,
                'ICON'      => $definition['icon'] ?? 'bi-grid',
                'NAMESPACE' => $this->namespace($definition),
            ]
        );

        $destination = $this->file($definition, $className . '.php');

        $this->write(
            $destination,
            $content,
            $this->config()['overwrite']
        );
    }
}