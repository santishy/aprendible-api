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
    protected bool $addJsonApiHeaders = true;

    public function withoutJsonApiDocumentFormatting(): self
    {
        $this->formatJsonApiDocument = false;
        return $this;
    }
    public function withoutJsonApiHeaders(): self
    {
        $this->addJsonApiHeaders = false;
        return $this;
    }
    public function withoutJsonApiHelpers()
    {
        $this->addJsonApiHeaders = false;
        $this->formatJsonApiDocument = false;
        return $this;
    }
    public function json($method, $uri, array $data = [], array $headers = [], $options = 0)
    {
        if ($this->addJsonApiHeaders) {
            $headers['accept'] = "application/vnd.api+json";
            if ($method === 'POST' || $method === 'PATCH') {
                $headers['content-type'] = "application/vnd.api+json";
            }
        }
        if ($this->formatJsonApiDocument) {
            if (!isset($data['data']))
                $formattedData = $this->getFormattedData($uri, $data);
        }

        return parent::json($method, $uri, $formattedData ?? $data, $headers, $options = 0);
    }
    protected function getFormattedData($uri, $data)
    {
        $path = parse_url($uri)["path"];
        // $path = route("api.v1.articles.show", ["article" => 1, "relationships" => "alguna"]);
        $type = (string) Str::of($path)->after('/api/v1/')->before("/");
        $id = (string) Str::of($path)->after($type)->replace("/", "");
        //dd($data);
        return Document::type($type)
            ->id($id)
            ->attributes($data)
            ->relationshipsData($data["_relationships"] ?? []) //se manda vacio para que retorne solo el $this
            ->toArray();
    }
    //YA NO SE OCUPARIAN ESTOS METODOS POR QUE LA VALIDACION SE HACEN EN METODO JSON
    // public function postJson($uri, array $data = [], array $headers = [], $options = 0)
    // {
    //     if ($this->addJsonApiHeaders) {
    //         $headers['content-type'] = "application/vnd.api+json";
    //     }
    //     return parent::postJson($uri, $data, $headers, $options = 0);
    // }
    // public function patchJson($uri, array $data = [], array $headers = [], $options = 0)
    // {
    //     if ($this->addJsonApiHeaders) {
    //         $headers['content-type'] = "application/vnd.api+json";
    //     }
    //     return parent::patchJson($uri, $data, $headers, $options = 0);
    // }
}
