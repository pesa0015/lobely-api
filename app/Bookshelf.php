<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bookshelf extends Model
{
    protected $fillable = [
        'user_id', 'book_id', 'comment'
    ];

    public static function getTransformer()
    {
        return new \App\Http\Transformer\BookshelfTransformer;
    }

    public static function getIncludes()
    {
        return ['book'];
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function book()
    {
        return $this->belongsTo('App\Book', 'book_id');
    }
}
