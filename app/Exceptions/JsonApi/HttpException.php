<?php

namespace App\Exceptions\JsonApi;

use Symfony\Component\HttpKernel\Exception\HttpException as BaseExceptionHttp;

class HttpException extends BaseExceptionHttp
{

    public function __construct($e)
    {
        dd('hola');
        //parent::__construct($e->getStatusCode());
    }
}
