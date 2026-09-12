<?php
declare(strict_types=1);

use App\Core\Router;
use App\Modules\Creator\Controllers\Controller;


Router::get(
    '/creator',
    [Controller::class,'index']
);


Router::post(
    '/creator/generate',
    [Controller::class,'generate']
);