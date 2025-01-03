<?php

// namespace App\Http\Controllers;

// use App\Services\ArticleParserService;
// use App\Models\Article;
// use Illuminate\Http\Request;
// use Carbon\Carbon;


// class ArticleController extends Controller
// {
//     protected $articleParser;

//     public function __construct(ArticleParserService $articleParser)
//     {
//         $this->articleParser = $articleParser;
//     }

//      // Метод для удаления статей без тега "News"
//      public function deleteOldArticlesWithoutNews()
//      {
//          try {
//              // Удаляем статьи, у которых нет тега "News"
//              Article::whereRaw('NOT FIND_IN_SET("News", tags)')->delete();
     
//              Log::info('Удалены статьи без тега "News"');
     
//              return response()->json(['success' => true, 'message' => 'Articles without "News" tag deleted']);
//          } catch (\Exception $e) {
//              Log::error('Ошибка при удалении статей без тега "News": ' . $e->getMessage());
//              return response()->json(['success' => false, 'message' => $e->getMessage()]);
//          }
//      }
     

//     public function fetchNewArticles()
//     {
//         try {
//             // Загружаем новые статьи через сервис
//             $this->articleParser->fetchArticles();

//             return response()->json(['success' => true, 'message' => 'New articles fetched successfully']);
//         } catch (\Exception $e) {
//             return response()->json(['success' => false, 'message' => $e->getMessage()]);
//         }
//     }

//     public function fetchUpdates(Request $request)
//     {
//         try {
//             // Загружаем новые статьи через сервис
//             $this->articleParser->fetchArticles();

//             // Возвращаем успешный JSON-ответ
//             return response()->json(['success' => true]);
//         } catch (\Exception $e) {
//             // В случае ошибки, возвращаем сообщение об ошибке
//             return response()->json(['success' => false, 'message' => $e->getMessage()]);
//         }
//     }
//     public function getArticles()
//     {
//         $fourMonthsAgo = Carbon::now()->subMonths(4);  // Дата 4 месяца назад

//         // Получаем только статьи, опубликованные за последние 4 месяца и с тегом "News"
//         $articles = Article::where('publication_date', '>=', $fourMonthsAgo)
//             ->whereRaw('FIND_IN_SET("News", tags)') // Фильтруем статьи по тегу "News"
//             ->get();

//         return response()->json([
//             'articles' => $articles
//         ]);
//     }

//     public function index(Request $request)
//     {
//         $this->articleParser->fetchArticles(); // Загрузка статей через сервис

//         $sortBy = $request->get('sort_by', 'author'); // По умолчанию сортировать по автору
//         $order = $request->get('order', 'asc');

//         $fourMonthsAgo = Carbon::now()->subMonths(4);  // Дата 4 месяца назад

//         // Получаем только статьи, опубликованные за последние 4 месяца и с тегом "News"
//         $articles = Article::where('publication_date', '>=', $fourMonthsAgo)
//             ->whereRaw('FIND_IN_SET("News", tags)') // Фильтруем статьи по тегу "News"
//             ->orderBy($sortBy, $order)
//             ->get();

//         return view('articles.index', compact('articles', 'sortBy', 'order'));
//     }

// }
/*
namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\ArticleParserService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected $articleParserService;

    public function __construct(ArticleParserService $articleParserService)
    {
        $this->articleParserService = $articleParserService;
    }

    public function index()
    {
        // Загружаем статьи из базы данных с тегом "News" и сортируем по автору
        $articles = Article::where('tags', 'like', '%News%')
            ->orderBy('author', 'asc')
            ->get();

        return view('articles.index', compact('articles'));
    }

    // Метод для обработки запроса на обновление
    public function fetchUpdates()
    {
        // Загружаем и сохраняем новые статьи
        $this->articleParserService->fetchArticles();

        // Возвращаем успех
        return response()->json([
            'success' => true,
        ]);
    }

    // Метод для получения списка статей в формате JSON
    public function getArticles()
    {
        // Получаем статьи с тегом "News" и сортируем по автору
        $articles = Article::where('tags', 'like', '%News%')
            ->orderBy('author', 'asc')
            ->get();

        // Возвращаем статьи в формате JSON
        return response()->json([
            'articles' => $articles
        ]);
    }
}
*/

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\ArticleParserService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected $articleParserService;

    public function __construct(ArticleParserService $articleParserService)
    {
        $this->articleParserService = $articleParserService;
    }

    public function index(Request $request)
    {
        // Get sorting parameters from the query, if any, or by default
        $sortBy = $request->get('sort_by', 'author'); // By default, we sort by author
        $order = $request->get('order', 'asc'); // By default we sort in ascending order (alphabetically)

        // If we sort by author, we must sort in ascending order (alphabetical)
        if ($sortBy === 'author') {
            $order = 'asc';
        }

        // Upload articles with the tag "News" and sort by selected criteria
        $articles = Article::where('tags', 'like', '%News%')
            ->orderBy($sortBy, $order)
            ->get();

        return view('articles.index', compact('articles'));
    }

    // Method for processing an update request
    public function fetchUpdates()
    {
        // Uploading and saving new articles
        $this->articleParserService->fetchArticles();

        // Bringing back success
        return response()->json([
            'success' => true,
        ]);
    }

    // Method to get the list of articles in JSON format
    public function getArticles()
    {
        // Get articles with the tag "News" and sort by author
        $articles = Article::where('tags', 'like', '%News%')
            ->orderBy('author', 'asc')
            ->get();

        // Return articles in JSON format
        return response()->json([
            'articles' => $articles
        ]);
    }
}

