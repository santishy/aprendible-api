<?php

namespace App\JsonApi;

use Closure;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use InvalidArgumentException;

class JsonApiTestResponse
{

    public function assertJsonApiValidationErrors(): \Closure
    {
        return function ($attribute) {
            /** @var TestResponse $this */

            $pointer =  "/data/attributes/{$attribute}";

            if (Str::of($attribute)->startsWith("data")) {
                $pointer = "/" . str_replace(".", "/", $attribute);
            } elseif (Str::of($attribute)->startsWith("relationships")) {
                $pointer = '/data/' . str_replace(".", "/", $attribute) . "/data/id";
            }

            try {
                $this->assertJsonFragment([
                    "source" => [
                        "pointer" => $pointer
                    ]
                ])->assertStatus(422);
            } catch (ExpectationFailedException $e) {

                Assert::fail("failed to find a JSON:API validation error for key: {$attribute}" . PHP_EOL . PHP_EOL . $e->getMessage());
            }

            try {

                $this->assertJsonStructure([
                    "errors" => [
                        [
                            "title",
                            "detail",
                            "source" => ["pointer"]
                        ]
                    ]
                ]);
            } catch (ExpectationFailedException $e) {
                Assert::fail("failed to find a valid JSON:API error response"
                    . PHP_EOL . PHP_EOL . $e->getMessage());
            }


            $this->assertHeader("content-type", "application/vnd.api+json")
                ->assertStatus(422);
        };
    }

    public function assertJsonApiRelationshipLinks(): Closure
    {
        return function ($model, $relationships) {
            /** @var TestResponse $this */
            foreach ($relationships as $relation) {
                $this->assertJson([
                    "data" => [
                        "relationships" => [
                            $relation => [
                                "links" => [
                                    "self" => route("api.v1.{$model->getResourceType()}.relationships.{$relation}", $model->getRouteKey()),
                                    "related" => route("api.v1.{$model->getResourceType()}.{$relation}", $model->getRouteKey())
                                ]
                            ]
                        ]
                    ]
                ]);
            }
            return $this;
        };
    }

    public function assertJsonApiResource()
    {

        return function ($model, $attributes) {
            /** @var TestResponse $this */
            $this->assertJson([
                "data" => [
                    "type" => $model->getResourceType(),
                    "id" => (string) $model->getRouteKey(),
                    "attributes" => $attributes,
                    "links" => [
                        "self" => route('api.v1.' . $model->getResourceType() . '.show', $model)
                    ]
                ]
            ]);

            return $this->assertHeader(
                'Location',
                route('api.v1.' . $model->getResourceType() . '.show', $model)
            );
        };
    }
    public function assertJsonApiResourceCollection()
    {
        return function ($models, $attributesKeys) {
            /** @var TestResponse $this */

            $this->assertJsonStructure([
                "data" => [
                    //cualquier elemento
                    "*" => [
                        "attributes" => $attributesKeys
                    ]
                ]
            ]);

            foreach ($models as $model) {
                $this->assertJsonFragment([
                    "type" => $model->getResourceType(),
                    "id" => (string) $model->getRouteKey(),
                    "links" => [
                        "self" => route('api.v1.' . $model->getResourceType() . '.show', $model)
                    ]
                ]);
            }
            return $this;

            // $this->assertHeader(
            //     'Location',
            //     route('api.v1.' . $model->getResourceType() . '.show', $model)
            // );
        };
    }

    public function assertJsonApiError(): Closure
    {
        //argumentos con nombre ... x eso es q pueden ser opcionales sin importar el orden
        return function ($title = null, $detail = null, $status = null) {
            /** @var TestResponse $this */
            // se le pone ['title','detail'] para que cumpla la regla de jsonApi donde dice que la llave errors debe serun array [ de objetos ]
            try {
                $this->assertJsonStructure([
                    'errors' => [
                        '*' => ['title', 'detail']
                    ]
                ]);
            } catch (ExpectationFailedException $e) {
                Assert::fail("Error objects must be returns as an array keyed by errors in the top level of JSON:API DOCUMENT" . PHP_EOL . PHP_EOL . $e->getMessage());
            }
            $title && $this->assertJsonFragment([
                "title" => $title,
            ]);
            $detail && $this->assertJsonFragment([
                "detail" => $detail,
            ]);
            $status && $this->assertJsonFragment([
                "status" => $status,
            ]);

            $this->assertStatus((int) $status);

            return $this;
        };
    }
}
