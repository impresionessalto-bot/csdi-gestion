<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

final class ControllerGenerator extends AbstractGenerator
{
    public function name(): string
    {
        return 'ControllerGenerator';
    }

    public function generate(array $definition): void
    {
        $templateFile = 'controller.tpl';

        if (!is_file($templateFile)) {
            return;
        }

        $moduleName = $this->required($definition, 'module');
        $className = $moduleName . 'Controller';

        $content = $this->render(
            $templateFile,
            [
                'MODULE'    => $moduleName,
                'CLASS'     => $className,
                'NAMESPACE' => $this->namespace($definition) . '\\Controllers',
            ]
        );

        $destination = $this->file($definition, 'Controllers/' . $className . '.php');

        $this->write(
            $destination,
            $content,
            $this->config()['overwrite']
        );
    }
}