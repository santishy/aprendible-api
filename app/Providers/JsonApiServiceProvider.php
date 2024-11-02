<?php

namespace App\Providers;

use App\JsonApi\Exceptions\Handler;
use Illuminate\Testing\TestResponse;
use App\JsonApi\Mixins\JsonApiRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use App\JsonApi\Mixins\JsonApiQueryBuilder;
use App\JsonApi\Mixins\JsonApiTestResponse;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;

class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ExceptionHandlerContract::class, Handler::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Builder::mixin(new JsonApiQueryBuilder);

        TestResponse::mixin(new JsonApiTestResponse);

        Request::mixin(new JsonApiRequest);
    }
}
