<?php

use App\Exceptions\InternalServerErrorException;
use App\Exceptions\UrlNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (Illuminate\Http\Request $request, Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->renderable(function (Throwable $e) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 500);

            } else if($e instanceof ModelNotFoundException) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 404);

            } else if ($e instanceof InvalidArgumentException) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 403);
            }


            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        });

        $exceptions->reportable(function (Throwable $e) {
            Log::error('error',[
                'code' => $e->getCode(),
                'trace' => $e->getTrace(),
            ]);
        });
    })->create();
