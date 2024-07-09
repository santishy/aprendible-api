<?php

namespace App\JsonApi;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

class JsonApiTestResponse
{

    public function assertJsonApiValidationErrors(): \Closure
    {
        return function ($attribute) {
            /** @var TestResponse $this */
            $pointer = Str::of($attribute)->startsWith("data")
                ? "/" . str_replace(".", "/", $attribute)
                : "/data/attributes/{$attribute}";
            try {
                $this->assertJsonFragment([
                    "source" => [
                        "pointer" => $pointer
                    ]
                ])->assertStatus(422);
            } catch (ExpectationFailedException $e) {

                Assert::fail("failed to find a JSON:API validation error for key: {$attribute}"
                    . PHP_EOL . PHP_EOL . $e->getMessage());
            }

            try {

                $this->assertJsonStructure([
                    "errors" => [
                        [
                            "title", "detail", "source" => ["pointer"]
                        ]
                    ]
                ]);
            } catch (ExpectationFailedException $e) {
                Assert::fail("failed to find a vali JSON:API error response"
                    . PHP_EOL . PHP_EOL . $e->getMessage());
            }


            $this->assertHeader("content-type", "application/vnd.api+json")
                ->assertStatus(422);
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

            $this->assertHeader(
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


            // $this->assertHeader(
            //     'Location',
            //     route('api.v1.' . $model->getResourceType() . '.show', $model)
            // );
        };
    }
}
