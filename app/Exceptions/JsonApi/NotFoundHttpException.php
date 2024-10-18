<?php

namespace App\Exceptions\JsonApi;

use Exception;

class NotFoundHttpException extends Exception
{
    public function render($request)
    {
        $type = $request->input('data.type');
        $id = $request->input('data.id');
        //la espeficificacion jsonApi dice que la llave errors debe ser un array de objetos,osea una matriz en este caso

        return response()->json([
            'errors' => [[
                'title' => 'Not found',
                'detail' => "No records found with the id '{$id}' in the '{$type}' resource",
                'status' => '404',
            ]],
        ], 404);
    }
}
