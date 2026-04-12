<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException; // <--- AGGIUNGI QUESTO
use Illuminate\Http\Request;                   // <--- AGGIUNGI QUESTO

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Qui caricherai i tuoi middleware admin/user via FTP quando li avrai pronti
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // GESTIONE 419
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            return redirect()->back()
                ->with('error', 'Sessione scaduta, riprova.');
        });

    })->create();
