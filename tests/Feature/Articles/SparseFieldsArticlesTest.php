<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SparseFieldsArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_specific_fields_can_be_requested_index(): void
    {
        //$this->withoutExceptionHandling();
        $article = Article::factory()->create();

        // json:api spec. articles?fields[articles]=title,slug

        $url = route('api.v1.articles.index', [
            "fields[articles]" => "title,slug"
        ]);

        $this->getJson($url)->dump();
        // ->assertJsonFragment([
        //     "title" => $article->title,
        //     "slug" => $article->slug,
        // ])
        // ->assertJsonMissing([
        //     "content" => $article->content
        // ])
        // ->assertJsonMissing([
        //     "content" => null
        // ]);
    }
    public function test_route_key_must_be_added_automatically_index(): void
    {
        $article = Article::factory()->create();

        // json:api spec. articles?fields[articles]=title

        $url = route('api.v1.articles.index', [
            "fields[articles]" => "title"
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                "title" => $article->title,
            ])
            ->assertJsonMissing([
                "slug" => $article->slug,
                "content" => $article->content
            ]);
    }
    public function test_specific_fields_can_be_requested_show(): void
    {
        $article = Article::factory()->create();

        // json:api spec. articles?fields[articles]=title,slug

        $url = route('api.v1.articles.show', [
            "article" => $article,
            "fields[articles]" => "title,slug"
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                "title" => $article->title,
                "slug" => $article->slug,
            ])
            ->assertJsonMissing([
                "content" => $article->content
            ])
            ->assertJsonMissing([
                "content" => null
            ]);
    }
    public function test_route_key_must_be_added_automatically_show(): void
    {
        $article = Article::factory()->create();

        // json:api spec. articles?fields[articles]=title

        $url = route('api.v1.articles.show', [
            "article" => $article,
            "fields[articles]" => "title"
        ]);

        $this->getJson($url)
            ->assertJsonFragment([
                "title" => $article->title,
            ])
            ->assertJsonMissing([
                "slug" => $article->slug,
                "content" => $article->content
            ]);
    }
}
