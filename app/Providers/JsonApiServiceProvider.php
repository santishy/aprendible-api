<?php

namespace App\Providers;

use Illuminate\Support\Str;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;

class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     *
     */
    public function boot(): void
    {
        Builder::macro("allowedSorts", function ($allowedSorts) {
            if (request()->filled('sort')) {
                $sortFields = explode(",", request()->input('sort'));
                foreach ($sortFields as $sortField) {
                    $sortDirection = Str::of($sortField)->startsWith('-') ? "desc" : "asc";
                    $sortField = ltrim($sortField, "-");
                    abort_unless(in_array($sortField, $allowedSorts), 400);
                    $this->orderBy($sortField, $sortDirection);
                }
            }
            return $this;
        });
    }
}
