<?php

namespace App\JsonApi\Mixins;

use Closure;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class JsonApiQueryBuilder
{
    public function allowedSorts(): \Closure
    {
        return function ($allowedSorts) {
            /** @var Builder $this */
            if (request()->filled('sort')) {
                $sortFields = explode(',', request()->input('sort'));
                foreach ($sortFields as $sortField) {
                    $sortDirection = Str::of($sortField)->startsWith('-') ? 'desc' : 'asc';
                    $sortField = ltrim($sortField, '-');
                    if (! in_array($sortField, $allowedSorts)) {
                        throw new BadRequestHttpException("The sort field '{$sortField}' is not allowed in the '{$this->getResourceType()}' resource");
                    }

                    $this->orderBy($sortField, $sortDirection);
                }
            }

            return $this;
        };
    }

    public function allowedFilters()
    {
        return function ($allowedFilters) {
            /** @var Builder $this */
            foreach (request('filter', []) as $filter => $value) {

                if (! in_array($filter, $allowedFilters)) {
                    throw new BadRequestHttpException("The filter '{$filter}' is not allowed in the '{$this->getResourceType()}' resource");
                }

                $this->hasNamedScope($filter)
                    ? $this->{$filter}($value)
                    :
                    $this->where($filter, 'LIKE', '%'.$value.'%');
            }

            return $this;
        };
    }

    public function sparseFieldset(): \Closure
    {
        return function () {
            /** @var Builder $this */
            if (request()->isNotFilled('fields')) {
                return $this;
            }

            $columns = explode(',', request("fields.{$this->getResourceType()}"));
            $keyRouteName = $this->model->getRouteKeyName();

            if (! in_array($keyRouteName, $columns)) {
                $columns[] = $keyRouteName;
            }

            return $this->addSelect($columns);
        };
    }

    public function jsonPaginate()
    {
        return function () {
            /** @var Builder $this */
            return $this->paginate(
                $perPage = request('page.size', 15),
                $columns = ['*'],
                $pageName = 'page[number]',
                $page = request('page.number', null),
                $total = null
                //appends es para agregar un nuevo parametro a la paginacion, el page.size es un array por que page tiene como llaves size y number pero number viene por defecto asi que agrego la siguiente llave y only me lo trae como un array
            )->appends(request()->only('sort', 'page.size', 'filter'));
        };
    }

    public function getResourceType(): Closure
    {
        return function () {
            /** @var Builder $this */
            if (property_exists($this->model, 'resourceType')) {
                return $this->model->resourceType;
            }

            return $this->model->getTable();
        };
    }

    public function allowedIncludes(): Closure
    {
        return function ($allowedIncludes) {
            /** @var Builder $this */
            if (request()->isNotFilled('include')) {
                return $this;
            }
            $includes = explode(',', request()->input('include'));

            foreach ($includes as $include) {
                if (! in_array($include, $allowedIncludes)) {
                    throw new BadRequestHttpException(
                        "The included relationship '{$include}' is not allowed in the '{$this->getResourceType()}' resource"
                    );
                }
                $this->with($include);
            }

            return $this;
        };
    }
}
