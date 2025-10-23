<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (AuthorizationException $e, Request $request) {
            if ($request->route()->getName() === 'colmeias.create' || $request->route()->getName() === 'colmeias.store') {
                return back()->with('error', 'Apenas o dono do apiário pode criar novas colmeias.');
            }
            return back()->with('error', 'Você não tem permissão para executar esta ação.');
        });
    })->create();
