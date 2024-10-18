<?php

namespace App\Http\Resources;

use App\JsonApi\traits\JsonApiResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    use JsonApiResource;

    public function toJsonApi(): array
    {
        return [
            'name' => $this->resource->name,
        ];
    }
}
