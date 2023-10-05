<?php

namespace App\Classes\Lumen\Http;

use Laravel\Lumen\Http\Request as LumenRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends LumenRequest
{
    public Dto $dto;

    /**
     * Create an Illuminate request from a Symfony instance.
     */
    public static function createFromBase(SymfonyRequest $request): static
    {
        $request = parent::createFromBase($request);
        $request->dto = DtoFactory::createDto($request);

        return $request;
    }
}
