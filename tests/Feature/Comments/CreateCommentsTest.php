<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_create_comments(): void
    {
        $this->postJson((route('api.v1.comments.store')))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication',
                status: '401'
            );

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_can_create_comments(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();
        Sanctum::actingAs($user);
        $response = $this->postJson(route('api.v1.comments.store'), [
            'body' => $commentBody = 'Comment body',
            '_relationships' => [
                'author' => $user,
                'article' => $article,
            ],
        ])
            ->assertCreated();

        $this->assertDatabaseCount('comments', 1);
        $comment = Comment::first();
        $response->assertJsonApiResource($comment, [
            'body' => $comment->body,
        ]);
        $this->assertDatabaseHas('comments', [
            'body' => $commentBody,
            'user_id' => $user->id,
            'article_id' => $article->id,
        ]);
    }

    public function test_body_is_required(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.comments.store'), [
            'body' => null,
        ])->assertJsonApiValidationErrors('body');
    }

    public function test_article_relationship_is_required(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(
            route('api.v1.comments.store'),
            [
                'body' => 'Comment body',
            ]
        )->assertJsonApiValidationErrors('relationships.article');
    }

    public function test_article_must_exist_in_database()
    {

        Sanctum::actingAs(User::factory()->create());

        $this->postJson(
            route('api.v1.comments.store'),
            [
                'body' => 'Comment body',
                '_relationships' => [
                    'article' => Article::factory()->make(),
                ],
            ]
        )->assertJsonApiValidationErrors('relationships.article');
    }

    public function test_author_relationship_is_required(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(
            route('api.v1.comments.store'),
            [
                'body' => 'Comment body',
                '_relationships' => [
                    'article' => Article::factory()->create(),
                ],
            ]
        )->assertJsonApiValidationErrors('relationships.author');
    }

    public function test_author_must_exist_in_database()
    {

        Sanctum::actingAs(User::factory()->create());

        $this->postJson(
            route('api.v1.comments.store'),
            [
                'body' => 'Comment body',
                '_relationships' => [
                    'article' => Article::factory()->create(),
                    'author' => User::factory()->make(['id' => 'uuid']), // se manda el id con este metodo, por que sino pasaria el test ... pero por razones equivocadas ya que se dispararia que el id es requerido .. como el metodo make no crea un id
                ],
            ]
        )->assertJsonApiValidationErrors('relationships.author');
    }
}
