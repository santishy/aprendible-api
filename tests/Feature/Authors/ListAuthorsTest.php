<?php

namespace Tests\Feature\Authors;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class ListAuthorsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_a_single_author(): void
    {
        $author = User::factory()->create();
        $url = route('api.v1.authors.show', $author);
        $response = $this->getJson($url);

        $response->assertJsonApiResource($author, [
            "name" => $author->name
        ]);

        $this->assertTrue(
            Str::isUuid(($response->json('data.id'))),
            'The authors "id" must be UUID.'
        );
    }

    public function test_can_fetch_all_authors(): void
    {
        $authors = User::factory()->count(5)->create();

        $this->getJson(route('api.v1.authors.index'))
            ->assertJsonApiResourceCollection($authors, ["name"]);
    }
}
