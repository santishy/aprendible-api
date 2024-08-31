<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthorRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_the_associated_author_identifier(): void
    {
        //$this->withoutExceptionHandling();
        $article = Article::factory()->create();

        $url = route('api.v1.articles.relationships.author', $article);

        $response = $this->getJson($url)->dump();

        $response->assertExactJson([
            "data" => [
                "type" => "authors",
                "id" => $article->author->getRouteKey()
            ]
        ]);
    }

    public function test_can_fetch_the_associated_author_resource(): void
    {
        $article = Article::factory()->create()->load('author');

        $url = route('api.v1.articles.author', $article);

        $this->getJson($url)
            ->assertJson([
                "data" => [
                    "type" => "authors",
                    "id" => $article->author->getRouteKey(),
                    "attributes" => [
                        "name" => $article->author->name
                    ]
                ]
            ]);
    }
}
