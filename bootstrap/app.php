<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\PermissionMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(
            [
                'role' => RoleMiddleware::class,
                'permission' => PermissionMiddleware::class,
            ]
        );
        $middleware->statefulApi();
        //
    })
    ->withExceptions(
        function (Exceptions $exceptions) {
            //


            $exceptions->render(function (ValidationException $e, $request) {

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            });

            $exceptions->render(function (AuthenticationException $e) {

                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            });

            $exceptions->render(function (AuthorizationException $e) {

                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden.',
                ], 403);
            });

            $exceptions->render(function (ModelNotFoundException $e) {

                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found.',
                ], 404);
            });

            $exceptions->render(function (NotFoundHttpException $e) {

                return response()->json([
                    'success' => false,
                    'message' => 'Route not found.',
                ], 404);
            });

            $exceptions->render(function (HttpException $e) {

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Http Error.',
                ], $e->getStatusCode());
            });

            $exceptions->render(function (Throwable $e) {

                return response()->json([
                    'success' => false,
                    'message' => config('app.debug')
                        ? $e->getMessage()
                        : 'Internal server error.',
                ], 500);
            });
        }
    )->create();
