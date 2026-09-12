<?php

declare(strict_types=1);

namespace App\Modules\Creator\Generator;

use RuntimeException;

final class MenuGenerator extends AbstractGenerator
{

    public function name(): string
    {
        return 'MenuGenerator';
    }


    /**
     * Genera Config/menu.php del módulo
     */
    public function generate(array $definition): void
    {

        $moduleName =
            $this->required(
                $definition,
                'module'
            );


        $moduleName =
            $this->sanitize(
                $moduleName
            );


        $moduleLower =
            strtolower(
                $moduleName
            );


        $icon =
            $definition['icon']
            ??
            'bi-grid';


        $url =
            $definition['url']
            ??
            '/' . $moduleLower;


        $order =
            $definition['order']
            ??
            50;



        $templateFile =
            $this->templatePath()
            .
            '/menu.tpl.php';



        if (is_file($templateFile)) {


            $content =
                $this->render(
                    $templateFile,
                    [

                        'MODULE'=>
                            $moduleName,


                        'LOWER'=>
                            $moduleLower,


                        'ICON'=>
                            $icon,


                        'URL'=>
                            $url,


                        'ORDER'=>
                            $order

                    ]
                );


        } else {


            $content =
                $this->defaultContent(
                    $moduleName,
                    $url,
                    $icon,
                    $order
                );


        }



        $destination =
            $this->file(
                $definition,
                'Config/menu.php'
            );



        $this->write(
            $destination,
            $content,
            $this->config()['overwrite']
            ??
            false
        );

    }





    /**
     * Contenido por defecto
     */
    private function defaultContent(
        string $module,
        string $url,
        string $icon,
        int $order
    ): string {


        $module =
            addslashes($module);


        $url =
            addslashes($url);


        $icon =
            addslashes($icon);



        return <<<PHP
<?php

declare(strict_types=1);

return [

    'titulo' => '{$module}',

    'url' => '{$url}',

    'icon' => '{$icon}',

    'order' => {$order}

];

PHP;

    }





    /**
     * Limpieza básica
     */
    private function sanitize(
        string $value
    ): string {


        $value =
            trim($value);



        if ($value === '') {

            throw new RuntimeException(
                'Valor vacío no permitido'
            );

        }


        return $value;

    }


}