<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_a_single_comment(): void
    {
        $comment = Comment::factory()->create();

        $response = $this->getJson(route('api.v1.comments.show', $comment));

        $response->assertJsonApiResource($comment, ['body' => $comment->body]);

        $response->assertJsonApiRelationshipLinks($comment, ['article', 'author']);
    }

    public function test_can_fetch_all_comments(): void
    {
        $comments = Comment::factory()->count(3)->create();
        $response = $this->getJson(route('api.v1.comments.index'));
        $response->assertJsonApiResourceCollection($comments, ['body']);
    }

    public function test_it_returns_a_json_api_object_when_an_comment_is_not_found()
    {
        $url = route('api.v1.comments.show', 'non-existing');
        $this->getJson($url)->assertJsonApiError(
            detail: "No records found with the id 'non-existing' in the 'comments' resource",
            status: '404',
            title: 'Not found'
        );
    }
}
