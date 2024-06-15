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

                $title = $exception->getMessage();

                return response()->json([
                    "errors" => collect($exception->errors())
                        ->map(function ($messages, $field) use ($title) {
                            return [
                                "title" => $title,
                                "detail" => $messages[0],
                                "source" => ["pointer" => "/" . str_replace(".", "/", $field)]
                            ];
                        })->values()
                ], 422)->withHeaders(["content-type" => "application/vnd.api+json"]);
            }

            return null;
        });
    })->create();
