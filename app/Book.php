<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'official_title', 'author', 'publisher', 'published_year', 'pages', 'language', 'isbn_nr'
    ];
}
