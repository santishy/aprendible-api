<?php

use App\Http\Middleware\ValidateJsonApiDocument;
use App\Http\Middleware\ValidateJsonApiHeaders;
use App\Http\Responses\JsonApiValidationErrorResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            ValidateJsonApiHeaders::class,
            ValidateJsonApiDocument::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $exception, Request $request) {

            if ($request->expectsJson()) {
                return new JsonApiValidationErrorResponse($exception);
            }

            return null;
        });

        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            $type = $request->input('data.type');
            $id = $request->input('data.id');
            //la espeficificacion jsonApi dice que la llave errors debe ser un array de objetos,osea una matriz en este caso

            return response()->json([
                "errors" => [
                    "title" => "Not found",
                    "detail" => "No records found with the id '{$id}' in the '{$type}' resource",
                    "status" => "404"
                ]
            ], 404);
        });
    })->create();
