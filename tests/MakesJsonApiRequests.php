<?php

namespace Tests;

trait MakesJsonApiRequests
{
    public function json($method, $uri, array $data = [], array $headers = [], $options = 0)
    {
        $headers['accept'] = "application/vnd.api+json";
        return parent::json($method, $uri, $data, $headers, $options = 0);
    }
    public function postJson($uri, array $data = [], array $headers = [], $options = 0)
    {
        $headers['content-type'] = "application/vnd.api+json";
        return parent::postJson($uri, $data, $headers, $options = 0);
    }
    public function patchJson($uri, array $data = [], array $headers = [], $options = 0)
    {
        $headers['content-type'] = "application/vnd.api+json";
        return parent::patchJson($uri, $data, $headers, $options = 0);
    }
}
