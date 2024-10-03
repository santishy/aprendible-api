<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SortArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_sort_articles_by_title(): void
    {
        Article::factory()->create([
            "title" => "c title"
        ]);
        Article::factory()->create([
            "title" => "a title"
        ]);
        Article::factory()->create([
            "title" => "b title"
        ]);
        $uri = route('api.v1.articles.index', ["sort" => "title"]);
        $this->getJson($uri)
            ->assertSeeInOrder([
                "a title",
                "b title",
                "c title",
            ]);
    }
    public function test_can_sort_articles_by_title_descending(): void
    {
        Article::factory()->create([
            "title" => "c title"
        ]);
        Article::factory()->create([
            "title" => "a title"
        ]);
        Article::factory()->create([
            "title" => "b title"
        ]);
        $uri = route('api.v1.articles.index', ["sort" => "-title"]);

        //sort=-title para ordenar de forma descendente segun las especificacion json:api
        $this->getJson($uri)
            ->assertSeeInOrder([
                "c title",
                "b title",
                "a title",
            ]);
    }
    public function test_can_sort_articles_by_content(): void
    {
        Article::factory()->create([
            "content" => "c content"
        ]);
        Article::factory()->create([
            "content" => "a content"
        ]);
        Article::factory()->create([
            "content" => "b content"
        ]);
        $uri = route('api.v1.articles.index', ["sort" => "content"]);
        $this->getJson($uri)
            ->assertSeeInOrder([
                "a content",
                "b content",
                "c content",
            ]);
    }
    public function test_can_sort_articles_by_content_descending(): void
    {
        Article::factory()->create([
            "content" => "c content"
        ]);
        Article::factory()->create([
            "content" => "a content"
        ]);
        Article::factory()->create([
            "content" => "b content"
        ]);
        $uri = route('api.v1.articles.index', ["sort" => "-content"]);
        $this->getJson($uri)
            ->assertSeeInOrder([
                "c content",
                "b content",
                "a content",
            ]);
    }
    public function test_can_sort_articles_by_title_and_content(): void
    {
        Article::factory()->create([
            "title" => "a title",
            "content" => "a content"
        ]);
        Article::factory()->create([
            "title" => "b title",
            "content" => "b content"
        ]);
        Article::factory()->create([
            "title" => "a title",
            "content" => "c content"
        ]);
        $uri = route('api.v1.articles.index', ["sort" => "title,-content"]);
        $this->getJson($uri)
            ->assertSeeInOrder([
                "c content",
                "a content",
                "b content",
            ]);
    }
    public function test_cannot_sort_articles_by_unknown_fields(): void
    {
        Article::factory()->count(3)->create();
        $uri = route('api.v1.articles.index', ["sort" => "unknown"]);
        $this->getJson($uri)->dump()
            ->assertJsonApiError(
                detail: "The sort field 'unknown' is not allowed in the 'articles' resource",
                status: "400",
                title: "Bad request"
            );;
    }
}
