<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_update_comments(): void
    {
        $comment = Comment::factory()->create();
        $this->patchJson(route('api.v1.comments.update', $comment))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication',
                status: '401'
            );
    }

    public function test_can_update_owned_comments()
    {
        $comment = Comment::factory()->create();

        Sanctum::actingAs($comment->author, ['comment:update']);

        $response = $this->patchJson(route('api.v1.comments.update', $comment), [
            'body' => 'Updated content',
        ])->assertOk();

        $response->assertJsonApiResource($comment, [
            'body' => 'Updated content',
        ]);
    }

    public function test_can_update_owned_comments_with_relationships()
    {

        $article = Article::factory()->create();
        $comment = Comment::factory()->create();

        Sanctum::actingAs($comment->author, ['comment:update']);

        $response = $this->patchJson(route('api.v1.comments.update', $comment), [
            'body' => 'Updated content',
            '_relationships' => [
                // 'article' => $article,
                //  'author' => $comment->author,
            ],
        ]);

        $response->assertJsonApiResource($comment, [
            'body' => 'Updated content',
        ]);

        // $this->assertTrue($article->is($comment->fresh()->article));

        $this->assertDatabaseHas('comments', [
            'body' => 'Updated content',
            'user_id' => $comment->author->id,
            'article_id' => $comment->article->id,
        ]);
    }

    public function test_cannot_update_comments_owned_by_other_users()
    {

        $comment = Comment::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->patchJson(route('api.v1.comments.update', $comment), [
            'body' => 'Updated content',
        ])->assertForbidden();
    }
}
