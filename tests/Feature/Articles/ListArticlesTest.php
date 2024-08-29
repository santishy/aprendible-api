<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
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
        ])->assertJsonApiRelationshipLinks($article, ['category','author']);
    }
    public function test_can_fetch_all_articles()
    {
        //$this->withoutExceptionHandling();
        $articles = Article::factory()->count(3)->create();
        $url = route('api.v1.articles.index');
        $response = $this->getJson($url);
        $response->assertJsonApiResourceCollection($articles, [
            "title",
            "slug",
            "content",
        ]);
    }
}
