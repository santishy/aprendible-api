<?php

namespace App\Exceptions\JsonApi;

use Illuminate\Support\Str;

use Exception;

class NotFoundHttpException extends Exception
{
    public function render($request)
    {
        $detail = $this->getMessage();

        if (str($this->getMessage())->startsWith('No query results for model')) {

            $type = $request->filled('data.type')
                ? $request->input('data.type')
                : (string) Str::of($request->path())->after('api/v1/')->before('/');

            $id = $request->filled('data.id')
                ? $request->input('data.id')
                : (string) Str::of($request->path())->after($type)->replace('/', '');

            if ($type && $id) {
                $detail = "No records found with the id '{$id}' in the '{$type}' resource";
            }
        }


        //la espeficificacion jsonApi dice que la llave errors debe ser un array de objetos,osea una matriz en este caso

        return response()->json([
            'errors' => [[
                'title' => 'Not found',
                'detail' => $detail,
                'status' => '404',
            ]],
        ], 404);
    }
}
