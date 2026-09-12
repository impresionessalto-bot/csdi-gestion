<?php
declare(strict_types=1);

namespace App\Modules\Creator\Generator;

final class ApiGenerator extends AbstractGenerator
{
    public function name(): string
    {
        return 'ApiGenerator';
    }

    public function generate(array $definition): void
    {
        $moduleName = $this->required($definition, 'module');
        $moduleLower = strtolower($moduleName);
        $namespace = 'App\\Modules\\' . $moduleName . '\\Api';

        // 1. Generar Controlador de API usando render() seguro
        try {
            $controllerContent = $this->render('ApiController', [
                'MODULE'    => $moduleName,
                'NAMESPACE' => $namespace,
            ]);
            $this->write($this->file($definition, 'Api/' . $moduleName . 'ApiController.php'), $controllerContent, $this->config()['overwrite']);
        } catch (\Throwable $e) {
            // Opcional: si la plantilla opcional no existe, continúa sin interrumpir el proceso
        }

        // 2. Generar Rutas de API
        try {
            $routesContent = $this->render('api_routes', [
                'MODULE' => $moduleName,
                'LOWER'  => $moduleLower,
            ]);
            $this->write($this->file($definition, 'Config/api_routes.php'), $routesContent, $this->config()['overwrite']);
        } catch (\Throwable $e) {
        }

        // 3. Generar Request de API
        try {
            $requestContent = $this->render('ApiRequest', [
                'MODULE'    => $moduleName,
                'NAMESPACE' => $namespace,
            ]);
            $this->write($this->file($definition, 'Api/' . $moduleName . 'ApiRequest.php'), $requestContent, $this->config()['overwrite']);
        } catch (\Throwable $e) {
        }

        // 4. Generar Response de API
        try {
            $responseContent = $this->render('ApiResponse', [
                'MODULE'    => $moduleName,
                'NAMESPACE' => $namespace,
            ]);
            $this->write($this->file($definition, 'Api/' . $moduleName . 'ApiResponse.php'), $responseContent, $this->config()['overwrite']);
        } catch (\Throwable $e) {
        }
    }
}