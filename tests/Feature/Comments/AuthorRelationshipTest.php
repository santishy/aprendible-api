<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_the_associated_author_identifier(): void
    {
        $comment = Comment::factory()->create();
        $url = route('api.v1.comments.relationships.author', $comment);

        $this->getJson($url)->assertExactJson([
            'data' => [
                'id' => $comment->author->getRouteKey(),
                'type' => 'authors',
            ],
        ]);
    }

    public function test_can_fetch_the_associated_author_resource(): void
    {

        $comment = Comment::factory()->create();
        $url = route('api.v1.comments.author', $comment);

        $this->getJson($url)->assertJson([
            'data' => [
                'type' => 'authors',
                'id' => $comment->author->getRouteKey(),
                'attributes' => [
                    'name' => $comment->author->name,
                ],
            ],
        ]);
    }

    public function test_can_update_associated_author()
    {

        $comment = Comment::factory()->create();
        $author = User::factory()->create();

        $this->patchJson(route('api.v1.comments.relationships.author', $comment), [
            'data' => [
                'id' => $author->getRouteKey(),
                'type' => 'authors',
            ],
        ])->assertExactJson([
            'data' => [
                'type' => 'authors',
                'id' => $author->id,
            ],
        ]);

        $this->assertDatabaseHas('comments', [
            'body' => $comment->body,
            'user_id' => $author->getRouteKey(),
        ]);
    }

    public function test_author_must_exist_in_database()
    {
        $comment = Comment::factory()->create();

        $this->patchJson(route('api.v1.comments.relationships.author', $comment), [
            'data' => [
                'id' => 'non-existing',
                'type' => 'authors',
            ],
        ])->assertJsonApiValidationErrors('data.id');

        $this->assertDatabaseHas('comments', [
            'body' => $comment->body,
            'user_id' => $comment->user_id,
        ]);
    }
}
