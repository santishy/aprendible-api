<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListArticlesTest extends TestCase
{
    use RefreshDatabase;


    public function can_fetch_a_single_article()
    {
        $this->withoutExceptionHandling();
        $article = Article::factory()->create();
        $response = $this->getJson('/api/v1/articles/' . $article->getRouteKey());
        $response->assertSee($article->title);
    }
}
