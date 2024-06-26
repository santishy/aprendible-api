<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_delete_articles(): void
    {
        // $this->withoutExceptionHandling();
        $article = Article::factory()->create();
        $this->deleteJson(route('api.v1.articles.destroy', $article))
            ->assertNoContent();
        $this->assertDatabaseCount('articles', 0);
    }
}
