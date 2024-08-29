<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\Models\User;
use Illuminate\Http\Request;

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
