<?php

namespace App\Exceptions\JsonApi;

use Exception;

class BadRequestHttpException extends Exception
{
    public function render($request)
    {

        return response()->json([
            "errors" =>
            [[
                "title" => "Bad request",
                "detail" => $this->getMessage(),
                "status" => "400"
            ]]
        ], 400);
    }
}
