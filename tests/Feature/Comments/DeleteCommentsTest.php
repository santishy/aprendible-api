<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\User;
use App\Models\Comment;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_delete_comments(): void
    {
        $comment = Comment::factory()->create();

        $this->deleteJson(route('api.v1.comments.destroy', $comment))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication',
                status: '401'
            );
    }

    public function test_can_delete_owned_comments(): void
    {
        $comment = Comment::factory()->create();
        Sanctum::actingAs($comment->author, ['comment:delete']);

        $response = $this->deleteJson(route('api.v1.comments.destroy', $comment))->assertNoContent();

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_cannot_delete_comments_owned_by_other_users(): void
    {
        $comment = Comment::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->deleteJson(route('api.v1.comments.destroy', $comment))
            ->assertForbidden();

        $this->assertDatabaseCount('comments', 1);
    }
}
