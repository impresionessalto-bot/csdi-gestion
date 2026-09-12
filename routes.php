<?php

declare(strict_types=1);

use App\Core\Router;
use App\Modules\Creator\Controllers\Controller;

/*
|--------------------------------------------------------------------------
| CSDI ERP PRO
| MODULE ROUTES: CREATOR / ERP STUDIO
|--------------------------------------------------------------------------
*/

Router::group(
    '/creator',
    [
        'auth' // Middleware de seguridad del ERP
    ],
    function () {

        /*
        |--------------------------------------------------------------------------
        | Consola de ERP Studio (Dashboard)
        |--------------------------------------------------------------------------
        */
        Router::get(
            '/',
            [Controller::class, 'index']
        );

        /*
        |--------------------------------------------------------------------------
        | Diseñador / Explorador de Estructura de Base de Datos
        |--------------------------------------------------------------------------
        | Resuelve la URL '/creator/database' llamando al método database()
        | del controlador unificado de Creator.
        */
        Router::get(
            '/database',
            [Controller::class, 'database']
        );

        /*
        |--------------------------------------------------------------------------
        | Generación de Código y Módulos
        |--------------------------------------------------------------------------
        | Procesa las solicitudes de creación física de módulos en disco (POST).
        */
        Router::post(
            '/generate',
            [Controller::class, 'generate']
        );
    }
);