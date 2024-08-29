<?php

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

Route::get("articles/{article}/relationships/category", fn() => "ok")
    ->name('api.v1.articles.relationships.category');

Route::get("articles/{article}/category", fn() => "ok")
    ->name('api.v1.articles.category');

Route::apiResource('authors', AuthorController::class)
    ->names('api.v1.authors')
    ->only('show', 'index');
