<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Article;
use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

class ArticleParserService
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function fetchArticles()
    {
        // URL для получения статей с тегом 'news'
        $url = 'https://laravel-news.com/blog?tag=news';

        // Устанавливаем заголовки для имитации запроса от браузера
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        ];

        // Получаем данные с сайта с заголовками
        try {
            $response = $this->client->get($url, [
                'headers' => $headers
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Обрабатываем исключение, если не удалось получить данные
            echo 'Ошибка: ' . $e->getMessage();
            return;
        }

        $html = (string) $response->getBody();

        // Используем библиотеку DOM для парсинга HTML
        $crawler = new Crawler($html);

        // Парсим статьи
        $articles = $crawler->filter('.group')->each(function (Crawler $node) {
            $title = $node->filter('h3')->text('Untitled'); // Получаем заголовок
            $author = 'Unknown'; // Автор по умолчанию
            $tags = []; // Теги по умолчанию, нужно будет обновить, если теги будут на странице
            $link = $node->filter('a')->attr('href'); // Ссылка на статью
            $publication_date = now()->format('Y-m-d'); // Текущая дата (можно заменить, если дата есть на странице)

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

        // Убираем null значения из массива
        $articles = array_filter($articles);
        $articles = array_values($articles); // Пересчитываем индексы массива

        // Сохраняем данные в базе
        foreach ($articles as $articleData) {
            // Обновляем или создаем статью в базе данных
            Article::updateOrCreate(
                ['link' => $articleData['link']],
                [
                    'title' => $articleData['title'],
                    'author' => $articleData['author'],
                    'tags' => implode(',', $articleData['tags']), // Преобразуем теги в строку
                    'publication_date' => $articleData['publication_date']
                ]
            );
        }
    }
}