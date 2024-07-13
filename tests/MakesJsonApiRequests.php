<?php

namespace Tests;

use App\JsonApi\Document;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;

trait MakesJsonApiRequests
{
    protected bool $formatJsonApiDocument = true;

    public function withoutJsonApiDocumentFormatting()
    {
        $this->formatJsonApiDocument = false;
    }
    public function json($method, $uri, array $data = [], array $headers = [], $options = 0)
    {
        $headers['accept'] = "application/vnd.api+json";
        if ($this->formatJsonApiDocument) {
            $formattedData = $this->getFormattedData($uri, $data);
        }

        return parent::json($method, $uri, $formattedData ?? $data, $headers, $options = 0);
    }
    protected function getFormattedData($uri, $data)
    {
        $path = parse_url($uri)["path"];

        $type = (string) Str::of($path)->after('/api/v1/')->before("/");
        $id = (string) Str::of($path)->after($type)->replace("/", "");

        return Document::type($type)
            ->id($id)
            ->attributes($data)
            ->relationships($data["_relationships"] ?? []) //se manda vacio para que retorne solo el $this
            ->toArray();
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
