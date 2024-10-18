<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::jsonPaginate();

        return CategoryResource::collection($categories);
    }

    public function show($category)
    {
        //whereSlug es la propiedad magica , where+Property
        $category = Category::whereSlug($category)->firstOrFail();

        return CategoryResource::make($category);
    }
}
