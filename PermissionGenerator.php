<?php

declare(strict_types=1);

namespace App\Modules\Creator\Generator;


final class PermissionGenerator extends AbstractGenerator
{

    public function name(): string
    {
        return 'PermissionGenerator';
    }



    public function generate(array $definition): void
    {

        $module =
            $this->required(
                $definition,
                'module'
            );


        $lower =
            strtolower($module);



        $template =
            'permission.tpl';



        if (
            file_exists(
                $this->templatePath()
                .
                '/'
                .
                $template
            )
        ){

            $content =
                $this->render(
                    $template,
                    [
                        'MODULE'=>$module,
                        'LOWER'=>$lower
                    ]
                );


        } else {


            $content =
<<<PHP
<?php

declare(strict_types=1);

return [

    '{$lower}.ver',

    '{$lower}.crear',

    '{$lower}.editar',

    '{$lower}.eliminar'

];

PHP;

        }



        $destination =
            $this->file(
                $definition,
                'Config/permissions.php'
            );



        $this->write(
            $destination,
            $content,
            $this->config()['overwrite']
        );

    }

}