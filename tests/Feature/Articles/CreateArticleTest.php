<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_articles(): void
    {
        $category = Category::factory()->create();
        $response = $this->postJson(route('api.v1.articles.store'), [
            //"data" => [
            //  "type" => "articles",
            //"attributes" => [
            "title" => "Nuevo articulo",
            "slug" => "Nuevo-producto",
            "content" => "nuevo contenido",
            "_relationships" => ["category" => $category]
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
    public function test_category_relationship_is_required(): void
    {

        $response = $this->postJson(route('api.v1.articles.store'), [
            "slug" => "Nuevo-producto",
            "content" => "nuevo contenido",
            "title" => "title article"
        ]);

        $response->assertJsonApiValidationErrors('relationships.category');
    }
    public function test_category_must_be_exist_database(): void
    {

        $response = $this->postJson(route('api.v1.articles.store'), [
            "slug" => "Nuevo-producto",
            "content" => "nuevo contenido",
            "title" => "title article",
            "_relationships" => ["category" => Category::factory()->make()]
        ]);

        $response->assertJsonApiValidationErrors('relationships.category');
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
    public function test_slug_must_be_unique(): void
    {
        //$this->withoutExceptionHandling();
        $article = Article::factory()->create();
        $response = $this->postJson(route('api.v1.articles.store'), [
            "title" => "Nuevo producto",
            "content" => "nuevo contenido",
            "slug" => $article->slug,
        ]);

        $response->assertJsonApiValidationErrors('slug');
    }

    public function test_slug_must_only_contain_letters_numbers_and_dashes()
    {
        $this->postJson(route('api.v1.articles.store'), [
            "title" => "new product",
            "slug" => "#423%",
            "content" => "new content"
        ])->assertJsonApiValidationErrors('slug');
    }

    public function test_slug_must_not_contain_underscores()
    {
        $this->postJson(route('api.v1.articles.store'), [
            "title" => "new product",
            "slug" => "new_slug",
            "content" => "new content"
        ])
            ->assertSee(__('validation.no_underscores', ["attribute" => "slug"]))
            ->assertJsonApiValidationErrors('slug');
    }
    public function test_slug_must_not_start_with_dashes()
    {
        $this->postJson(route('api.v1.articles.store'), [
            "title" => "new product",
            "slug" => "-starts-with-dashes",
            "content" => "new content"
        ])
            ->assertSee(__('validation.no_starting_dashes', ["attribute" => "slug"]))
            ->assertJsonApiValidationErrors('slug');
    }

    public function test_slug_must_not_end_with_dashes()
    {
        $this->postJson(route('api.v1.articles.store'), [
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
