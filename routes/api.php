<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Middleware\ValidateJsonApiHeaders;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Middleware\ValidateJsonApiDocument;
use App\Http\Controllers\Api\ArticleAuthorController;
use App\Http\Controllers\Api\ArticleCategoryController;
use App\Http\Controllers\CommentController;

Route::apiResource('articles', ArticleController::class)
    ->names('api.v1.articles'); //aqui es para agregar el api.v1 al estandar de rutas que se genera

Route::apiResource('categories', CategoryController::class)
    ->names('api.v1.categories')
    ->only('show', 'index');

Route::get('articles/{article}/relationships/category', [ArticleCategoryController::class, 'index'])
    ->name('api.v1.articles.relationships.category');

Route::patch('articles/{article}/relationships/category', [ArticleCategoryController::class, 'update'])
    ->name('api.v1.articles.relationships.category');

Route::get('articles/{article}/category', [ArticleCategoryController::class, 'show'])
    ->name('api.v1.articles.category');

Route::get('articles/{article}/relationships/author', [ArticleAuthorController::class, 'index'])
    ->name('api.v1.articles.relationships.author');

Route::patch('articles/{article}/relationships/author', [ArticleAuthorController::class, 'update'])
    ->name('api.v1.articles.relationships.author');

Route::get('articles/{article}/author', [ArticleAuthorController::class, 'show'])
    ->name('api.v1.articles.author');

Route::apiResource('authors', AuthorController::class)
    ->names('api.v1.authors')
    ->only('show', 'index');

Route::apiResource('comments', CommentController::class)
    ->names('api.v1.comments');


// en la ruta login, logout y register como no esta aderido a la especificacion json:api se desabilita el middleware que valida que lleve ciertos datos. como los headers , el data, el type y el id..

Route::withoutMiddleware([
    ValidateJsonApiDocument::class,
    ValidateJsonApiHeaders::class,
])->group(function () {
    Route::post('login', LoginController::class)->name('api.v1.login');

    Route::post('logout', LogoutController::class)->name('api.v1.logout');

    Route::post('register', RegisterController::class)->name('api.v1.register');
});
