<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = User::jsonPaginate();

        return AuthorResource::collection($authors);
    }

    public function show($author)
    {
        $author = User::findOrFail($author);

        return AuthorResource::make($author);
    }
}
