<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IncludeAuthorTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_include_related_author_of_an_article(): void
    {

        $article = Article::factory()->create();

        $url = route('api.v1.articles.show',[
            "article" => $article,
            "include" => "author"
        ]);

       
        $this->getJson($url)->assertJson([
            "included" => [
                [
                    "type" => "authors",
                    "id" => $article->author->getRouteKey(),
                    "attributes" => [
                        "name" => $article->author->name
                    ]
                ]
             ]
        ]);

    }

    public function test_can_include_related_authors_of_multiple_articles(){
        $article = Article::factory()->create()->load('author');
        $article2 = Article::factory()->create()->load('author');

        $url = route('api.v1.articles.index',[
            "include" => "author"
        ]);

        $response = $this->getJson($url);

        $response->assertJson([
            "included" => [
                [
                    "type" => "authors",
                    "id" => $article->author->getRouteKey(),
                    "attributes" => [
                        "name" => $article->author->name
                    ]
                ],
                [
                    "type" => "authors",
                    "id" => $article2->author->getRouteKey(),
                    "attributes" => [
                        "name" => $article2->author->name
                    ]
                ],
            ]
        ]);
    }
}
