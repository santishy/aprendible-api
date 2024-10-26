<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Middleware\ValidateJsonApiHeaders;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Middleware\ValidateJsonApiDocument;
use App\Http\Controllers\Api\ArticleAuthorController;
use App\Http\Controllers\Api\CommentAuthorController;
use App\Http\Controllers\Api\CommentArticleController;
use App\Http\Controllers\Api\ArticleCategoryController;
use App\Http\Controllers\Api\ArticleCommentsController;

Route::apiResource('authors', AuthorController::class)
    ->names('api.v1.authors')
    ->only('show', 'index');

Route::apiResource('comments', CommentController::class)->names('api.v1.comments')->except('update');

Route::patch('comments/{comment}', [CommentController::class, 'update'])->middleware('can:update,comment')->name('api.v1.comments.update');

Route::apiResource('articles', ArticleController::class)
    ->names('api.v1.articles'); //aqui es para agregar el api.v1 al estandar de rutas que se genera

Route::apiResource('categories', CategoryController::class)
    ->names('api.v1.categories')
    ->only('show', 'index');

Route::prefix('articles/{article}')->group(function () {

    Route::controller(ArticleAuthorController::class)
        ->group(function () {

            Route::get('relationships/author', [ArticleAuthorController::class, 'index'])
                ->name('api.v1.articles.relationships.author');

            Route::patch('relationships/author', [ArticleAuthorController::class, 'update']);

            Route::get('author', [ArticleAuthorController::class, 'show'])
                ->name('api.v1.articles.author');
        });

    Route::controller(ArticleCategoryController::class)
        ->group(function () {

            Route::get('relationships/category', 'index')
                ->name('api.v1.articles.relationships.category');

            Route::patch('relationships/category', 'update');

            Route::get('category', 'show')
                ->name('api.v1.articles.category');
        });
    Route::controller(ArticleCommentsController::class)->group(function () {
        Route::get('relationships/comments', 'index')
            ->name('api.v1.articles.relationships.comments');
        Route::patch('relationships/comments', 'update');
        Route::get('comments', 'show')
            ->name('api.v1.articles.comments');
    });
});

Route::prefix('comments/{comment}')
    ->group(function () {

        Route::controller(CommentAuthorController::class)->group(function () {
            Route::get('relationships/author', 'index')
                ->name('api.v1.comments.relationships.author');

            Route::get('author', 'show')
                ->name('api.v1.comments.author');

            Route::patch('relationships/author', 'update');
        });

        Route::controller(CommentArticleController::class)
            ->group(function () {
                Route::get('relationships/article', 'index')->name('api.v1.comments.relationships.article');

                Route::get('article', 'show')->name('api.v1.comments.article');

                Route::patch('relationships/article', 'update');
                //->name('api.v1.comments.relationships.article');se quito el nombre aqui por que ya hay una ruta identica solo que con otro vergo HTTP y se por defecto se le aplicara el mismo nombre
            });
    });

// en la ruta login, logout y register como no esta aderido a la especificacion json:api se desabilita el middleware que valida que lleve ciertos datos. como los headers , el data, el type y el id..

Route::withoutMiddleware([
    ValidateJsonApiDocument::class,
    ValidateJsonApiHeaders::class,
])->group(function () {
    Route::post('login', LoginController::class)->name('api.v1.login');

    Route::post('logout', LogoutController::class)->name('api.v1.logout');

    Route::post('register', RegisterController::class)->name('api.v1.register');
});
