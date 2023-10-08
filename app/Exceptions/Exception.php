<?php

namespace App\Exceptions;

use App\Classes\Lumen\Http\Dto;
use App\Classes\Lumen\Http\Request;
use Exception as BaseException;
use Throwable;

abstract class Exception extends BaseException
{
    protected Dto $dto;

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        /** @var Request $request */
        $request = request();
        $this->dto = $request->dto;
    }
}
