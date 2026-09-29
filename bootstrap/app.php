<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Para onde mandar quem tenta abrir o painel sem estar logada.
        // Sem isto o middleware 'auth' procura uma rota chamada 'login',
        // que nao existe aqui (a nossa e 'admin.login'), e o resultado e
        // erro 500 em vez de tela de login.
        $middleware->redirectGuestsTo(fn () => route('admin.login'));

        // E para onde mandar quem JA esta logada e abre /admin/login.
        $middleware->redirectUsersTo(fn () => route('admin.painel'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
