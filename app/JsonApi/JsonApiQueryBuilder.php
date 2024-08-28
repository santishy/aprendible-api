<?php

namespace App\JsonApi;

use Closure;
use Illuminate\Support\Str;

class JsonApiQueryBuilder
{

    public function allowedSorts(): \Closure
    {
        return function ($allowedSorts) {
            /** @var Builder $this */
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
        };
    }

    public function allowedFilters()
    {
        return function ($allowedFilters) {
            /** @var Builder $this */
            foreach (request('filter', []) as $filter => $value) {

                abort_unless(in_array($filter, $allowedFilters), 400);

                $this->hasNamedScope($filter)
                    ? $this->{$filter}($value)
                    :
                    $this->where($filter, 'LIKE', "%" . $value . "%");
            }
            return $this;
        };
    }
    public function sparseFieldset(): \Closure
    {
        return function () {
            /** @var Builder $this */
            if (request()->isNotFilled("fields")) {
                return $this;
            }


            $columns = explode(",", request("fields.{$this->getResourceType()}"));
            $keyRouteName = $this->model->getRouteKeyName();

            if (!in_array($keyRouteName, $columns)) {
                $columns[] = $keyRouteName;
            }

            return $this->addSelect($columns);
        };
    }
    public function jsonPaginate()
    {
        return  function () {
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
            if (property_exists($this->model, "resourceType")) {
                return $this->model->resourceType;
            }
            return $this->model->getTable();
        };
    }
    public function allowedIncludes(): Closure
    {
        return function ($allowedIncludes) {
            /** @var Builder $this */

            if (request()->isNotFilled("include")) {
                return $this;
            }
            $includes = explode(",", request()->input('include'));

            foreach ($includes as $include) {
                abort_unless(in_array($include, $allowedIncludes), 400);
                $this->with($include);
            }
            return $this;
        };
    }
}
