<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

final class CrudBuilder extends AbstractGenerator
{
    public function name(): string
    {
        return 'CrudBuilder';
    }

    public function generate(array $definition): void
    {
        $module = $this->validateModuleName((string) $this->required($definition, 'module'));
        $templates = ['index' => 'index.tpl.php', 'form' => 'form.tpl.php', 'show' => 'show.tpl.php'];

        foreach ($templates as $view => $template) {
            if (!is_file($this->templatePath() . '/' . $template)) {
                continue;
            }
            $content = $this->render($template, [
                'MODULE' => $module,
                'ICON' => $definition['icon'] ?? 'bi-grid',
            ]);
            $this->write($this->file($definition, 'Views/' . $view . '.php'), $content, (bool) $this->config()['overwrite']);
        }
    }
}
