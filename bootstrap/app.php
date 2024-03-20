<?php

/*
|--------------------------------------------------------------------------
| Bootstrap
|--------------------------------------------------------------------------
|
| This application is made using our personal Framework. This Framework
| contains every tooling that makes a solid application abiding by PSR
| convention. Have fun.
|
|--------------------------------------------------------------------------
*/

use App\Http\Kernel;
use Framework\Foundation\Session;
use Framework\Http\Kernel as HttpKernel;
use Framework\Foundation\Application;

require_once 'autoload.php';
require_once 'Framework/helpers.php';
require_once 'routes/web.php';

$app = new Application(getcwd());

$app->singleton(HttpKernel::class, Kernel::class);
$app->get(Session::class)->start();

return $app;