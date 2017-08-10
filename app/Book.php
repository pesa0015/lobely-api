<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'original_title',
        'slug',
        'publisher',
        'published',
        'pages',
        'cover'
    ];

    public function bookshelf()
    {
        return $this->hasMany('App\Bookshelf', 'book_id');
    }
}
