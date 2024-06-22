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
        //$this->withoutExceptionHandling();

        // $response = $this->postJson(route('api.v1.articles.store'), [
        //     "title" => "Nuevo articulo",
        //     "slug" => "Nuevo-producto",
        //     "content" => "nuevo contenido"
        // ]);

        // $response->dump();

        $article = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update', $article));
        $response->assertOk()->dump();
        $response->assertHeader('Location', route('api.v1.articles.show', $article));
        $response->assertExactJson([
            "id" => (string) $article->getRouteKey(),
            "type" => "articles",
            "attributes" => [
                "title" => $article->title,
                "slug" => $article->slug,
                "content" => $article->content,
            ],
            "links" => [
                "self" => route('api.v1.articles.show', $article)
            ]
        ]);
    }
}
