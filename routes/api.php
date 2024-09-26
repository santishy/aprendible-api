<?php

use App\Http\Controllers\Api\ArticleAuthorController;
use App\Http\Controllers\Api\ArticleCategoryController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::apiResource('articles', ArticleController::class)
    ->names('api.v1.articles'); //aqui es para agregar el api.v1 al estandar de rutas que se genera

Route::apiResource('categories', CategoryController::class)
    ->names('api.v1.categories')
    ->only('show', 'index');

Route::get("articles/{article}/relationships/category", [ArticleCategoryController::class, 'index'])
    ->name('api.v1.articles.relationships.category');

Route::patch("articles/{article}/relationships/category", [ArticleCategoryController::class, 'update'])
    ->name('api.v1.articles.relationships.category');

Route::get("articles/{article}/category", [ArticleCategoryController::class, 'show'])
    ->name('api.v1.articles.category');

Route::get("articles/{article}/relationships/author", [ArticleAuthorController::class, 'index'])
    ->name('api.v1.articles.relationships.author');

Route::patch("articles/{article}/relationships/author", [ArticleAuthorController::class, 'update'])
    ->name('api.v1.articles.relationships.author');

Route::get("articles/{article}/author", [ArticleAuthorController::class, 'show'])
    ->name('api.v1.articles.author');

Route::apiResource('authors', AuthorController::class)
    ->names('api.v1.authors')
    ->only('show', 'index');
