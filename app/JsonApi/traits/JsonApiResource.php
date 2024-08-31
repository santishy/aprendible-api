<?php

namespace App\JsonApi\traits;

use App\Http\Resources\CategoryResource;
use App\JsonApi\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

trait JsonApiResource
{

    abstract public function toJsonApi(): array;


    public static function identifier($resource)
    {
        return Document::type($resource->getResourceType())
            ->id($resource->getRouteKey())->toArray();
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        if (request()->filled('include')) {
            foreach ($this->getIncludes() as $include) {
                if ($include->resource instanceof MissingValue) {
                    continue;
                }
                $this->with['included'][] = $include;
            }
        }

        return Document::type($this->resource->getResourceType())
            ->id($this->resource->getRouteKey())
            ->attributes($this->filterAttributes($this->toJsonApi()))
            ->relationshipLinks($this->getRelationshipsLinks())
            ->links([
                "self" => route('api.v1.' . $this->resource->getResourceType() . '.show', $this->resource)
            ])
            ->get('data');
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

    public function getRelationshipsLinks(): array
    {
        return [];
    }
    public function getIncludes(): array
    {
        return [];
    }
    /**este metodo es llamado cuando ArticleResource::collection y se usa el path para obtener el resourceType */
    public static function collection($resources)
    {

        $collection = parent::collection($resources);
        if (request()->filled('include')) {
            foreach ($resources as $resource) {
                foreach ($resource->getIncludes() as $include) {
                    if ($include->resource instanceof MissingValue) {
                        continue;
                    }
                    $collection->with["included"][] = $include;
                }
            }
        }
        $collection->with["links"] = ["self" => $resources->path()];
        return $collection;
    }
}
