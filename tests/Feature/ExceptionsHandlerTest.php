<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExceptionsHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_json_api_errors_are_only_shown_to_requests_with_the_prefix_api()
    {
        $this->getJson('api/route')->assertJsonApiError(
            detail: 'The route api/route could not be found.',
            title: 'Not found',
            status: '404'
        );
    }
}
