<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\JsonApi\Http\Middleware\ValidateJsonApiHeaders;

class ValidateJsonApiHeadersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Route::any('test_route', fn () => 'ok')
            ->middleware(ValidateJsonApiHeaders::class);
    }

    public function test_accept_header_must_be_present_in_all_requests(): void
    {
        //$this->withoutExceptionHandling();

        $this->get('test_route')
            ->assertStatus(406);

        $this->get('test_route', ['accept' => 'application/vnd.api+json'])
            ->assertSuccessful();
    }

    public function test_content_type_header_must_be_present_in_all_posts_requests(): void
    {
        //$this->withoutExceptionHandling();

        $this->post(
            'test_route',
            [],
            ['accept' => 'application/vnd.api+json']
        )->assertStatus(415);

        $this->post(
            'test_route',
            [],
            [
                'accept' => 'application/vnd.api+json',
                'content-type' => 'application/vnd.api+json',
            ]
        )->assertSuccessful();
    }

    public function test_content_type_header_must_be_present_in_all_patch_requests(): void
    {
        //$this->withoutExceptionHandling();

        $this->patch(
            'test_route',
            [],
            ['accept' => 'application/vnd.api+json']
        )->assertStatus(415);

        $this->patch(
            'test_route',
            [],
            [
                'accept' => 'application/vnd.api+json',
                'content-type' => 'application/vnd.api+json',
            ]
        )->assertSuccessful();
    }

    public function test_content_type_header_must_be_present_in_responses()
    {
        //$this->withoutExceptionHandling();
        $this->get('test_route', ['accept' => 'application/vnd.api+json'])
            ->assertHeader('content-type', 'application/vnd.api+json');

        $this->post(
            'test_route',
            [],
            [
                'accept' => 'application/vnd.api+json',
                'content-type' => 'application/vnd.api+json',
            ]
        )
            ->assertHeader('content-type', 'application/vnd.api+json');

        $this->patch(
            'test_route',
            [],
            [
                'accept' => 'application/vnd.api+json',
                'content-type' => 'application/vnd.api+json',
            ]
        )
            ->assertHeader('content-type', 'application/vnd.api+json');
    }

    public function content_type_header_must_not_be_present_in_empty_responses()
    {
        Route::any('empty_response', function () {
            return response()->noContent();
        })->middleware(ValidateJsonApiHeaders::class);

        $this->get('empty_response', ['accept' => 'application/vnd.api+json'])
            ->assertHeaderMissing('content-type');

        $this->patch(
            'empty_response',
            [],
            [
                'accept' => 'application/vnd.api+json',
                'content-type' => 'application/vnd.api+json',
            ]

        )->assertHeaderMissing('content-type');

        $this->post(
            'empty_response',
            [],
            [
                'accept' => 'application/vnd.api+json',
                'content-type' => 'application/vnd.api+json',
            ]

        )->assertHeaderMissing('content-type');

        $this->delete(
            'empty_response',
            [],
            [
                'accept' => 'application/vnd.api+json',
                'content-type' => 'application/vnd.api+json',
            ]

        )->assertHeaderMissing('content-type');
    }
}
