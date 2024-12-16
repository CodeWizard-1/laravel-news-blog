<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ['title', 'author', 'tags', 'link', 'publication_date'];

    // Указываем, что поле publication_date должно быть автоматически преобразовано в объект Carbon
    // protected $dates = ['publication_date']; 
    protected $casts = [
        'publication_date' => 'datetime',
    ];
}

