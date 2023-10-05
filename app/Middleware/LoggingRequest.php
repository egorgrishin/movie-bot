<?php

namespace App\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class LoggingRequest
{
    /**
     * Handle an incoming request.
     */
    public function handle( $request, Closure $next): mixed
    {
        dd($request);
        Log::debug(var_export($request->all(), true));
        return $next($request);
    }
}
