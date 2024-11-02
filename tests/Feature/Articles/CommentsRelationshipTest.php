<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentsRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_the_associated_comments_identifier(): void
    {

        $article = Article::factory()->hasComments(2)->create();
        $url = route('api.v1.articles.relationships.comments', $article);
        $response = $this->getJson($url);

        $response->assertJsonCount(2, 'data');

        $article->comments->map(fn ($comment) => $response->assertJsonFragment([
            'type' => 'comments',
            'id' => (string) $comment->getRouteKey(),
        ]));
    }

    public function test_it_returns_an_empty_array_when_there_are_no_associated_comments()
    {
        $this->withoutExceptionHandling();

        $article = Article::factory()->create();

        $url = route('api.v1.articles.relationships.comments', $article);
        $response = $this->getJson($url);

        $response->assertJsonCount(0, 'data');

        $response->assertExactJson([
            'data' => [],
        ]);
    }

    public function test_can_fetch_the_associated_comments_resource()
    {
        $this->withoutExceptionHandling();
        $article = Article::factory()->hasComments(2)->create();

        $url = route('api.v1.articles.comments', $article);

        $response = $this->getJson($url);

        $response->assertJson([
            'data' => [
                [
                    'id' => (string) $article->comments[0]->getRouteKey(),
                    'type' => $article->comments[0]->getResourceType(),
                    'attributes' => [
                        'body' => $article->comments[0]->body,
                    ],
                ],
                [
                    'id' => (string) $article->comments[1]->getRouteKey(),
                    'type' => $article->comments[1]->getResourceType(),
                    'attributes' => [
                        'body' => $article->comments[1]->body,
                    ],
                ],
            ],
        ]);
    }

    public function test_can_update_the_associated_comments()
    {

        $article = Article::factory()->create();

        $comments = Comment::factory()->count(2)->create();

        $url = route('api.v1.articles.relationships.comments', $article);

        $response = $this->patchJson($url, [
            'data' => $comments->map(fn ($comment) => [
                'id' => $comment->getRouteKey(),
                'type' => 'comments',
            ]),
        ])->dump();
        $response->assertJsonCount(2, 'data');
        $comments->map(fn ($comment) => $response->assertJsonFragment([
            'id' => (string) $comment->getRouteKey(),
            'type' => $comment->getResourceType(),
        ]));

        $comments->map(fn ($comment) => $this->assertDatabaseHas('comments', [
            'body' => $comment->body,
            'article_id' => $article->id,
        ]));
    }

    public function test_comments_must_exist_in_database()
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.relationships.comments', $article);

        $this->patchJson($url, [
            'data' => [
                [
                    'id' => 'doesnt-existing',
                    'type' => 'comments',
                ],
            ],
        ])->assertJsonApiValidationErrors('data.0.id');

        $this->assertDatabaseEmpty('comments');
    }
}
