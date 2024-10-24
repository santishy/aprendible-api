<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\CommentResource;
use App\Http\Requests\SaveCommentRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class CommentController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', only: ['store', 'update', 'destroy']),
        ];
    }

    public function index()
    {
        $comments = Comment::paginate();

        return CommentResource::collection($comments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveCommentRequest $request)
    {
        $comment = new Comment;
        $comment->body = $request->input('data.attributes.body');
        $articleSlug = $request->getRelationshipId('article');
        $comment->user_id =
            $request->getRelationshipId('author');
        $comment->article_id =
            Article::whereSlug($articleSlug)->firstOrFail()->id;
        $comment->save();

        return CommentResource::make($comment);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        return CommentResource::make($comment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveCommentRequest $request, Comment $comment)
    {

        $comment->body = $request->input('data.attributes.body');
        if ($request->hasRelationship('article')) {
            $articleSlug = $request->getRelationshipId('article');
            $comment->article_id = Article::whereSlug($articleSlug)->firstOrFail()->id;
        }
        if ($request->hasRelationship('author')) {
            $userId = $request->getRelationshipId('author');
            $comment->user_id = $userId;
        }

        $comment->save();

        return CommentResource::make($comment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        Gate::authorize('delete', $comment);
        $comment->delete();

        return response()->noContent();
    }
}
