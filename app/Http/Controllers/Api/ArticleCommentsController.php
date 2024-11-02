<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;

class ArticleCommentsController extends Controller
{
    public function index(Article $article)
    {
        return CommentResource::identifiers($article->comments);
    }

    public function show(Article $article)
    {
        return CommentResource::collection($article->comments);
    }

    public function update(Article $article, Request $request)
    {

        $request->validate([
            'data.*.id' => 'exists:comments,id',
        ]);
        //$request->input('data.*.id') con esto obtengo todos los ids en un simple array
        $comments = Comment::find($request->input('data.*.id'));
        $comments->each->update(['article_id' => $article->id]);

        return CommentResource::identifiers($comments);
    }
}
