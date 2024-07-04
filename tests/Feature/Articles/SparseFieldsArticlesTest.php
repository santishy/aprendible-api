<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SparseFieldsArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_specific_fields_can_be_requested(): void
    {
        $article = Article::factory()->create();

        // json:api spec. articles?fields[articles]=title,slug

        $url = route('api.v1.articles.index', [
            "fields[articles]" => "title,slug"
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                "title" => $article->title,
                "slug" => $article->slug,
            ])
            ->assertJsonMissing([
                "content" => $article->content
            ]);
    }
}
