<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->renderable(function (\Throwable $exception, Request $request) {

            if (! $request->expectsJson() && ! $request->is('api/*')) {
                return null;
            }

            if ($exception instanceof ModelNotFoundException) {
                return response()->json([
                    'error' => true,
                    'message' => 'Registro não encontrado',
                    'response' => []
                ], 404);
            }

            if ($exception instanceof ValidationException) {
                return response()->json([
                    'error' => true,
                    'message' => 'Dados inválidos',
                    'response' => $exception->errors()
                ], 422);
            }

            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'error' => true,
                    'message' => 'Registro não encontrado',
                    'response' => []
                ], 404);
            }

            if ($exception instanceof HttpExceptionInterface) {
                return response()->json([
                    'error' => true,
                    'message' => $exception->getMessage() ?: 'Erro de requisição',
                    'response' => []
                ], $exception->getStatusCode());
            }

            return response()->json([
                'error' => true,
                'message' => 'Ocorreu um erro inesperado',
                'response' => []
            ], 500);
        });
    })

->create();
