<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sortField', 'author');
        $sortDirection = $request->get('sortDirection', 'asc');

        $articles = Article::query()
            ->orderBy($sortField, $sortDirection)
            ->get();

        return view('index', compact('articles', 'sortField', 'sortDirection'));
    }
}



