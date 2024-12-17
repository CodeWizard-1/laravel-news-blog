<?php

use App\Http\Controllers\ArticleController;

Route::get('/', [ArticleController::class, 'index']);
Route::post('/fetch-updates', [ArticleController::class, 'fetchUpdates']);
Route::get('/articles', [ArticleController::class, 'getArticles']);
