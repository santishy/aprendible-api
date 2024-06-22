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
        $response = $this->postJson(route('api.v1.articles.store'), [
            //"data" => [
            //  "type" => "articles",
            //"attributes" => [
            "title" => "Nuevo articulo",
            "slug" => "Nuevo-producto",
            "content" => "nuevo contenido"
            //],

            // ]
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
    public function test_title_is_required(): void
    {
        // $this->withoutExceptionHandling();
        $response = $this->postJson(route('api.v1.articles.store'), [
            // "data" => [
            //     "type" => "articles",
            //     "attributes" => [
            // "title" => "el titulo es requerido",
            "slug" => "Nuevo-producto",
            "content" => "nuevo contenido"
            // ],
            // "links" => [
            //     "self" => route('api.v1.articles.store')
            // ]
            //]
        ]);

        $response->assertJsonApiValidationErrors('title');
    }
    public function test_slug_is_required(): void
    {
        //$this->withoutExceptionHandling();
        $response = $this->postJson(route('api.v1.articles.store'), [
            // "data" => [
            //     "type" => "articles",
            //     "attributes" => [
            "title" => "Nuevo producto",
            "content" => "nuevo contenido"
            // ],
            // "links" => [
            //     "self" => route('api.v1.articles.store')
            // ]
            // ]
        ]);

        $response->assertJsonApiValidationErrors('slug');
    }
    public function test_content_is_required(): void
    {
        //$this->withoutExceptionHandling();
        $response = $this->postJson(route('api.v1.articles.store'), [

            "title" => "Nuevo producto",
            "slug" => "nuevo-producto"

        ]);

        $response->assertJsonApiValidationErrors('content');
    }

    public function test_title_must_be_at_least_4_characters()
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
            "title" => "123",
            "slug" => "nuevo-producto",
            "content" => "contenido nuevo",
        ]);

        $response->assertJsonApiValidationErrors('title');
    }
}
