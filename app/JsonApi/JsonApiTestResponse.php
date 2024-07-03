<?php 

namespace App\JsonApi;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

class JsonApiTestResponse{

    public function assertJsonApiValidationErrors() : \Closure{
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
}