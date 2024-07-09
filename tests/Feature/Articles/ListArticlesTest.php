<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_a_single_article()
    {
        //$this->withoutExceptionHandling();
        $article = Article::factory()->create();

        $response = $this->getJson('/api/v1/articles/' . $article->getRouteKey());

        $response->assertJsonApiResource($article, [
            "title" => $article->title,
            "slug" => $article->slug,
            "content" => $article->content
        ]);
        // $response->assertExactJson([
        //     "data" => [
        //         "type" => "articles",
        //         "id" => (string) $article->getRouteKey(),
        //         "attributes" => [
        //             "title" => $article->title,
        //             "slug" => $article->slug,
        //             "content" => $article->content
        //         ],
        //         "links" => [
        //             "self" => route('api.v1.articles.show', $article)
        //         ]
        //     ]
        // ]);
    }
    public function test_can_fetch_all_articles()
    {
        $this->withoutExceptionHandling();
        $articles = Article::factory()->count(3)->create();
        $response = $this->getJson(route('api.v1.articles.index'));

        $response->assertJsonApiResourceCollection($articles, [
            "title",
            "slug",
            "content",
        ]);
    }
}
