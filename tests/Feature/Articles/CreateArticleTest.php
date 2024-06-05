<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_articles(): void
    {
        $this->withoutExceptionHandling();
        $response = $this->postJson(route('api.v1.articles.create'), [
            "data" => [
                "type" => "articles",
                "attributes" => [
                    "title" => "Nuevo articulo",
                    "slug" => "Nuevo-producto",
                    "content" => "nuevo contenido"
                ],
                "links" => [
                    "self" => route('api.v1.articles.create')
                ]
            ]
        ]);

        $response->assertCreated();
        $article = Article::first();
        $response->assertHeader('Location', route('api.v1.articles.show', $article));
        $response->assertExactJson([
            "data" => [
                "type" => "articles",
                "id" => (string) $article->getRouteKey(),
                "attributes" => [
                    "title" => "Nuevo articulo",
                    "slug" => "Nuevo-producto",
                    "content" => "nuevo contenido"
                ],
                "links" => [
                    "self" => route("api.v1.articles.show", $article)
                ]
            ]
        ]);
    }
}
