<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;

class CommentAuthorController extends Controller
{
    public function index(Comment $comment)
    {
        return AuthorResource::identifier($comment->author);
    }

    public function show(Comment $comment)
    {
        return AuthorResource::make($comment->author);
    }

    public function update(Comment $comment, Request $request)
    {
        $request->validate([
            'data.id' => ['exists:users,id'],
        ]);
        $comment->update(['user_id' => $request->input('data.id')]);

        return AuthorResource::identifier($comment->author);
    }
}
