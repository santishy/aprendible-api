<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IncludeCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_include_related_comments_of_an_article(): void
    {
        $article = Article::factory()->hasComments(2)->create();

        $url = route('api.v1.articles.show', [
            'article' => $article,
            'include' => 'comments'
        ]);

        $response = $this->getJson($url);

        $response->assertJsonCount(2, 'included');

        $article->comments->map(fn($comment) => $response->assertJsonFragment([
            'type' => 'comments',
            'id' => (string) $comment->getRouteKey(),
            'attributes' => [
                'body' => $comment->body
            ]
        ]));
    }

    public function test_can_include_related_comments_of_multiple_articles()
    {
        $article = Article::factory()->hasComments(2)->create();
        $article2 = Article::factory()->hasComments(2)->create();

        $url = route('api.v1.articles.index', [
            'include' => 'comments'
        ]);

        $response = $this->getJson($url);

        $response->assertJsonCount(4, 'included');
    }
}
