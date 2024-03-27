<?php

namespace App\Http\Middleware;

use Closure;
use Framework\Foundation\Session;
use Framework\Http\Middleware;
use Framework\Http\Request;

class SessionMiddleware extends Middleware
{
    /**
     * Session instance.
     *
     * @var Session
     */
    private Session $session;

    /**
     * SessionMiddleware constructor.
     *
     * @param Session $session The session instance.
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request The incoming request.
     * @param Closure $next The next middleware closure.
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->session->start();

        return $next($request);
    }
}