<?php

use App\Http\Controllers\ArticleController;

Route::get('/', [ArticleController::class, 'index']);
Route::post('/fetch-updates', [ArticleController::class, 'fetchUpdates']);
// В web.php добавьте маршрут для получения статей
Route::get('/articles', [ArticleController::class, 'getArticles']);


