<?php

use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\EnsureProfileIsComplete;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        channels:__DIR__ . '/../routes/channels.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias middleware
        $middleware->alias([
            'verified' => EnsureEmailIsVerified::class,
            'profile.complete' => EnsureProfileIsComplete::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle ModelNotFoundException via NotFoundHttpException
        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            $previous = $e->getPrevious(); // Get the original exception

            if ($previous instanceof ModelNotFoundException) {
                $model = class_basename($previous->getModel()); // Extract model name (e.g., "Post", "Comment")

                return response()->json([
                    'status' => 'error',
                    'message' => "The {$model} was not found or has already been deleted."
                ], 404);
            }
        });

        // Handle AccessDeniedHttpException for unauthorized actions
        $exceptions->renderable(function (AccessDeniedHttpException $e, $request) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to perform this action.'
            ], 403);
        });
    })
    ->withBroadcasting(
        __DIR__ . '/../routes/channels.php',
        // ['middleware' => ['auth:sanctum']],
    )
    ->create();
