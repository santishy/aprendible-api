<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_article(): void
    {

        $article = Article::factory()->create();

        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            "title" => "update title",
            "slug" => "update-slug",
            "content" => "update-content"
        ]);

        $response->assertOk();

        $response->assertHeader('Location', route('api.v1.articles.show', $article));

        $response->assertExactJson([
            "data" => [
                "type" => "articles",
                "id" => (string) $article->getRouteKey(),
                "attributes" => [
                    "title" => "update title",
                    "slug" => "update-slug",
                    "content" => "update-content"
                ],
                "links" => [
                    "self" => route("api.v1.articles.show", $article)
                ]
            ]
        ]);
    }
    public function test_title_is_required(): void
    {

        $response = $this->patchJson(route('api.v1.articles.update'), [
            "slug" => "Nuevo-producto",
            "content" => "nuevo contenido"
        ]);

        $response->assertJsonApiValidationErrors('title');
    }
    public function test_slug_is_required(): void
    {
        //$this->withoutExceptionHandling();
        $response = $this->patchJson(route('api.v1.articles.update'), [
            "title" => "Nuevo producto",
            "content" => "nuevo contenido"
        ]);

        $response->assertJsonApiValidationErrors('slug');
    }
    public function test_content_is_required(): void
    {
        //$this->withoutExceptionHandling();
        $response = $this->patchJson(route('api.v1.articles.update'), [

            "title" => "Nuevo producto",
            "slug" => "nuevo-producto"

        ]);

        $response->assertJsonApiValidationErrors('content');
    }

    public function test_title_must_be_at_least_4_characters()
    {
        $response = $this->patchJson(route('api.v1.articles.update'), [
            "title" => "123",
            "slug" => "nuevo-producto",
            "content" => "contenido nuevo",
        ]);

        $response->assertJsonApiValidationErrors('title');
    }
}
