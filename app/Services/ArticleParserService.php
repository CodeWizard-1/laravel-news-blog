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
            // Логируем ошибку вместо вывода на экран
            Log::error('Ошибка при запросе данных: ' . $e->getMessage());
            return; // Останавливаем выполнение, если ошибка
        }

        $html = (string) $response->getBody();

        // Используем библиотеку DOM для парсинга HTML
        $crawler = new Crawler($html);

        // Парсим статьи
        $articles = $crawler->filter('.group')->each(function (Crawler $node) {
            $title = $node->filter('h3')->text('Untitled'); // Получаем заголовок
            $authorNode = $node->filter('a[rel="author"]');
            $author = $authorNode->count() ? $authorNode->text() : 'Unknown';           
            $tags = $node->filter('.flex .inline-flex')->each(function (Crawler $tagNode) {
                return $tagNode->text();
            }); // Теги по умолчанию, нужно будет обновить, если теги будут на странице
            $link = $node->filter('a')->attr('href'); // Ссылка на статью
            $publication_date = $node->filter('time')->first();
            if ($publication_date->count()) {
                $publication_date = $publication_date->attr('datetime');
            } else {
                $publication_date = now()->format('Y-m-d'); // Если нет, используем текущую дату
            }
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

        // Теперь для каждой статьи извлекаем дополнительные данные с её страницы
        foreach ($articles as &$articleData) {
            try {
                $response = $this->client->get($articleData['link'], [
                    'headers' => $headers
                ]);
                $html = (string) $response->getBody();
                $articleCrawler = new Crawler($html);

                // Извлекаем дату, автора и дополнительные теги
                $publicationDateNode = $articleCrawler->filter('time'); // Убедитесь, что селектор правильный
                $publicationDate = $publicationDateNode->count()
                    ? $publicationDateNode->attr('datetime')
                    : now()->format('Y-m-d');

                // Извлекаем только теги из ссылок с конкретным href (начинаются на "/tag" или "/category")
                $tags = $articleCrawler->filter('div.flex.flex-wrap.items-center a')->reduce(function (Crawler $node) {
                    $href = $node->attr('href');
                    // Проверяем, что ссылка содержит нужные маркеры (tag или category)
                    return str_starts_with($href, '/tag') || str_starts_with($href, '/category');
                })->each(function (Crawler $tagNode) {
                    return trim($tagNode->text());
                });
                // Извлекаем имя автора
                $authorNode = $articleCrawler->filter('a[rel="author"]');
                $author = $authorNode->count() ? trim($authorNode->text()) : 'Unknown';

                $articleData['publication_date'] = $publicationDate;
                $articleData['tags'] = array_unique(array_merge($articleData['tags'], $tags));
                $articleData['author'] = $author; // Добавляем автора
            } catch (\Exception $e) {
                Log::error("Ошибка при парсинге статьи {$articleData['link']}: " . $e->getMessage());
            }
        }





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

