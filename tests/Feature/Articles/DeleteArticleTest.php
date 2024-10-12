<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteArticleTest extends TestCase
{
    use RefreshDatabase;
    public function test_guests_cannot_delete_articles(): void
    {
        // $this->withoutExceptionHandling();
        $article = Article::factory()->create();
        $this->deleteJson(route('api.v1.articles.destroy', $article))
            ->assertJsonApiError(
                title: "Unauthenticated",
                detail: "This action requires authentication",
                status: "401",
            );
    }
    public function test_can_delete_owned_articles(): void
    {
        // $this->withoutExceptionHandling();
        $article = Article::factory()->create();
        Sanctum::actingAs($article->author, ['article:delete']);
        $this->deleteJson(route('api.v1.articles.destroy', $article))
            ->assertNoContent();
        $this->assertDatabaseCount('articles', 0);
    }
    public function test_cannot_delete_articles_owned_by_others_users(): void
    {
        // $this->withoutExceptionHandling();
        $article = Article::factory()->create();
        Sanctum::actingAs(User::factory()->create());
        $this->deleteJson(route('api.v1.articles.destroy', $article))
            ->assertForbidden();
        $this->assertDatabaseCount('articles', 1);
    }
}
