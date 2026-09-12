<?php
declare(strict_types=1);

namespace App\Modules\Creator\Services;

use App\Modules\Creator\Generator\Creator;
use App\Modules\Creator\Generator\TemplateResolver;
use RuntimeException;

final class Service
{
    public function generate(array $definition): bool
    {
        // Forzamos la ruta absoluta exacta donde tenés alojadas las plantillas en el servidor
        $templatesPath = $_SERVER['DOCUMENT_ROOT'] . '/App/Modules/Creator/Templates/default';

        // Si tu estructura no cuelga directo de public_html/DOCUMENT_ROOT, 
        // usá directamente la ruta física absoluta de tu entorno (ej: /home/tu_usuario/public_html/App/...)
        if (!is_dir($templatesPath)) {
            // Alternativa robusta basada en la ubicación del archivo actual subiendo niveles si es necesario
            $templatesPath = realpath(__DIR__ . '/../Templates/default');
        }

        if ($templatesPath === false || !is_dir($templatesPath)) {
            throw new RuntimeException(
                "No existe el directorio de plantillas 'default' en la ruta especificada."
            );
        }

        $templateResolver = new TemplateResolver($templatesPath);
        $creator = new Creator();

        return $creator->generate($definition);
    }

    public function template(): array
    {
        return [
            'status' => 'active',
            'engine' => 'CSDI Creator v3.0'
        ];
    }
}