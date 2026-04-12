<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        Log::debug('>>> HANDLER PERSONALIZZATO ATTIVO <<<');

        return parent::render($request, $e);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->is('admin') || $request->is('admin/*')) {
            return redirect('/admin/login');
        }

        return redirect('/');
    }

    public function register(): void
    {
        //
    }
}
