<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_a_single_article()
    {
        //$this->withoutExceptionHandling();
        $article = Article::factory()->create();

        $response = $this->getJson('/api/v1/articles/'.$article->getRouteKey());

        $response->assertJsonApiResource($article, [
            'title' => $article->title,
            'slug' => $article->slug,
            'content' => $article->content,
        ])->assertJsonApiRelationshipLinks($article, ['category', 'author']);
    }

    public function test_can_fetch_all_articles()
    {
        //$this->withoutExceptionHandling();
        $articles = Article::factory()->count(3)->create();
        $url = route('api.v1.articles.index');
        $response = $this->getJson($url);
        $response->assertJsonApiResourceCollection($articles, [
            'title',
            'slug',
            'content',
        ]);
    }

    public function test_it_returns_a_json_api_object_when_an_article_is_not_found()
    {
        $url = route('api.v1.articles.show', 'non-existing');
        $this->getJson($url)->assertJsonApiError(
            detail: "No records found with the id 'non-existing' in the 'articles' resource",
            status: '404',
            title: 'Not found'
        );
    }
}
