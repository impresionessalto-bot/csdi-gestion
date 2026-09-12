<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

final class ViewGenerator extends AbstractGenerator
{
    public function name(): string
    {
        return 'ViewGenerator';
    }

    public function generate(array $definition): void
    {
        $moduleName = $this->required($definition, 'module');
        $templatePath = $this->templatePath();

        $views = ['index', 'form', 'show'];

        foreach ($views as $view) {
            $templateFile = $templatePath . '/' . $view . '.tpl';

            if (!is_file($templateFile)) {
                continue;
            }

            $content = $this->render(
                $templateFile,
                [
                    'MODULE' => $moduleName,
                    'ICON'   => $definition['icon'] ?? 'bi-grid',
                ]
            );

            $destination = $this->file($definition, 'Views/' . $view . '.php');

            $this->write(
                $destination,
                $content,
                $this->config()['overwrite']
            );
        }
    }
}