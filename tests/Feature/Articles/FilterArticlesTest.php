<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FilterArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_filter_articles_by_title(): void
    {

        Article::factory()->create([
            'title' => 'Aprende Laravel Desde Cero',
        ]);

        Article::factory()->create([
            'title' => 'Other Article',
        ]);

        // articles?filter[title]=Laravel
        $url = route('api.v1.articles.index', [
            'filter' => [
                'title' => 'Laravel',
            ],
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprende Laravel Desde Cero')
            ->assertDontSee('Other Article');
    }

    public function test_can_filter_articles_by_content(): void
    {
        $this->withoutExceptionHandling();
        $article = Article::factory()->create([
            'content' => 'Aprende laravel desde cero.',
        ]);
        $article2 = Article::factory()->create([
            'content' => 'Otro articulo',
        ]);
        // articles?filter[content]=value
        $url = route('api.v1.articles.index', [
            'filter' => [
                'content' => 'laravel',
            ],
        ]);

        $response = $this->getJson($url);
        $response->assertJsonCount(1, 'data');
        $response->assertSee('Aprende laravel desde cero.');
        $response->assertDontSee('Otro articulo');
    }

    public function test_can_filter_articles_by_category(): void
    {
        $this->withoutExceptionHandling();
        Category::factory()->hasArticles(2)->create();
        $cat1 = Category::factory()->hasArticles(3)->create(['slug' => 'cat-1']);
        $cat2 = Category::factory()->hasArticles()->create(['slug' => 'cat-2']);

        //articles?filter[categories]=cat-1
        $url = route('api.v1.articles.index', [
            'filter' => [
                'categories' => 'cat-1,cat-2',
            ],
        ]);
        $this->getJson($url)
            ->assertJsonCount(4, 'data')
            ->assertSee($cat1->articles[0]->title)
            ->assertSee($cat1->articles[1]->title)
            ->assertSee($cat1->articles[2]->title)
            ->assertSee($cat2->articles[0]->title);
    }

    public function test_can_filter_articles_by_year(): void
    {

        $this->withoutExceptionHandling();
        $article = Article::factory()->create([
            'title' => 'article from 2021',
            'created_at' => now()->year(2021),
        ]);
        $article2 = Article::factory()->create([
            'title' => 'article from 2022',
            'created_at' => now()->year(2022),
        ]);
        // articles?filter[content]=value
        $url = route('api.v1.articles.index', [
            'filter' => [
                'year' => 2021,
            ],
        ]);

        $response = $this->getJson($url);
        $response->assertJsonCount(1, 'data');
        $response->assertSee('article from 2021');
        $response->assertDontSee('article from 2022');
    }

    public function test_can_filter_articles_by_month(): void
    {

        $article = Article::factory()->create([
            'title' => 'article from month 1',
            'created_at' => now()->month(1),
        ]);
        $article = Article::factory()->create([
            'title' => 'article from month 3',
            'created_at' => now()->month(3),
        ]);
        $article = Article::factory()->create([
            'title' => 'another article from month 3',
            'created_at' => now()->month(3),
        ]);

        // articles?filter[month]=value
        $url = route('api.v1.articles.index', [
            'filter' => [
                'month' => 3,
            ],
        ]);

        $response = $this->getJson($url);
        $response->assertJsonCount(2, 'data');
        $response->assertSee('article from month 3');
        $response->assertSee('another article from month 3');
        $response->assertDontSee('article from month 1');
    }

    public function test_can_filter_articles_by_unknown_filters(): void
    {
        Article::factory()->count(2)->create();
        // articles?filter[unknown]=filter
        $url = route('api.v1.articles.index', [
            'filter' => [
                'unknown' => 'filter',
            ],
        ]);

        $this->getJson($url)
            ->assertJsonApiError(
                detail: "The filter 'unknown' is not allowed in the 'articles' resource",
                status: '400',
                title: 'Bad Request'
            );
    }
}
