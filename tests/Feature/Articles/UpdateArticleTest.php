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
            "slug" => $article->slug,
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
                    "slug" => $article->slug,
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
        $article = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            "slug" => "-producto",
            "content" => " contenido"
        ]);

        $response->assertJsonApiValidationErrors('title');
    }
    public function test_slug_is_required(): void
    {
        //$this->withoutExceptionHandling();
        $article = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            "title" => " producto",
            "content" => " contenido"
        ]);

        $response->assertJsonApiValidationErrors('slug');
    }

    public function test_slug_must_be_unique(): void
    {
        //$this->withoutExceptionHandling();
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update', $article1), [
            "title" => "Nuevo producto",
            "content" => "nuevo contenido",
            "slug" => $article2->slug,
        ]);

        $response->assertJsonApiValidationErrors('slug');
    }
    public function test_slug_must_only_contain_letters_numbers_and_dashes()
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update', $article), [
            "title" => "new product",
            "slug" => "#423%",
            "content" => "new content"
        ])->assertJsonApiValidationErrors('slug');
    }

    public function test_slug_must_not_contain_underscores()
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update', $article), [
            "title" => "new product",
            "slug" => "new_slug",
            "content" => "new content"
        ])
            ->assertSee(__('validation.no_underscores', ["attribute" => "slug"]))
            ->assertJsonApiValidationErrors('slug');
    }
    public function test_slug_must_not_start_with_dashes()
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update', $article), [
            "title" => "new product",
            "slug" => "-starts-with-dashes",
            "content" => "new content"
        ])
            ->assertSee(__('validation.no_starting_dashes', ["attribute" => "slug"]))
            ->assertJsonApiValidationErrors('slug');
    }

    public function test_slug_must_not_end_with_dashes()
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update', $article), [
            "title" => "new product",
            "slug" => "end-with-dashes-",
            "content" => "new content"
        ])
            ->assertSee(__('validation.no_ending_dashes', ["attribute" => "slug"]))
            ->assertJsonApiValidationErrors('slug');
    }
    public function test_content_is_required(): void
    {
        //$this->withoutExceptionHandling();
        $article = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update', $article), [

            "title" => " producto",
            "slug" => "-producto"

        ]);

        $response->assertJsonApiValidationErrors('content');
    }

    public function test_title_must_be_at_least_4_characters()
    {

        $article = Article::factory()->create();
        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            "title" => "123",
            "slug" => "-producto",
            "content" => "contenido ",
        ]);

        $response->assertJsonApiValidationErrors('title');
    }
}
