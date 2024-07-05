<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::query();

        $articles->allowedFilters(["title", "content", "year", "month"]);

        $articles->allowedSorts(['title', 'content']);

        $articles->sparseFieldset();

        return ArticleCollection::make(
            $articles->jsonPaginate()
        );
    }
    //si se quita el modelo Article del bind entonces queda el getRouteKey
    public function show($article): ArticleResource
    {
        $article = Article::where('slug', $article)
            ->sparseFieldset()
            ->firstOrFail();

        return ArticleResource::make($article);
    }

    public function store(SaveArticleRequest $request)
    {
        $article = Article::create($request->validated());
        return ArticleResource::make($article);
    }

    public function update(Article $article, SaveArticleRequest $request)
    {

        $article->update($request->validated());
        return ArticleResource::make($article);
    }
    public function destroy(Article $article)
    {
        $article->delete();
        return response()->noContent();
    }
}
