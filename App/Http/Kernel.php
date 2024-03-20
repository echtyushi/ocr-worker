<?php

namespace App\Http;

use App\Http\Middleware\StartSession;
use \Framework\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected array $middleware = [];

    protected array $route_middleware = [
        'web' => [
            StartSession::class
        ]
    ];
}