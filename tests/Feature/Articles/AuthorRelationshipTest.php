<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_the_associated_author_identifier(): void
    {
        //$this->withoutExceptionHandling();
        $article = Article::factory()->create();

        $url = route('api.v1.articles.relationships.author', $article);

        $response = $this->getJson($url);

        $response->assertExactJson([
            'data' => [
                'type' => 'authors',
                'id' => $article->author->getRouteKey(),
            ],
        ]);
    }

    public function test_can_fetch_the_associated_author_resource(): void
    {
        $article = Article::factory()->create()->load('author');

        $url = route('api.v1.articles.author', $article);

        $this->getJson($url)
            ->assertJson([
                'data' => [
                    'type' => 'authors',
                    'id' => $article->author->getRouteKey(),
                    'attributes' => [
                        'name' => $article->author->name,
                    ],
                ],
            ]);
    }

    public function test_can_update_the_associated_author()
    {
        $article = Article::factory()->create();
        $author = User::factory()->create();
        $url = route('api.v1.articles.relationships.author', $article);

        $response = $this->patchJson($url, [
            'data' => [
                'type' => 'authors',
                'id' => $author->getRouteKey(),
            ],
        ]);
        $response->assertExactJson([
            'data' => [
                'type' => 'authors',
                'id' => $author->getRouteKey(),
            ],
        ]);

        $this->assertDatabaseHas('articles', [
            'title' => $article->title,
            'user_id' => $author->id,
        ]);
    }

    public function test_author_must_exist_database()
    {
        $article = Article::factory()->create();
        $url = route('api.v1.articles.relationships.author', $article);

        $this->patchJson($url, [
            'data' => [
                'type' => 'authors',
                'id' => 'non-existing',
            ],
        ])->assertJsonApiValidationErrors('data.id');

        $this->assertDatabaseHas('articles', [
            'title' => $article->title,
            'user_id' => $article->user_id,
        ]);
    }
}
