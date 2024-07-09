<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_a_single_category(): void
    {
        $category = Category::factory()->create();

        $this->getJson(route('api.v1.categories.show', $category))
            ->assertJsonApiResource($category, [
                "name" => $category->name,
            ]);
    }
}
