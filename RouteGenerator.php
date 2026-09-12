<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

final class RouteGenerator extends AbstractGenerator
{
    public function name(): string
    {
        return 'RouteGenerator';
    }

    public function generate(array $definition): void
    {
        $module = $this->validateModuleName((string) $this->required($definition, 'module'));
        $template = 'routes.tpl.php';
        $content = $this->render($template, ['MODULE' => $module]);
        $this->write($this->file($definition, 'Config/routes.php'), $content, (bool) $this->config()['overwrite']);
    }
}
