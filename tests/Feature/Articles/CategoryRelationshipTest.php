<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_the_associated_category_identifier(): void
    {
        $this->withoutExceptionHandling();
        $article = Article::factory()->create();
        
        $url = route('api.v1.articles.relationships.category',$article);

        $response = $this->getJson($url)->dump();

        $response->assertExactJson([
            "data" => [
                "type" => "categories",
                "id" => $article->category->getRouteKey()
            ]
            ]);
    }
}
