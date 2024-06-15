<?php

namespace Tests\Feature;

use App\Http\Middleware\ValidateJsonApiDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ValidateJsonApiDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Route::any('test_route', fn () => "ok")
            ->middleware(ValidateJsonApiDocument::class);
    }

    public function test_data_is_required(): void
    {
        $this->postJson('test_route', [])
            ->assertJsonApiValidationErrors('data');

        $this->patchJson('test_route', [])
            ->assertJsonApiValidationErrors('data');
    }
    public function test_data_must_be_an_array(): void
    {
        $this->postJson('test_route', [
            "data" => "string"
        ])
            ->assertJsonApiValidationErrors('data');

        $this->patchJson('test_route', [
            "data" => "string"
        ])
            ->assertJsonApiValidationErrors('data');
    }
    public function test_data_type_is_required(): void
    {
        $this->postJson('test_route', [
            "data" => [
                "attributes" => ["name" => "test"]
            ]
        ])
            ->assertJsonApiValidationErrors('data.type');

        $this->patchJson('test_route', [
            "data" => [
                "attributes" => ["name" => "test"]
            ]
        ])
            ->assertJsonApiValidationErrors('data.type');
    }
    public function test_data_type_must_be_string(): void
    {
        $this->postJson('test_route', [
            "data" => [
                "type" => 1,
                "attributes" => ["name" => "test"]
            ]
        ])->dump()
            ->assertJsonApiValidationErrors('data.type');

        $this->patchJson('test_route', [
            "data" => [
                "type" => 1,
                "attributes" => ["name" => "test"]
            ]
        ])
            ->assertJsonApiValidationErrors('data.type');
    }

    public function test_data_attributes_is_required(): void
    {
        $this->postJson('test_route', [
            "data" => [
                "type" => "string"
            ]
        ])
            ->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('test_route', [
            "data" => [
                "type" => "string"
            ]
        ])
            ->assertJsonApiValidationErrors('data.attributes');
    }
    public function test_data_attributes_must_be_an_array(): void
    {
        $this->postJson('test_route', [
            "data" => [
                "attributes" => "string",
                "type" => "string"
            ]
        ])
            ->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('test_route', [
            "data" => [
                "attributes" => "string",
                "type" => "string"
            ]
        ])->assertJsonApiValidationErrors('data.attributes');
    }
}
