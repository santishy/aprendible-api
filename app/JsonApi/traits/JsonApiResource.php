<?php

namespace App\JsonApi\traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait JsonApiResource
{

    abstract public function toJsonApi(): array;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => $this->getResourceType(),
            "id" => (string) $this->resource->getRouteKey(),
            "attributes" => $this->filterAttributes(
                $this->toJsonApi()
            ),
            "links" => [
                "self" => route('api.v1.' . $this->getResourceType() . '.show', $this->resource)
            ]
        ];
    }

    public function withResponse(Request $request, JsonResponse $response)
    {
        $response->header(
            "Location",
            route('api.v1.' . $this->getResourceType() . '.show', $this->resource)
        );
    }

    public function filterAttributes(array $attributes): array
    {
        return array_filter(
            $attributes,
            /**
                    esta funcion es un closure para array_filter en donde se evalua
                    propiedad x prop y es por eso que que pregunto si en fields[resource]=title
                    no viene el getRouteKey .. lo borre de aqqui de attributes json:api
             */
            function ($value) {

                if (request()->isNotFilled("fields")) {
                    return true;
                }
                $fields = explode(",", request('fields.' . $this->getResourceType()));

                if ($value === $this->getRouteKey()) {
                    return in_array($this->getRouteKeyName(), $fields);
                }
                return $value;
            }
        );
    }

    /**este metodo es llamado cuando ArticleResource::collection y se usa el path para obtener el resourceType */
    public static function collection($resource)
    {
        $collection = parent::collection($resource);
        $collection->with["links"] = ["self" => $resource->path()];
        return $collection;
    }
}
