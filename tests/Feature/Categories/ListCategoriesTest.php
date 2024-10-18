<?php

namespace Tests\Feature\Categories;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_a_single_category(): void
    {
        $category = Category::factory()->create();

        $this->getJson(route('api.v1.categories.show', $category))
            ->assertJsonApiResource($category, [
                'name' => $category->name,
            ]);
    }

    public function test_can_fetch_all_categories(): void
    {
        $categories = Category::factory()->count(3)->create();

        $this->getJson(route('api.v1.categories.index'))
            ->assertJsonApiResourceCollection($categories, [
                'name',
            ]);
    }
}
