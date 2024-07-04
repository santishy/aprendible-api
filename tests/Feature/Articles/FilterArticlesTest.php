<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FilterArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_filter_articles_by_title(): void
    {

        $article = Article::factory()->create([
            "title" => "Aprende laravel desde cero."
        ]);
        $article2 = Article::factory()->create([
            "title" => "Otro articulo"
        ]);
        // articles?filter[title]=value
        $url  = route('api.v1.articles.index', [
            "filter" => [
                "title" => "laravel"
            ]
        ]);

        $response = $this->getJson($url);
        $response->assertJsonCount(1, 'data');
        $response->assertSee("Aprende laravel desde cero.");
        $response->assertDontSee("Otro articulo");
    }

    public function test_can_filter_articles_by_content(): void
    {
        $this->withoutExceptionHandling();
        $article = Article::factory()->create([
            "content" => "Aprende laravel desde cero."
        ]);
        $article2 = Article::factory()->create([
            "content" => "Otro articulo"
        ]);
        // articles?filter[content]=value
        $url  = route('api.v1.articles.index', [
            "filter" => [
                "content" => "laravel"
            ]
        ]);

        $response = $this->getJson($url);
        $response->assertJsonCount(1, 'data');
        $response->assertSee("Aprende laravel desde cero.");
        $response->assertDontSee("Otro articulo");
    }
    public function test_can_filter_articles_by_year(): void
    {

        $this->withoutExceptionHandling();
        $article = Article::factory()->create([
            "title" => "article from 2021",
            "created_at" => now()->year(2021)
        ]);
        $article2 = Article::factory()->create([
            "title" => "article from 2022",
            "created_at" => now()->year(2022)
        ]);
        // articles?filter[content]=value
        $url  = route('api.v1.articles.index', [
            "filter" => [
                "year" => 2021
            ]
        ]);

        $response = $this->getJson($url);
        $response->assertJsonCount(1, 'data');
        $response->assertSee("article from 2021");
        $response->assertDontSee("article from 2022");
    }
    public function test_can_filter_articles_by_month(): void
    {

        $article = Article::factory()->create([
            "title" => "article from month 1",
            "created_at" => now()->month(1)
        ]);
        $article = Article::factory()->create([
            "title" => "article from month 3",
            "created_at" => now()->month(3)
        ]);
        $article = Article::factory()->create([
            "title" => "another article from month 3",
            "created_at" => now()->month(3)
        ]);

        // articles?filter[month]=value
        $url  = route('api.v1.articles.index', [
            "filter" => [
                "month" => 3
            ]
        ]);

        $response = $this->getJson($url);
        $response->assertJsonCount(2, 'data');
        $response->assertSee("article from month 3");
        $response->assertSee("another article from month 3");
        $response->assertDontSee("article from month 1");
    }
    public function test_can_filter_articles_by_unknown_filters(): void
    {
        Article::factory()->count(2)->create();
        // articles?filter[unknown]=filter
        $url  = route('api.v1.articles.index', [
            "filter" => [
                "unknown" => "filter"
            ]
        ]);

        $this->getJson($url)
            ->assertStatus(400);
    }
}
