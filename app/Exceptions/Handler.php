<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        Log::info('Unauthenticated handler called', ['expects_json' => $request->expectsJson()]);
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Tidak terautentikasi'], 401);
        }

        return parent::unauthenticated($request, $exception);
    }
}
