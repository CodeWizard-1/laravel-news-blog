<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Article;
use Carbon\Carbon;

class ParseArticles extends Command
{
    protected $signature = 'articles:parse';
    protected $description = 'Parse articles from Laravel News website and save to database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $client = new Client();
        $url = 'https://laravel-news.com/blog';

        $this->info('Fetching articles...');

        try {
            $response = $client->get($url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.9',
                ],
            ]);
            $this->info('Request was successful.');  // Добавьте это сообщение
        } catch (\Exception $e) {
            $this->error('Error fetching articles: ' . $e->getMessage());
            return;
        }        

        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        $articles = $crawler->filter('.group')->each(function (Crawler $node) {
            $title = $node->filter('h3')->text('Untitled');
            $author = 'Unknown'; // Автор по умолчанию
            $tags = []; // Теги по умолчанию
            $link = $node->filter('a')->attr('href');
            $publication_date = now()->format('Y-m-d'); // Текущая дата
        
            // Проверяем, если ссылка относительная, добавляем домен
            if (strpos($link, '/') === 0) {
                $link = 'https://laravel-news.com' . $link;
            }
        
            // Пропускаем ссылки с UTM-параметрами или на рекламные страницы
            if (strpos($link, 'utm_') !== false || strpos($link, '/advertising') !== false) {
                return null; // Возвращаем null, чтобы пропустить статью
            }
        
            return [
                'title' => $title,
                'author' => $author,
                'tags' => $tags,
                'link' => $link,
                'publication_date' => $publication_date,
            ];
        });
        
        // Убираем пустые статьи
        $articles = array_filter($articles, fn($article) => $article !== null);        
        
        dd($articles);  // Добавляем вывод содержимого переменной        

        // Убираем пустые значения (null)
        $articles = array_filter($articles);

        foreach ($articles as $article) {
            $this->info('Parsed article: ' . print_r($article, true));  // Добавьте вывод
            Article::updateOrCreate(
                ['link' => $article['link']],
                [
                    'title' => $article['title'],
                    'author' => $article['author'],
                    'tags' => implode(',', $article['tags']),
                    'publication_date' => $article['publication_date'],
                ]
            );
        }            

        $this->info('Articles parsed and saved to database successfully.');
    }
}
