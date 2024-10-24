<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_the_associated_article_identifier(): void
    {
        $comment = Comment::factory()->create();
        $url = route('api.v1.comments.relationships.article', $comment);

        $this->getJson($url)
            ->assertExactJson([
                'data' => [
                    'type' => 'articles',
                    'id' => $comment->article->getRouteKey(),
                ],
            ]);
    }

    public function test_can_fetch_associated_resource_article(): void
    {
        $comment = Comment::factory()->create();
        $url = route('api.v1.comments.article', $comment);

        $this->getJson($url)->assertJson([
            'data' => [
                'type' => 'articles',
                'id' => $comment->article->getRouteKey(),
                'attributes' => [
                    'title' => $comment->article->title,
                ],
            ],
        ]);
    }

    public function test_can_update_the_associated_article(): void
    {
        $comment = Comment::factory()->create();
        $article = Article::factory()->create();
        $url = route('api.v1.comments.relationships.article', $comment);

        $this->patchJson($url, [
            'data' => [
                'type' => 'articles',
                'id' => $article->getRouteKey(),
            ],
        ])
            ->assertExactJson([
                'data' => [
                    'type' => 'articles',
                    'id' => $article->getRouteKey(),
                ],
            ]);

        $this->assertDatabaseHas('comments', [
            'body' => $comment->body,
            'article_id' => $article->id,
        ]);
    }

    public function test_article_must_exist_in_database(): void
    {
        $comment = Comment::factory()->create();
        $url = route('api.v1.comments.relationships.article', $comment);
        $this->patchJson($url, [
            'data' => [
                'type' => 'articles',
                'id' => 'non-existing'
            ]
        ])->assertJsonApiValidationErrors('data.id');
        $this->assertDatabaseHas('comments', [
            "article_id" => $comment->article->id,
            "body" => $comment->body
        ]);
    }
}
