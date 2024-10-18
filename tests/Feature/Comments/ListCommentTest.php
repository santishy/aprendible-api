<?php

namespace Tests\Feature\Comments;

use App\Models\Api\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_a_single_comment(): void
    {
        $comment = Comment::factory()->create();

        $response = $this->getJson(route('api.v1.comments', $comment));

        $response->assertJsonApiResource($comment, ['body']);

        //$response->assertJsonApiRelationshipLinks($comment,[]);
    }
}
