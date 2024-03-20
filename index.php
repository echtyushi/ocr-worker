<?php

/*
|--------------------------------------------------------------------------
| Introduction
|--------------------------------------------------------------------------
|
| This application is made using our personal Framework. This Framework
| contains every tooling that makes a solid application abiding by PSR
| convention. Have fun.
|
|--------------------------------------------------------------------------
*/

use Framework\Foundation\Application;
use Framework\Http\Kernel;

/**
 * @var Application $app
 */
$app = require_once 'bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Application Setup
|--------------------------------------------------------------------------
|
| Here you register your configurations and setting your application's
| Service Providers. These registrations will be used to bootstrap the
| application.
|
|--------------------------------------------------------------------------
*/

$app->set_services(
    []
);

$app->bootstrap();

$app->get(Kernel::class)->handle(request());