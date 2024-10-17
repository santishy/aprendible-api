<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ArticleController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', only: ['store', 'update', 'destroy'])
        ];
    }

    public function index()
    {
        $articles = Article::query();

        $articles->allowedIncludes(['category', 'author']);

        $articles->allowedFilters(["title", "content", "year", "month", "categories"]);

        $articles->allowedSorts(['title', 'content']);

        $articles->sparseFieldset();

        return ArticleResource::collection(
            $articles->jsonPaginate()
        );
    }
    //si se quita el modelo Article del bind entonces queda el getRouteKey
    public function show($article): ArticleResource
    {
        $article = Article::where('slug', $article)
            ->allowedIncludes(['category', 'author'])
            ->sparseFieldset()
            ->firstOrFail();

        return ArticleResource::make($article);
    }

    public function store(SaveArticleRequest $request)
    {
        Gate::authorize('create', new Article);
        $data = $request->validated()["data"];
        $articleData = $data['attributes'];
        $articleData['user_id'] =
            $data['relationships']['author']['data']['id'];
        $categorySlug =
            $data['relationships']['category']['data']['id'];
        $category = Category::whereSlug($categorySlug)->first();
        $articleData['category_id'] = $category->id;
        $article = Article::create($articleData);
        return ArticleResource::make($article);
    }

    public function update(Article $article, SaveArticleRequest $request)
    {
        Gate::authorize('update', $article);
        $data = $request->validated()["data"];
        $articleData = $data['attributes'];
        if (isset($data['relationships'])) {
            if (isset($data['relationships']['author'])) {

                $articleData['user_id'] =
                    $data['relationships']['author']['data']['id'];
            }
            if (isset($data['relationships']['category'])) {

                $categorySlug =
                    $data['relationships']['category']['data']['id'];
                $category = Category::whereSlug($categorySlug)->first();
                $articleData['category_id'] = $category->id;
            }
        }
        $article->update($articleData);
        return ArticleResource::make($article);
    }
    public function destroy(Article $article)
    {
        Gate::authorize('delete', $article);
        $article->delete();
        return response()->noContent();
    }
}
