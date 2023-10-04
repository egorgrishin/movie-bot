<?php

namespace App\Middleware;

use App\Classes\Request;
use Closure;

class ExampleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        return $next($request);
    }
}
