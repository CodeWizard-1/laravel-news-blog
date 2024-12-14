<?php

namespace App\Http\Controllers;

use App\Services\ArticleParserService;
use App\Models\Article;
use Illuminate\Http\Request;


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

    public function fetchUpdates(Request $request)
    {
        try {
            // Загружаем новые статьи через сервис
            $this->articleParser->fetchArticles();

            // Возвращаем успешный JSON-ответ
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // В случае ошибки, возвращаем сообщение об ошибке
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function getArticles()
    {
        $articles = Article::all();  // Или используйте сортировку/фильтрацию, если нужно

        return response()->json([
            'articles' => $articles
        ]);
    }
}


