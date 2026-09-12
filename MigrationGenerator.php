<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

final class MigrationGenerator extends AbstractGenerator
{
    public function name(): string
    {
        return 'MigrationGenerator';
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

        $destination = $this->file($definition, 'Config/migration.php');

        $this->write(
            $destination,
            $content,
            $this->config()['overwrite']
        );
    }
}