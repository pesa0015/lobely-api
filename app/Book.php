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
        return \App\Http\Transformer\BookTransformer::class;
    }

    public static function getBookshelfTransformer()
    {
        return \App\Http\Transformer\BookshelfTransformer::class;
    }

    public static function getIncludes()
    {
        return ['authors'];
    }

    public function bookshelf()
    {
        return $this->hasMany('App\Bookshelf', 'book_id');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'bookshelves', 'book_id', 'user_id')->withTimestamps();
    }

    public function authors()
    {
        return $this->belongsToMany('App\Author', 'book_authors', 'book_id', 'author_id')->withTimestamps();
    }
}
