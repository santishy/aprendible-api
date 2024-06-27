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
    public function index(Request $request)
    {
        $sortField = $request->input('sort');

        $sortDirection = Str::of($sortField)->startsWith("-") ? 'desc' : "asc";
        $sortField = ltrim($sortField, "-");
        return ArticleCollection::make(Article::orderBy($sortField, $sortDirection)->get());
    }
    public function show(Article $article): ArticleResource
    {
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
