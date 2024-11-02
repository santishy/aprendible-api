<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\JsonApi\Http\Middleware\ValidateJsonApiDocument;

class ValidateJsonApiDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        $this->withoutJsonApiDocumentFormatting();
        parent::setUp();
        Route::any('api/test-route', fn () => 'ok')
            ->middleware(ValidateJsonApiDocument::class);
    }

    public function test_data_is_required(): void
    {

        $this->postJson('api/test-route', [])
            ->assertJsonApiValidationErrors('data');

        $this->patchJson('api/test-route', [])
            ->assertJsonApiValidationErrors('data');
    }

    public function test_data_must_be_an_array(): void
    {
        $this->postJson('api/test-route', [
            'data' => 'string',
        ])
            ->assertJsonApiValidationErrors('data');

        $this->patchJson('api/test-route', [
            'data' => 'string',
        ])
            ->assertJsonApiValidationErrors('data');
    }

    public function test_data_type_is_required(): void
    {
        $this->postJson('api/test-route', [
            'data' => [
                'attributes' => ['name' => 'test'],
            ],
        ])
            ->assertJsonApiValidationErrors('data.type');

        $this->patchJson('api/test-route', [
            'data' => [
                'attributes' => ['name' => 'test'],
            ],
        ])->assertJsonApiValidationErrors('data.type');

        //se reproducio el error de que cuando se actualizen algunos recursos asociados a un recurso, ya que como el envoltorio cambia y el id y el type sobre todo en este vergo PATCH ... queda fuera de data.id o data.type, sino data.0.id o data.0.type y no deberia de ser requerido en ese caso
        $this->patchJson('api/test-route', [
            'data' => [
                [
                    'id' => 'string',
                    'type' => 'resourceType',
                ],
            ],
        ])->assertSuccessful();
    }

    public function test_data_type_must_be_string(): void
    {
        $this->postJson('api/test-route', [
            'data' => [
                'type' => 1,
                'attributes' => ['name' => 'test'],
            ],
        ])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('api/test-route', [
            'data' => [
                'type' => 1,
                'attributes' => ['name' => 'test'],
            ],
        ])
            ->assertJsonApiValidationErrors('data.type');
    }

    public function test_data_attributes_is_required(): void
    {
        $this->postJson('api/test-route', [
            'data' => [
                'type' => 'string',
            ],
        ])
            ->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('api/test-route', [
            'data' => [
                'type' => 'string',
            ],
        ])
            ->assertJsonApiValidationErrors('data.attributes');
    }

    public function test_data_attributes_must_be_an_array(): void
    {
        $this->postJson('api/test-route', [
            'data' => [
                'attributes' => 'string',
                'type' => 'string',
            ],
        ])
            ->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('api/test-route', [
            'data' => [
                'attributes' => 'string',
                'type' => 'string',
            ],
        ])->assertJsonApiValidationErrors('data.attributes');
    }

    public function test_data_id_is_required(): void
    {
        $this->patchJson('/api/test-route', [
            'data' => [
                'type' => 'articles',
                'attributes' => ['name' => 'test'],
            ],
        ])->assertJsonApiValidationErrors('data.id');
    }

    public function test_data_id_must_be_a_string(): void
    {
        $this->patchJson('/api/test-route', [
            'data' => [
                'id' => 1,
                'type' => 'articles',
                'attributes' => ['name' => 'test'],
            ],
        ])->assertJsonApiValidationErrors('data.id');
    }

    public function test_only_accepts_valid_json_api_document()
    {
        $this->postJson('/api/test-route', [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'name' => 'test',
                ],
            ],
        ])->assertSuccessFul();

        $this->patchJson('/api/test-route', [
            'data' => [
                'id' => 'string',
                'type' => 'articles',
                'attributes' => [
                    'name' => 'test',
                ],
            ],
        ])->assertSuccessFul();
    }
}
