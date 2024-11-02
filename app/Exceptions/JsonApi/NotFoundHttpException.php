<?php

namespace App\Exceptions\JsonApi;

use Exception;

class NotFoundHttpException extends Exception
{
    public function render($request)
    {

        //la espeficificacion jsonApi dice que la llave errors debe ser un array de objetos,osea una matriz en este caso

        return response()->json([
            'errors' => [[
                'title' => 'Not Found',
                'detail' => $this->getDetail($request),
                'status' => '404',
            ]],
        ], 404);
    }

    public function getDetail($request)
    {

        if (str($this->getMessage())->startsWith('No query results for model')) {
            return "No records found with the id '{$request->getResourceId()}' in the '{$request->getResourceType()}' resource";
        }

        return $this->getMessage();
    }
}
