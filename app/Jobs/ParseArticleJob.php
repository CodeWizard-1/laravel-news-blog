<?php


namespace App\Jobs;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use App\Models\Article;
use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParseArticleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $articleData;
    
    public function __construct($articleData)
    {
        $this->articleData = $articleData;
    }

    public function handle()
    {
        $client = new Client();
        $response = $client->get($this->articleData['link']);
        
        $html = (string) $response->getBody();
        $crawler = new Crawler($html);

        // Парсим дополнительные данные статьи
        $publicationDateNode = $crawler->filter('time');
        $publicationDate = $publicationDateNode->count()
            ? $publicationDateNode->attr('datetime')
            : now()->format('Y-m-d');

        $tags = $crawler->filter('div.flex.flex-wrap.items-center a')->each(function (Crawler $tagNode) {
            return trim($tagNode->text());
        });

        $authorNode = $crawler->filter('a[rel="author"]');
        $author = $authorNode->count() ? trim($authorNode->text()) : 'Unknown';

        $this->articleData['publication_date'] = $publicationDate;
        $this->articleData['tags'] = array_unique(array_merge($this->articleData['tags'], $tags));
        $this->articleData['author'] = $author;

        // Сохраняем статью в базу данных
        $this->saveArticle($this->articleData);
    }

    private function saveArticle($articleData)
    {
        if (empty($articleData['publication_date'])) {
            return;
        }

        $publicationDate = Carbon::parse($articleData['publication_date']);
        if ($publicationDate->isBefore(Carbon::now()->subMonths(1))) {
            return;
        }

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


