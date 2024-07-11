<?php

namespace Tests\Unit\JsonApi;

use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_can_create_json_api_document(): void
    {
        Document::type("articles")
            ->id("article-id")
            ->attributes(["title" => "article title"])
            ->toArray();

        $expected = [
            "data" => [
                "type" => "articles",
                "id" => "article-id",
                "attributes" => [
                    "title" => "article title"
                ],

            ]
        ];
    }
}
