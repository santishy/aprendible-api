<?php

namespace App\JsonApi\Exceptions;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException as BaseExceptionHttp;

class HttpException extends BaseExceptionHttp
{
    public function __construct($e)
    {

        parent::__construct($e->getStatusCode(), $e->getMessage());
    }

    public function render($request): JsonResponse
    {

        method_exists($this, $method = "get{$this->getStatusCode()}Detail")
            ? $detail = $this->{$method}($request)
            : $detail = $this->getMessage();

        return response()->json([
            'errors' => [[
                'title' => Response::$statusTexts[$this->getStatusCode()],
                'detail' => $detail,
                'status' => (string) $this->getStatusCode(),
            ]],
        ], $this->getStatusCode());
    }

    public function get404Detail($request)
    {

        if (str($this->getMessage())->startsWith('No query results for model')) {
            return "No records found with the id '{$request->getResourceId()}' in the '{$request->getResourceType()}' resource";
        }

        return $this->getMessage();
    }
}
