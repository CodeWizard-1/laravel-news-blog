<?php

namespace App\Http\Controllers;

use App\Services\ArticleParserService;
use App\Models\Article;

class ArticleController extends Controller
{
    protected $articleParser;

    public function __construct(ArticleParserService $articleParser)
    {
        $this->articleParser = $articleParser;
    }

    public function index()
    {
        // Загружаем статьи
        $this->articleParser->fetchArticles();

        // Получаем все статьи из базы данных
        $articles = Article::all();

        // Передаем статьи в представление
        return view('articles.index', compact('articles'));
    }
}