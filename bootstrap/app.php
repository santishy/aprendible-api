<?php

use App\Http\Middleware\ValidateJsonApiDocument;
use App\Http\Middleware\ValidateJsonApiHeaders;
use App\Http\Responses\JsonApiValidationErrorResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: 'api/v1',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->redirectUsersTo(function ($request) {
            // $guard = Auth::guard('sanctum')->check();
            // RedirectIfAuthenticated::redirectUsing(function ($request, $guard) {
            //     return new Response(204);
            // });
            $guard = Auth::guard('sanctum')->check();
            if ($guard) {
                RedirectIfAuthenticated::redirectUsing(function ($request, $guard) {
                    return response()->noContent();
                });
                return new Response(204);
            }
        });

        $middleware->api(append: [
            ValidateJsonApiHeaders::class,
            ValidateJsonApiDocument::class,
        ]);
        // $middleware->alias([
        //     "guest"
        // ]);
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

        $exceptions->renderable(
            fn(AuthenticationException $e) =>
            throw new App\Exceptions\JsonApi\AuthenticationException
        );
    })->create();
