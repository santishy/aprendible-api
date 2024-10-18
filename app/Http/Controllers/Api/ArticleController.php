<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\SaveArticleRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ArticleController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', only: ['store', 'update', 'destroy']),
        ];
    }

    public function index()
    {
        $articles = Article::query();

        $articles->allowedIncludes(['category', 'author']);

        $articles->allowedFilters(['title', 'content', 'year', 'month', 'categories']);

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

        $articleData = $request->getAttributes();

        $articleData['user_id'] = $request->getRelationshipId('author');

        $categorySlug = $request->getRelationshipId('category');

        $category = Category::whereSlug($categorySlug)->first();

        $articleData['category_id'] = $category->id;

        $article = Article::create($articleData);

        return ArticleResource::make($article);
    }

    public function update(Article $article, SaveArticleRequest $request)
    {
        Gate::authorize('update', $article);

        $articleData = $request->getAttributes();

        if ($request->hasRelationship('author')) {

            $articleData['user_id'] = $request->getRelationshipId('author');
        }

        if ($request->hasRelationship('category')) {

            $categorySlug = $request->getRelationshipId('category');
            $category = Category::whereSlug($categorySlug)->first();
            $articleData['category_id'] = $category->id;
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
