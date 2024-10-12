<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_create_articles()
    {
        // $this->withoutExceptionHandling();
        $response = $this->postJson(route('api.v1.articles.store'));
        $response->assertJsonApiError(
            title: "Unauthenticated",
            detail: "This action requires authentication",
            status: "401",
        );
        $this->assertDatabaseCount('articles', 0);
    }
    public function test_can_create_articles(): void
    {

        $category = Category::factory()->create();
        $author = User::factory()->create();
        Sanctum::actingAs($author, ["article:create"]);
        $response = $this->postJson(route('api.v1.articles.store'), [
            //"data" => [
            //  "type" => "articles",
            //"attributes" => [
            "title" => "Nuevo articulo",
            "slug" => "Nuevo-producto",
            "content" => "nuevo contenido",
            "_relationships" => [
                "category" => $category,
                "author" => $author
            ]
            //],

            // ]
        ]);

        $response->assertCreated();
        $article = Article::first();
        $response->assertHeader('Location', route('api.v1.articles.show', $article));
        $response->assertJsonApiResource($article, [
            "title" => "Nuevo articulo",
            "slug" => "Nuevo-producto",
            "content" => "nuevo contenido"
        ]);

        $this->assertDatabaseHas('articles', [
            "title" => "Nuevo articulo",
            'category_id' => $category->id,
            'user_id' => $author->id
        ]);
    }
    public function test_title_is_required(): void
    {
        Sanctum::actingAs(User::factory()->create());

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
        Sanctum::actingAs(User::factory()->create());
        $response = $this->postJson(route('api.v1.articles.store'), [
            "slug" => "Nuevo-producto",
            "content" => "nuevo contenido",
            "title" => "title article"
        ]);

        $response->assertJsonApiValidationErrors('relationships.category');
    }
    public function test_category_must_be_exist_database(): void
    {
        Sanctum::actingAs(User::factory()->create());
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
        Sanctum::actingAs(User::factory()->create());
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
        Sanctum::actingAs(User::factory()->create());
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
        Sanctum::actingAs(User::factory()->create());
        $this->postJson(route('api.v1.articles.store'), [
            "title" => "new product",
            "slug" => "#423%",
            "content" => "new content"
        ])->assertJsonApiValidationErrors('slug');
    }

    public function test_slug_must_not_contain_underscores()
    {
        Sanctum::actingAs(User::factory()->create());
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
        Sanctum::actingAs(User::factory()->create());
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
        Sanctum::actingAs(User::factory()->create());
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
        Sanctum::actingAs(User::factory()->create());
        $response = $this->postJson(route('api.v1.articles.store'), [

            "title" => "Nuevo producto",
            "slug" => "nuevo-producto"

        ]);

        $response->assertJsonApiValidationErrors('content');
    }

    public function test_title_must_be_at_least_4_characters()
    {
        Sanctum::actingAs(User::factory()->create());
        $response = $this->postJson(route('api.v1.articles.store'), [
            "title" => "123",
            "slug" => "nuevo-producto",
            "content" => "contenido nuevo",
        ]);

        $response->assertJsonApiValidationErrors('title');
    }
}
