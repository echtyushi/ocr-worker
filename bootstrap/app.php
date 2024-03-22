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

use Framework\Foundation\Config;
use Framework\Foundation\Application;

require_once 'autoload.php';
require_once 'Framework/helpers.php';
require_once 'routes/web.php';

$app = new Application(getcwd());

$app->get(Config::class)->load_configuration_files(base_path('config'));

return $app;