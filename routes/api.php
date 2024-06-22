<?php

use App\Http\Controllers\Api\ArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('articles/{article}', [ArticleController::class, 'show'])
    ->name('api.v1.articles.show');

Route::get('articles', [ArticleController::class, 'index'])
    ->name('api.v1.articles.index');


Route::post('articles', [ArticleController::class, 'store'])
    ->name('api.v1.articles.store');

Route::patch('articles/{article}', [ArticleController::class, 'update'])
    ->name('api.v1.articles.update');
