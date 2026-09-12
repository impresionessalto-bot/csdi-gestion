<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

final class SqlGenerator extends AbstractGenerator
{
    public function name(): string
    {
        return 'SqlGenerator';
    }

    public function generate(array $definition): void
    {
        $templateFile = 'migration.tpl';

        if (!is_file($templateFile)) {
            return;
        }

        $moduleName = $this->required($definition, 'module');
        $table = $this->required($definition, 'table');

        $content = $this->render(
            $templateFile,
            [
                'MODULE' => $moduleName,
                'TABLE'  => $table,
            ]
        );

        $destination = $this->file($definition, 'Config/migration.sql');

        $this->write(
            $destination,
            $content,
            $this->config()['overwrite']
        );
    }
}