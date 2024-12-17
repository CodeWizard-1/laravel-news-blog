<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Article;
use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;

class ArticleParserService
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function fetchArticles()
    {
        set_time_limit(120);
        $baseUrl = 'https://laravel-news.com/blog?tag=news&page=';
        $maxPages = 3; 
        $currentPage = 1; 

        // Headers for HTTP requests
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        ];

        // Going through the pages
        do {
            $currentPageUrl = $baseUrl . $currentPage;

            try {
                $response = $this->client->get($currentPageUrl, [
                    'headers' => $headers,
                    'timeout' => 120,
                ]);
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                Log::error('Error during data request: ' . $e->getMessage());
                break;
            }

            $html = (string) $response->getBody();
            $crawler = new Crawler($html);

            // Parsing the current page
            $this->parsePage($crawler, $headers);

            $currentPage++; // Go to the next page
        } while ($currentPage <= $maxPages);
    }

    private function parsePage(Crawler $crawler, array $headers)
    {
        // Parsing all articles on the current page
        $articles = $crawler->filter('.group')->each(function (Crawler $node) use ($headers) {
            $title = $node->filter('h3')->text('Untitled');
            $authorNode = $node->filter('a[rel="author"]');
            $author = $authorNode->count() ? $authorNode->text() : 'Unknown';
            $tags = $node->filter('.flex .inline-flex')->each(function (Crawler $tagNode) {
                return $tagNode->text();
            });
            $link = $node->filter('a')->attr('href');
            $publication_date = $node->filter('time')->first();
            $publication_date = $publication_date->count() ? $publication_date->attr('datetime') : null;

            if (strpos($link, '/') === 0) {
                $link = 'https://laravel-news.com' . $link;
            }

            if (strpos($link, 'utm_') !== false || strpos($link, '/advertising') !== false) {
                return null;
            }

            return [
                'title' => $title,
                'author' => $author,
                'tags' => $tags,
                'link' => $link,
                'publication_date' => $publication_date,
            ];
        });

        $articles = array_filter($articles);
        $articles = array_values($articles);

        // Parsing additional data from each article
        foreach ($articles as &$articleData) {
            try {
                $response = $this->client->get($articleData['link'], [
                    'headers' => $headers,
                    'timeout' => 120, 
                ]);
                $html = (string) $response->getBody();
                $articleCrawler = new Crawler($html);

                $publicationDateNode = $articleCrawler->filter('time');
                $publicationDate = $publicationDateNode->count()
                    ? $publicationDateNode->attr('datetime')
                    : now()->format('Y-m-d');

                $tags = $articleCrawler->filter('div.flex.flex-wrap.items-center a')->reduce(function (Crawler $node) {
                    $href = $node->attr('href');
                    return str_starts_with($href, '/tag') || str_starts_with($href, '/category');
                })->each(function (Crawler $tagNode) {
                    return trim($tagNode->text());
                });

                $authorNode = $articleCrawler->filter('a[rel="author"]');
                $author = $authorNode->count() ? trim($authorNode->text()) : 'Unknown';

                $articleData['publication_date'] = $publicationDate;
                $articleData['tags'] = array_unique(array_merge($articleData['tags'], $tags));
                $articleData['author'] = $author;
            } catch (\Exception $e) {
                Log::error("Error when parsing an article {$articleData['link']}: " . $e->getMessage());
            }
        }

        // Saving data in the database
        foreach ($articles as $articleData) {
            Article::updateOrCreate(
                ['link' => $articleData['link']],
                [
                    'title' => $articleData['title'],
                    'author' => $articleData['author'],
                    'tags' => implode(',', $articleData['tags']),
                    'publication_date' => $articleData['publication_date'],
                ]
            );
        }
    }
}

// This code allows you to get articles for the last 4 months, but the process takes a long time and requires improvement
/*

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Article;
use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;

class ArticleParserService
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function fetchArticles()
    {
        $page = 1;  
        $baseUrl = 'https://laravel-news.com/blog?tag=news&page=';

        // Set headers to simulate a request from the browser
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        ];

        while (true) {
            $url = $baseUrl . $page;

            try {
                $response = $this->client->get($url, [
                    'headers' => $headers
                ]);
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                // Logging an error instead of displaying it on the screen
                Log::error('Error during data request: ' . $e->getMessage());
                break;
            }

            $html = (string) $response->getBody();

            // Using DOM library for HTML parsing
            $crawler = new Crawler($html);

            // Parsing articles
            $articles = $crawler->filter('.group')->each(function (Crawler $node) {
                $title = $node->filter('h3')->text('Untitled'); 
                $authorNode = $node->filter('a[rel="author"]');
                $author = $authorNode->count() ? $authorNode->text() : 'Unknown';           
                $tags = $node->filter('.flex .inline-flex')->each(function (Crawler $tagNode) {
                    return $tagNode->text();
                }); 
                $link = $node->filter('a')->attr('href');
                $publication_date = $node->filter('time')->first();
                if ($publication_date->count()) {
                    $publication_date = $publication_date->attr('datetime');
                } else {
                    $publication_date = null;
                }

                // Check if the link is relative, add the domain
                if (strpos($link, '/') === 0) {
                    $link = 'https://laravel-news.com' . $link;
                }

                // Skip links with UTM parameters or to advertising pages
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

            // Remove null values from the array
            $articles = array_filter($articles);
            $articles = array_values($articles); // Пересчитываем индексы массива

            if (count($articles) == 0) {
                
                break;
            }

            // Now for each article we extract additional data from its page
            foreach ($articles as &$articleData) {
                try {
                    $response = $this->client->get($articleData['link'], [
                        'headers' => $headers
                    ]);
                    $html = (string) $response->getBody();
                    $articleCrawler = new Crawler($html);

                    // Retrieve date, author and additional tags
                    $publicationDateNode = $articleCrawler->filter('time');
                    $publicationDate = $publicationDateNode->count()
                        ? $publicationDateNode->attr('datetime')
                        : now()->format('Y-m-d');

                    // Extract only tags from links with a specific href (starting with "/tag" or "/category")
                    $tags = $articleCrawler->filter('div.flex.flex-wrap.items-center a')->reduce(function (Crawler $node) {
                        $href = $node->attr('href');
                        //Check that the link contains the required markers (tag or category)
                        return str_starts_with($href, '/tag') || str_starts_with($href, '/category');
                    })->each(function (Crawler $tagNode) {
                        return trim($tagNode->text());
                    });

                    //Retrieving the author's name
                    $authorNode = $articleCrawler->filter('a[rel="author"]');
                    $author = $authorNode->count() ? trim($authorNode->text()) : 'Unknown';

                    $articleData['publication_date'] = $publicationDate;
                    $articleData['tags'] = array_unique(array_merge($articleData['tags'], $tags));
                    $articleData['author'] = $author; // Add the author
                } catch (\Exception $e) {
                    Log::error("Error when parsing an article {$articleData['link']}: " . $e->getMessage());
                }
            }

            // Save the data in the database, check for uniqueness and date
            foreach ($articles as $articleData) {
                // Check the date of publication (if the article is older than 4 months, skip it)
                $publicationDate = Carbon::parse($articleData['publication_date']);
                if ($publicationDate->isBefore(Carbon::now()->subMonths(1))) {
                    continue; // Skip an article if it is older than 4 months
                }

                // Update or create an article in the database
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

            // Increase the page number for the next request
            $page++;
        }
    }
}
*/
