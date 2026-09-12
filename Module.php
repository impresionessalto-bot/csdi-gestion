<?php

declare(strict_types=1);

namespace App\Modules\Creator;

use App\Core\Module as CoreModule;

/**
 * Definición del módulo Creator para el núcleo de CSDI PRO.
 */
final class Module extends CoreModule
{
    /**
     * Nombre del módulo
     */
    public function name(): string
    {
        return 'Creator';
    }

    /**
     * Ruta de configuración
     */
    public function routes(): string
    {
        return __DIR__.'/Config/routes.php';
    }

    /**
     * Menú lateral unificado bajo la sección de Herramientas del ERP
     */
    public function menu(): array
    {
        return [
            [
                'title'      => 'ERP Studio',
                'route'      => '/creator',
                'url'        => '/creator',
                'icon'       => 'bi bi-boxes',
                'group'      => 'Herramientas', // CORRECCIÓN: Agrupado bajo Herramientas
                'permission' => 'creator.view',
                'order'      => 900,
            ],
            [
                'title'      => 'Base de Datos (Studio)',
                'route'      => '/creator/database',
                'url'        => '/creator/database',
                'icon'       => 'bi bi-database',
                'group'      => 'Herramientas', // CORRECCIÓN: Agrupado bajo Herramientas
                'permission' => 'creator.view',
                'order'      => 910,
            ],
        ];
    }

    /**
     * Permisos del módulo
     */
    public function permissions(): array
    {
        return [
            'creator.view',
            'creator.create',
            'creator.generate',
            'creator.templates',
        ];
    }

    /**
     * Inicialización
     */
    public function boot(): void
    {
    }
}