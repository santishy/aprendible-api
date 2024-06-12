<?php

use App\Http\Middleware\ValidateJsonApiHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: 'api/v1',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(append: [
            ValidateJsonApiHeaders::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $exception, Request $request) {
            if ($request->expectsJson()) {
                $title = str_replace(".","/",)
                collect($exception->errors())
                    ->map(function ($messages, $key) {
                        return [
                            "title" => "lo que sea",
                            "detail" => "detalles",
                            "source" => ["pointer" => '/data/attributes/title']
                        ];
                    });


                return response()->json([
                    "errors" => []
                ]);
            }

            return null;
        });
    })->create();
