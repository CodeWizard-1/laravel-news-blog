<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;

class ClearArticles extends Command
{
    protected $signature = 'articles:clear';
    protected $description = 'Удаляет все записи из таблицы articles';

    public function handle()
    {
        Article::truncate();
        $this->info('Все записи успешно удалены из таблицы articles.');
    }
}