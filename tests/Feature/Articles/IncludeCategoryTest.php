<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IncludeCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_include_related_category_of_an_article(): void
    {
        $article = Article::factory()->create();
        $url = route('api.v1.articles.show', [
            'article' => $article,
            'include' => 'category',
        ]);

        $this->getJson($url)
            ->assertJson([
                'included' => [
                    [
                        'type' => 'categories',
                        'id' => $article->category->getRouteKey(),
                        'attributes' => [
                            'name' => $article->category->name,
                        ],
                    ],
                ],
            ]);
    }

    public function test_can_include_related_categories_of_multiple_articles(): void
    {
        $article = Article::factory()->create()->load('category');
        $article2 = Article::factory()->create()->load('category');

        $url = route('api.v1.articles.index', [
            'include' => 'category',
        ]);

        /*DB::listen(function ($query) {
            dump($query->sql);
        });*/

        $this->getJson($url)
            ->assertJson([
                'included' => [
                    [
                        'type' => 'categories',
                        'id' => $article->category->getRouteKey(),
                        'attributes' => [
                            'name' => $article->category->name,
                        ],
                    ],
                    [
                        'type' => 'categories',
                        'id' => $article2->category->getRouteKey(),
                        'attributes' => [
                            'name' => $article2->category->name,
                        ],
                    ],
                ],
            ]);
    }

    public function test_cannot_include_unknown_relationships(): void
    {
        $article = Article::factory()->create();
        $url = route('api.v1.articles.show', [
            'article' => $article,
            'include' => 'unknown,unknown2',
        ]);

        $this->getJson($url)
            ->assertJsonApiError(
                title: 'Bad request',
                detail: "The included relationship 'unknown' is not allowed in the 'articles' resource",
                status: '400'
            );

        $url = route('api.v1.articles.index', [
            'include' => 'unknown,unknown2',
        ]);

        $this->getJson($url)
            ->assertJsonApiError(
                title: 'Bad request',
                detail: "The included relationship 'unknown' is not allowed in the 'articles' resource",
                status: '400'
            );
    }
}
