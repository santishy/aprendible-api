<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Middleware\ValidateJsonApiHeaders;
use App\Http\Middleware\ValidateJsonApiDocument;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Responses\JsonApiValidationErrorResponse;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api/v1',
        commands: __DIR__.'/../routes/console.php',
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
        $middleware->alias([
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $exception, Request $request) {

            if ($request->isJsonApi()) {
                return new JsonApiValidationErrorResponse($exception);
            }
        });

        $exceptions->renderable(function (NotFoundHttpException $e) {
            throw new \App\Exceptions\JsonApi\NotFoundHttpException;
        });

        $exceptions->renderable(fn (BadRequestHttpException $e) => throw new \App\Exceptions\JsonApi\BadRequestHttpException);

        $exceptions->renderable(
            fn (AuthenticationException $e) => throw new \App\Exceptions\JsonApi\AuthenticationException
        );
    })->create();
