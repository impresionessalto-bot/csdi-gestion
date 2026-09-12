<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

final class ServiceGenerator extends AbstractGenerator
{
    public function name(): string
    {
        return 'ServiceGenerator';
    }

    public function generate(array $definition): void
    {
        $templateFile = 'service.tpl';

        if (!is_file($templateFile)) {
            return;
        }

        $moduleName = $this->required($definition, 'module');
        $className = $moduleName . 'Service';

        $content = $this->render(
            $templateFile,
            [
                'MODULE'    => $moduleName,
                'CLASS'     => $className,
                'NAMESPACE' => $this->namespace($definition) . '\\Services',
            ]
        );

        $destination = $this->file($definition, 'Services/' . $className . '.php');

        $this->write(
            $destination,
            $content,
            $this->config()['overwrite']
        );
    }
}