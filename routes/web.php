<?php

use App\Http\Controllers\ArticleController;

Route::get('/', [ArticleController::class, 'index']);
