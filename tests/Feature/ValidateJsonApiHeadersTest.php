<?php

namespace Tests\Feature;

use App\Http\Middleware\ValidateJsonApiHeaders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ValidateJsonApiHeadersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Route::any('test_route', fn () => "ok")
            ->middleware(ValidateJsonApiHeaders::class);
    }
    public function test_accept_header_must_be_present_in_all_requests(): void
    {
        //$this->withoutExceptionHandling();

        $this->getJson('test_route')->assertStatus(406);

        $this->getJson('test_route', ["accept" => "application/vnd.api+json"])
            ->assertSuccessful();
    }
    public function test_content_type_header_must_be_present_in_all_posts_requests(): void
    {
        //$this->withoutExceptionHandling();

        $this->postJson(
            'test_route',
            [],
            ["accept" => "application/vnd.api+json"]
        )->assertStatus(415);

        $this->postJson(
            'test_route',
            [],
            [
                "accept" => "application/vnd.api+json",
                "content-type" => "application/vnd.api+json"
            ]
        )->assertSuccessful();
    }
}
