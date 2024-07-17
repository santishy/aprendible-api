<?php

namespace Tests\Unit\JsonApi;

use App\JsonApi\Document;
use Mockery;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_can_create_json_api_document(): void
    {
        $category = Mockery::mock("Category", function ($mock) {
            $mock->shouldReceive("getResourceType")->andReturn('categories');
            $mock->shouldReceive("getRouteKey")->andReturn("category-id");
        });
        $document = Document::type("articles")
            ->id("article-id")
            ->attributes(["title" => "article title"])
            ->relationshipsData(["category" => $category])
            ->toArray();

        $expected = [
            "data" => [
                "type" => "articles",
                "id" => "article-id",
                "attributes" => [
                    "title" => "article title"
                ],
                "relationships" => [
                    "category" => [
                        "data" => [
                            "id" => "category-id",
                            "type" => "categories"
                        ]
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $document);
    }
}
