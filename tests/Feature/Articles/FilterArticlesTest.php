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
        $url  = route('api.v1.articles.index',[
            "filter" => [
                "title" => "laravel"
            ]
        ]);

        $response = $this->getJson($url);
        $response->assertJsonCount(1,'data');
        $response->assertSee("Aprende laravel desde cero.");
        $response->assertDontSee("Otro articulo");
    }

    public function test_can_filter_articles_by_content(): void
    {
        $article = Article::factory()->create([
            "content" => "Aprende laravel desde cero."
        ]);
        $article2 = Article::factory()->create([
            "content" => "Otro articulo"
        ]);
        // articles?filter[content]=value
        $url  = route('api.v1.articles.index',[
            "filter" => [
                "content" => "laravel"
            ]
        ]);

        $response = $this->getJson($url);
        $response->assertJsonCount(1,'data');
        $response->assertSee("Aprende laravel desde cero.");
        $response->assertDontSee("Otro articulo");
    }
}
