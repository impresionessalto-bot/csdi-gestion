<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

final class RepositoryGenerator extends AbstractGenerator
{
    public function name(): string
    {
        return 'RepositoryGenerator';
    }

    public function generate(array $definition): void
    {
        $templateFile = 'repository.tpl';

        if (!is_file($templateFile)) {
            return;
        }

        $moduleName = $this->required($definition, 'module');
        $className = $moduleName . 'Repository';

        $content = $this->render(
            $templateFile,
            [
                'MODULE'    => $moduleName,
                'CLASS'     => $className,
                'TABLE'     => $this->required($definition, 'table'),
                'NAMESPACE' => $this->namespace($definition) . '\\Repositories',
            ]
        );

        $destination = $this->file($definition, 'Repositories/' . $className . '.php');

        $this->write(
            $destination,
            $content,
            $this->config()['overwrite']
        );
    }
}