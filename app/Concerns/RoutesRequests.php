<?php

namespace App\Concerns;

use App\Classes\Lumen\Http\Request;
use Illuminate\Http\Request as LaravelRequest;
use Laravel\Lumen\Concerns\RoutesRequests as LumenRoutesRequests;

trait RoutesRequests
{
    use LumenRoutesRequests;

    /**
     * Parse the incoming request and return the method and path info.
     */
    protected function parseIncomingRequest($request): array
    {
        if (!$request) {
            $request = Request::capture();
        }
        $this->instance(LaravelRequest::class, $this->prepareRequest($request));

        return [
            $request->getMethod(),
            '/' . trim($request->getPathInfo(), '/')
        ];
    }
}
