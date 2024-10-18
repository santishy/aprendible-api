<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_the_associated_category_identifier(): void
    {
        //$this->withoutExceptionHandling();
        $article = Article::factory()->create();

        $url = route('api.v1.articles.relationships.category', $article);

        $response = $this->getJson($url);

        $response->assertExactJson([
            'data' => [
                'type' => 'categories',
                'id' => $article->category->getRouteKey(),
            ],
        ]);
    }

    public function test_can_fetch_the_associated_category_resource(): void
    {
        $article = Article::factory()->create()->load('category');

        $url = route('api.v1.articles.category', $article);

        $this->getJson($url)
            ->assertJson([
                'data' => [
                    'type' => 'categories',
                    'id' => $article->category->getRouteKey(),
                    'attributes' => [
                        'name' => $article->category->name,
                    ],
                ],
            ]);
    }

    public function test_can_update_the_associated_category()
    {
        $article = Article::factory()->create();
        $category = Category::factory()->create();
        $url = route('api.v1.articles.relationships.category', $article);
        //$this->withoutJsonApiDocumentFormatting(); se quito por que ... se cambio el formateo de peticiones adeheridos a la especificiacion json:api .. ya que esto si debe adeherise entonces vericicamos si la propiedad o mas bien el campo "data" viene por defecto no dar formato
        $response = $this->patchJson($url, [
            'data' => [
                'type' => 'categories',
                'id' => $category->getRouteKey(),
            ],
        ]);

        $response->assertExactJson([
            'data' => [
                'type' => 'categories',
                'id' => $category->getRouteKey(),
            ],
        ]);

        $this->assertDatabaseHas('articles', [
            'title' => $article->title,
            'category_id' => $category->id,
        ]);
    }

    public function test_category_must_exist_database()
    {
        $article = Article::factory()->create();
        $url = route('api.v1.articles.relationships.category', $article);

        // arriba en otro test explico el por que se comentariza esta linea $this->withoutJsonApiDocumentFormatting();

        $this->patchJson($url, [
            'data' => [
                'type' => 'categories',
                'id' => 'non-existing',
            ],
        ])->assertJsonApiValidationErrors('data.id');

        $this->assertDatabaseHas('articles', [
            'title' => $article->title,
            'category_id' => $article->category_id,
        ]);
    }
}
