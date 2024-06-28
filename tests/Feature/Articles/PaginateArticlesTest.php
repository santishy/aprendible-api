<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaginateArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_paginate_articles(): void
    {
        /**
         * la espeficificacion json:api es articles?page[size]=2&page[number]=2
         */
        $articles = Article::factory()->count(6)->create();
        $url = route('api.v1.articles.index', [
            "page" => [
                "size" => 2,
                "number" => 2
            ]
        ]);

        $response = $this->getJson($url);
        $response->assertSee([
            $articles[2]->title,
            $articles[3]->title,
        ]);
        $response->assertDontSee([
            $articles[0]->title,
            $articles[1]->title,
            $articles[4]->title,
            $articles[5]->title,
        ]);

        //verificando la estructura de links segun la especificacion json:api
        $response->assertJsonStructure([
            "links" => ["first", "last", "prev", "next"]
        ]);

        $firstLink = $response->json('links.first');

        // $response->assertStr
    }
}
