<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ['title', 'author', 'tags', 'link', 'publication_date'];

    // Specify that the publication_date field should be automatically converted to a Carbon object
    protected $casts = [
        'publication_date' => 'datetime',
    ];
}

