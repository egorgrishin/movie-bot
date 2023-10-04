<?php

namespace App\Classes;

use Laravel\Lumen\Http\Request as LumenRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends LumenRequest
{
    public readonly Dto $dto;

    /**
     * Create an Illuminate request from a Symfony instance.
     */
    public static function createFromBase(SymfonyRequest $request): static
    {
        $request = parent::createFromBase($request);
        $request->dto = Dto::make($request);

        return $request;
    }
}
