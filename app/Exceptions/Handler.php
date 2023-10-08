<?php

namespace App\Exceptions;

use App\Contracts\TelegramException;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Report or log an exception.
     * @throws ServerError
     */
    public function render($request, Throwable $e): JsonResponse
    {
        $e instanceof TelegramException
            ? $e->sendMessage()
            : throw new ServerError();
        return response()->json();
    }
}
