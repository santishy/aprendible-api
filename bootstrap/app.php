<?php

use App\Http\Middleware\ValidateJsonApiDocument;
use App\Http\Middleware\ValidateJsonApiHeaders;
use App\Http\Responses\JsonApiValidationErrorResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PHPUnit\Util\InvalidJsonException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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

            if ($request->expectsJson() && !$request->routeIs('api.v1.login')) {
                return new JsonApiValidationErrorResponse($exception);
            }
        });

        $exceptions->renderable(function (NotFoundHttpException $e) {
            throw new App\Exceptions\JsonApi\NotFoundHttpException;
        });

        $exceptions->renderable(fn(BadRequestHttpException $e) =>
        throw new App\Exceptions\JsonApi\BadRequestHttpException);
    })->create();
