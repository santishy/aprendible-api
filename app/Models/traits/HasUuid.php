<?php

namespace App\Models\traits;

use Illuminate\Support\Str;

trait HasUuid
{

    protected function getIncrementing()
    {
        return false;
    }
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            $model->{$model->getRouteKeyName()} = Str::uuid()->toString();
        });
    }
}
