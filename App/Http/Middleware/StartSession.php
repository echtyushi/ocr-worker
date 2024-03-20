<?php

namespace App\Http\Middleware;

use Closure;
use Framework\Foundation\Session;
use Framework\Http\Request;

class StartSession
{
    /**
     * Session instance.
     *
     * @var Session
     */
    private Session $session;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->session->start();

        return $next($request);
    }
}