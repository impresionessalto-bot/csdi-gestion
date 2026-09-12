<?php
declare(strict_types=1);

namespace App\Modules\Creator\Services;

use App\Modules\Creator\Generator\ControllerGenerator;
use App\Modules\Creator\Generator\ModuleGenerator;
use App\Modules\Creator\Generator\RepositoryGenerator;
use App\Modules\Creator\Generator\RouteGenerator;
use App\Modules\Creator\Generator\ServiceGenerator;
use App\Modules\Creator\Generator\ViewGenerator;
use App\Modules\Creator\Generator\SqlGenerator;
use InvalidArgumentException;

final class CreatorService
{
    public function __construct(
        private ModuleGenerator $moduleGenerator,
        private ControllerGenerator $controllerGenerator,
        private RepositoryGenerator $repositoryGenerator,
        private ServiceGenerator $serviceGenerator,
        private RouteGenerator $routeGenerator,
        private ViewGenerator $viewGenerator,
        private SqlGenerator $sqlGenerator
    ) {
    }

    /**
     * ============================================================
     * Genera un módulo completo
     * ============================================================
     */
    public function generate(array $definition): array
    {
        $this->validate($definition);

        $result = [];

        $result['module'] = $this->moduleGenerator->generate($definition);

        $result['controller'] = $this->controllerGenerator->generate($definition);

        $result['repository'] = $this->repositoryGenerator->generate($definition);

        $result['service'] = $this->serviceGenerator->generate($definition);

        $result['routes'] = $this->routeGenerator->generate($definition);

        $result['views'] = $this->viewGenerator->generate($definition);

        $result['sql'] = $this->sqlGenerator->generate($definition);

        return $result;
    }

    /**
     * ============================================================
     * Template vacío
     * ============================================================
     */
    public function template(): array
    {
        return [

            'module' => '',

            'table' => '',

            'icon' => 'bi-grid',

            'description' => '',

            'order' => 100,

            'permissions' => [],

            'fields' => []

        ];
    }

    /**
     * ============================================================
     * Validación
     * ============================================================
     */
    private function validate(array $definition): void
    {
        if (empty($definition['module'])) {
            throw new InvalidArgumentException(
                'Debe indicar el nombre del módulo.'
            );
        }

        if (empty($definition['table'])) {
            throw new InvalidArgumentException(
                'Debe indicar la tabla.'
            );
        }

        if (!isset($definition['fields'])) {
            throw new InvalidArgumentException(
                'Debe definir los campos.'
            );
        }

        if (!is_array($definition['fields'])) {
            throw new InvalidArgumentException(
                'Los campos son inválidos.'
            );
        }
    }
}