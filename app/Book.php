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

    public static function getTransformer()
    {
        return new \App\Http\Transformer\BookTransformer;
    }

    public static function getIncludes()
    {
        return ['authors'];
    }

    public function bookshelf()
    {
        return $this->hasMany('App\Bookshelf', 'book_id');
    }

    public function authors()
    {
        return $this->belongsToMany('App\Author', 'book_authors', 'book_id', 'author_id');
    }
}
