<?php

namespace App\Middleware;

use App\Classes\Request;
use Closure;
use Illuminate\Support\Facades\Log;

class LoggingRequest
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        Log::debug(var_export($request->all(), true));
        return $next($request);
    }
}
